<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.


function make_dict($db, $file_path, $file_contents, $logger)
{
    if (ADMIN_MODE != 1)
    {
        $logger->log(9, "Not allowed without Admin mode!");
        return false;
    }
        
    $word_map = csl_make_simplified_word_map($file_contents);
    
    unset ($file_contents);

    return $db->insert_words($word_map, $db->escape_string($file_path));
}
    
    
function populate_slavoniser_db($dir, $logger)
{
    if (ADMIN_MODE != 1)
    {
        $logger->log(9, "Not allowed without Admin mode!");
        return false;
    }
    
    $db2 = csl_db_connect(DB_TYPE, DB_NAME_SLAVONISER, DB_SERVER, DB_USER, DB_PASSWORD, $logger );
    
    if ($db2 === false)
    {
        $logger->log(9, "Failed to connect to database!");
        return false;
    }
    
    $success = false;
    
    $db2->rebuild_slavoniser_structure();
    
    $extension = ".txt";
    
    if ($dh = @opendir($dir))
    {
        $word_map = array();
        
        while (false !== ($fileName = @readdir ($dh)))
        {
            if (substr($fileName, -4) == $extension)
            {
                if ( ($fileName != "TriodCvetnaya.txt") &&  ($fileName != "StJamesLiturgyBulg.txt") )
                {
                    $file_contents = file_get_contents($dir.$fileName);
        
                    csl_make_slavoniser_word_map($file_contents, $word_map);
                    unset ($file_contents);
                    //break;
                }
            }
        }
        
        closedir($dh);
        
        $db2->insert_words_for_slavoniser($word_map);
        
        echo '<p>Words: '.count($word_map).'</p>';
        
        $success = true;
    }
    else
    {
        $logger->log(9, "Unable access directory: $dir");
    }
    
    $db2->close();
    
    return $success;
}

    
function populate_search_db($db, $dir, $logger)
{
    if (ADMIN_MODE != 1)
        die ("Unable to!");

    $at_least_partial_success = false;
    $extension = ".txt";
    
    
    if ($dh = @opendir($dir))
    {
        while (false !== ($fileName = @readdir ($dh)))
        {
            if (substr($fileName, -4) == $extension)
            {
                if (make_dict($db, $fileName, file_get_contents($dir.$fileName), $logger) )
                {
                    $word_count = $db->get_word_count();
                    
                    $logger->log(0, "Processed $fileName. Total words in database now: $word_count");
                    $at_least_partial_success = true;
                }
                else
                {
                    $logger->log(1, "Failed to process file: $fileName");
                }
            }
        }
        
        closedir($dh);
                    
        return $at_least_partial_success;
    }
    else
    {
        $logger->log(9, "Unable access directory: $dir");
        return false;
    }
}
    
    
  
    
    
?>
