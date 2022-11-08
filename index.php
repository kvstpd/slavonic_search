<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.


include_once("config.php");
include_once("headers.php");
    

    
// Search several words separated by  ' ' : need options "ANY",  "ALL" (+ changeable distance, abs() defaults to 1000 chars), "EXACT" (distance is <4-5-6 and without abs() )


if (ADMIN_MODE == 1)
{
	set_time_limit(0);
	CslLogger::defaultLogger()->setLogImmediately(SHOW_LOGGER_IMMEDIATELY);
}


CslLogger::defaultLogger()->setLogMode('html');

    
    
function make_dict($db, $file_path, $file_contents)
{
	if (ADMIN_MODE != 1)
		die ("Unable to!");

    $word_map = csl_make_simplified_word_map($file_contents);
    
    unset ($file_contents);

    return $db->insert_words($word_map, $db->escape_string($file_path));
}
    
    
function populate_slavoniser($dir)
{
    if (ADMIN_MODE != 1)
        die ("Unable to!");
    
    $db2 = csl_db_connect(DB_TYPE, DB_NAME_SLAVONISER, DB_SERVER, DB_USER, DB_PASSWORD,  CslLogger::defaultLogger() );
    
    if ($db2 === false)
    {
        CslLogger::defaultLogger()->log(9, "Failed to connect to database!");
        return false;
    }
    
    $db2->rebuild_slavoniser_structure();
    
    
    $extension = ".txt";
    
    
    if ($dh = @opendir($dir))
    {
        $word_map = array();
        
        while (false !== ($fileName = @readdir ($dh)))
        {
            if (substr($fileName, -4) == $extension)
            {
                if ( ($fileName != "Triodʹ Cvetnaya.txt") &&  ($fileName != "StJamesLiturgyBulg.txt") )
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
        
        echo "Words: ".count($word_map);
        //print_r($word_map);
    }
    else
    {
        CslLogger::defaultLogger()->log(1, "Unable access directory: $dir");
        return false;
    }
    
    $db2->close();
    
    
    //CslLogger::defaultLogger()->printEntries();
    
}

    
function populate($db, $dir)
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
                if (make_dict($db, $fileName, file_get_contents($dir.$fileName)) )
                {
                    $word_count = $db->get_word_count();
                    
                    CslLogger::defaultLogger()->log(0, "Processed $fileName. Total words in database now: $word_count");
                    $at_least_partial_success = true;
                }
                else
                {
                    CslLogger::defaultLogger()->log(1, "Failed to process file: $fileName");
                }
            }
        }
        
        closedir($dh);
                    
        return $at_least_partial_success;
    }
    else
    {
        CslLogger::defaultLogger()->log(1, "Unable access directory: $dir");
        return false;
    }
}
    
    
function read_books()
{
    $rebuild = (isset($_REQUEST['populate']) || isset($_REQUEST['rebuild']) ) && (ADMIN_MODE == 1);


    $db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD,  CslLogger::defaultLogger() );
    
    if ($db === false)
    {
        CslLogger::defaultLogger()->log(9, "Failed to connect to database!");
        return false;
    }
    
    if ( ($rebuild || !$db->is_structure_in_place() )  && (ADMIN_MODE == 1) )
    {
        $db->rebuild_structure();
    }
    
    //get_db(isset($_GET['rebuild']));
    
    if (isset($_REQUEST['populate']) && (ADMIN_MODE == 1) )
    {
        populate($db,  "./books/");
    }
    else if (isset($_REQUEST['slavoniser']) && (ADMIN_MODE == 1) )
    {
        populate_slavoniser("./books/");
    }
    else if (isset($_REQUEST['search']) || (ADMIN_MODE == 0))
    {
      //echo '<form action="search_json.php">';
        echo '<input id="csl_search_box" type="search" placeholder="Поиск" autocomplete="off" spellcheck="false"/><button type="button" id="csl_search_button">Искать</button><br/><br/>';
        echo '<div id="csl_search_head">';
        
        echo '<div id="csl_query_params">';
        echo '<fieldset>';
        echo '<legend>Критерий поиска:</legend>';
        echo '<input type="radio" class="csl_search_param" id="contains" name="match_type" value="contains" checked="checked"><label for="contains">Содержит</label>';
		echo '<input type="radio" class="csl_search_param" id="begins" name="match_type" value="begins"><label for="begins">Начинается с</label>';
		echo '<input type="radio" class="csl_search_param" id="ends" name="match_type" value="ends"><label for="ends">Кончается на</label>';
		echo '<input type="radio" class="csl_search_param" id="exact" name="match_type" value="exact"><label for="exact">Точное совпадение</label>';
        
        echo '<div id="csl_multi_words">';
        echo '<input type="radio" class="csl_search_param" id="multi_and_rigid" name="multi_words" value="multi_and_rigid" checked="checked"><label for="multi_and_rigid">Все слова в указанном порядке</label>';
        echo '<input type="radio" class="csl_search_param" id="multi_and_free" name="multi_words" value="multi_and_free"><label for="multi_and_free">Все слова в любом порядке</label>';
        echo '<input type="radio" class="csl_search_param" id="multi_or" name="multi_words" value="multi_or"><label for="multi_or">Любое из слов</label><br/>';
        echo '<label for="csl_word_distance">Расстояние между словами (приблизительно символов): </label><input id="csl_word_distance" type="number" autocomplete="off" spellcheck="false" value="2" min="1" max="500"/>';
        echo '</div>';
        
        echo '</fieldset>';
        

        
        echo '</div>';
        
        echo '<div id="csl_encoding_params">';
        echo '<fieldset>';
        echo '<legend>Кодировка запроса:</legend>';
        echo '<input type="radio" class="csl_search_param" id="simplified" name="encoding" value="simplified" checked="checked"><label for="simplified">Упрощённая гражданская</label>';
        echo '<input type="radio" class="csl_search_param" id="unicode" name="encoding" value="unicode"><label for="unicode">Unicode</label>';
        echo '<input type="radio" class="csl_search_param" id="ucs" name="encoding" value="ucs"><label for="ucs">UCS (как на orthlib.ru)</label>';
        echo '</fieldset>';
        
        echo '<fieldset>';
        echo '<legend>Кодировка результата:</legend>';
        echo '<input type="radio" class="csl_output_param" id="result_unicode" name="result_encoding" value="unicode" checked="checked"><label for="result_unicode">Unicode</label>';
		echo '<input type="radio" class="csl_output_param" id="result_ucs" name="result_encoding" value="ucs"><label for="result_ucs">UCS (как на orthlib.ru)</label>';
        echo '</fieldset>';
        echo '</div>';
        
        echo '<div id="csl_books">';
        echo '<fieldset>';
        echo '<legend>Книги для поиска:</legend>';
        echo '<input type="radio" class="csl_book_select" id="book_type_common" name="book_type" value="csl_ob_book" checked="checked"><label for="book_type_common">Общеупотребимые</label>';
        echo '<input type="radio" class="csl_book_select" id="book_type_bible" name="book_type" value="csl_elis_book"><label for="book_type_bible">Елизаветинская Библия</label>';
        echo '<input type="radio" class="csl_book_select" id="book_type_present" name="book_type" value="csl_pr_book"><label for="book_type_present">Богослужебные книги</label><br/>';
        echo '<input type="radio" class="csl_book_select" id="book_type_f" name="book_type" value="csl_f_book"><label for="book_type_f">Святоотеческая литература</label>';
        echo '<input type="radio" class="csl_book_select" id="book_type_n" name="book_type" value="csl_n_book"><label for="book_type_n">Неполная диакритика</label>';
        echo '<input type="radio" class="csl_book_select" id="book_type_old" name="book_type" value="csl_old_book"><label for="book_type_old">Дореформенные</label>';
        echo '<input type="radio" class="csl_book_select" id="book_type_all" name="book_type" value="csl_book"><label for="book_type_all">Все</label><br/>';
        echo '<input type="radio" class="csl_book_select" id="book_type_choose" name="book_type" value="__choose"><label for="book_type_choose">Выбрать</label>';
        

        $books = $db->get_books();

        echo '<div id="csl_book_list">';
        echo '<fieldset id="csl_book_checkboxes" disabled="disabled">';

		if (!empty($books) && is_array($books) )
		{
			$old_checkbox_class = 'csl_search_param';
			
			foreach ($books as $book)
			{
				$book_id = $book['book_id'];
				$checkbox_id = 'book_'.$book_id;
				$checkbox_class = 'csl_search_param csl_book';
				
				if ($book['type'] == 'o')
					$checkbox_class .= ' csl_old_book';
				else if ($book['type'] == 'n')
					$checkbox_class .= ' csl_n_book';
				else if ($book['type'] == 'f')
					$checkbox_class .= ' csl_f_book csl_ob_book';
				else if ($book['type'] == 'b')
					$checkbox_class .= ' csl_elis_book csl_ob_book';
				else
					$checkbox_class .= ' csl_pr_book csl_ob_book';
				
				if ($old_checkbox_class != $checkbox_class)
				{
					echo '<hr/>';
					$old_checkbox_class = $checkbox_class;
				}
				
				echo "<nobr><input type='checkbox' class='$checkbox_class' id='$checkbox_id' name='book_id' value='$book_id'><label for='$checkbox_id'>{$book['name']}</label></nobr> ";
			}
		}

        echo '</fieldset>';
        echo '</div>';
        
        echo '</fieldset>';
        echo '</div>';
        

        
        //echo '</form>';
        echo '<div class="csl_result_container">';
            echo '<div class="csl_result_block" id="csl_search_result"></div>';
            echo '<div class="csl_result_block" id="csl_occurences"></div>';
            echo '<div class="csl_result_block" id="csl_context"></div>';
        echo '</div>';
    }
    
    
    //CslLogger::defaultLogger()->setLogImmediately(true);
    //csl_db_search_string($db, 'испров');
    
    $db->close();
    
    
    CslLogger::defaultLogger()->printEntries();
}
    
    

    
    
    
header("Content-type: text/html; charset=UTF-8");




?>
<html>
    <head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Поиск в церковнославянских текстах</title>
        <link rel="stylesheet" href="csl.css" />
        <script src="search.js"></script>
    </head>
    <body>
	<h3>Поиск в церковнославянских текстах</h3>
<?php
		if  (ADMIN_MODE == 1)
        	echo '<p>MENU: <a href="index.php">HOME</a> <a href="index.php?rebuild">Rebuild</a> <a href="index.php?populate">Populate</a> <a href="index.php?slavoniser">Make Slavoniser DB</a> <a href="converter.php">Encoding converter</a> <a href="index.php?search">Search</a></p>';
    
         read_books( ); 

?>
            
            
    </body>
</html>
