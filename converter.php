<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.

include_once("config.php");
include_once("headers.php");



header("Content-type: text/html; charset=UTF-8");

$logger = CslLogger::defaultLogger();

$logger->setLogMode('html');
    



    
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Преобразование кодировок церковнославянских текстов</title>
        <link rel="stylesheet" href="csl.css" />
    </head>
    <body>
    <h3>Преобразование кодировок церковнославянских текстов</h3>
    <?php
        show_main_menu();
        
        $convert_query = isset($_POST['text'])  ? $_POST['text'] : '';

        $convert_query = htmlspecialchars(trim($convert_query));

        echo '<form id="csl_convert_form" action="converter.php" method="post">';
        
        echo '<textarea id="csl_convert_box" name="text" autofocus="true" cols="80" rows="12" form="csl_convert_form" maxlength="512000" placeholder="Текст для преобразования" required="true" >';
        echo $convert_query;
        echo '</textarea>';
    
        
        show_convert_params();
        
        echo '<input type="submit" name="do_convert" value="Преобразовать"><br/><br/>';
 
        

        if (isset($_POST['do_convert']))
        {
            $text_class = 'slv_unicode';
            $input_is_ucs = false;
            
            if (isset($_POST['query_encoding']) && ($_POST['query_encoding'] == 'q_ucs' ) )
            {
                $input_is_ucs = true;
            }
            
            if (isset($_POST['result_encoding']) )
            {
                if ( ($_POST['result_encoding'] == 'r_ucs') )
                {
                    $text_class = 'slv_ucs';
                }
                else if ( ($_POST['result_encoding'] == 'r_civil') )
                {
                    $text_class = 'slv_civil';
                }
            }
            
            
            if ($input_is_ucs)
            {
                if ($text_class == 'slv_unicode')
                {
                    $convert_query = csl_ucs_to_unicode($convert_query);
                }
            }
            else
            {
                if ($text_class == 'slv_ucs')
                {
                    $convert_query = csl_unicode_to_ucs($convert_query);
                }
            }
            

            echo "<div class='$text_class'>";
        
            
            echo nl2br($convert_query);
            
            echo '</div>';
            /*$query = $db->escape_string($_POST['search']);
            $encoding = empty($_POST['query_encoding']) ? 'simplified' : $_POST['query_encoding'] ;
            $search_simplified = false;
            
            switch($encoding)
            {
            case 'unicode':
                    $query = csl_normalise($query);
                    break;
            case 'ucs':
                    $query = csl_normalise(csl_ucs_to_unicode($query));
                    break;
            default:
                    $query = csl_simplify_civil($query);
                    $search_simplified = true;
                    break;
            }*/
        
            
        }
        

        echo '</form>';
        
        
        if (DEBUG_MODE == 1)
            $logger->printEntries();
    ?>
    </body>
</html>
