<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.

    
include_once("config.php");
include_once("headers.php");

    
// Search several words separated by  ' ' : need options "ANY",  "ALL" (+ changeable distance, abs() defaults to 1000 chars), "EXACT" (distance is <4-5-6 and without abs() )

    

header("Content-type: text/html; charset=UTF-8");

$logger = CslLogger::defaultLogger();

$logger->setLogMode('html');
    




function show_query_params()
{
		$match_types = array('contains' => 'Содержит', 'begins' => 'Начинается с', 'ends' => 'Кончается на', 'exact' => 'Точно совпадает');
		$multi_word_types = array('and_rigid' => 'Все слова в указанном порядке', 'and_free' => 'Все слова в любом порядке', 'or' => 'Любое из слов' );


       	echo '<div id="csl_query_params">';
        echo '<fieldset>';
        echo '<legend>Искать слово, которое:</legend>';
		make_radio_group('csl_search_param', 'match_type', $match_types, 'contains');
		echo '</fieldset>';		
		
        echo '<fieldset>';
        echo '<legend>Поиск нескольких слов:</legend>';
		make_radio_group('csl_search_param', 'multi_words', $multi_word_types, 'and_rigid');
		echo '<br/>';
		make_integer_field('csl_search_param', 'word_distance', 'Максимальное расстояние между словами (приблизительно символов):', 2, 1, 500);
		echo '</fieldset>';		
        
       	echo '</div>';
}

function show_encoding_params()
{
		$query_encodings = array('q_simplified' => 'Упрощённая гражданская', 'q_unicode' => 'Unicode', 'q_ucs' => 'UCS (как на orthlib.ru)');
		$result_encodings = array('r_unicode' => 'Unicode', 'r_ucs' => 'UCS (как на orthlib.ru)');

        echo '<div id="csl_encoding_params">';
        echo '<fieldset>';
        echo '<legend>Кодировка запроса:</legend>';
		make_radio_group('csl_search_param', 'query_encoding', $query_encodings, 'q_simplified');
        echo '</fieldset>';
       
        echo '<fieldset>';
        echo '<legend>Кодировка результата:</legend>';
		make_radio_group('csl_search_param', 'result_encoding', $result_encodings, 'r_ucs');
        echo '</fieldset>';
        echo '</div>';
}



function show_book_selection($db)
{
		echo '<div id="csl_book_selection">';

		if (isset($_POST['hide_books']) || !isset($_POST['show_books']) )
		{
			echo '<fieldset><legend>Искать в книгах: <input type="submit" name="show_books" value="-развернуть-"></legend>';
			//echo '<input type="hidden" name="hide_books" value="1">';
			
			$selected_book_ids = empty($_POST['book_ids']) ? $db->get_book_ids_of_types(array('b','c','f')) : $_POST['book_ids'];

			foreach ($selected_book_ids as $book_id)
			{
				echo '<input type="hidden" name="book_ids[]" value="'.$book_id.'">';
			}

			$n_books = count($selected_book_ids);
			$last_digit = $n_books % 10;

			//check for 'teens
			if (intval(($n_books % 100) / 10) == 1)
				$last_digit = 0;

			echo '(Выбрано: '.$n_books;

			switch ($last_digit)
			{
			case 1:
				echo ' книга)';	
				break;

			case 2:
			case 3:
			case 4:
				echo ' книги)';	
				break;

			default:
				echo ' книг)';	
				break;
			}

			echo '</fieldset>';
			echo '</div>';
			return;
		}

		$books = $db->get_books();

		$book_types = array(
			'common' => array('name' => 'Наиболее употребимые', 'db_types' => array('b','c','f')), 
			'bible' => array('name' => 'Елизаветинская Библия', 'db_types' => array('b')), 
			'lithurgic' => array('name' => 'Богослужебные', 'db_types' => array('c')), 
			'fathers' => array('name' => 'Святоотеческая литература', 'db_types' => array('f')), 
			'bulgarian' => array('name' => 'Совр. болгарское издание', 'db_types' => array('n')), 
			'old' => array('name' => 'Дониконовская орфография', 'db_types' => array('o')), 
			'all' => array('name' => 'Все', 'db_types' => array('b','c','f','o','n')), 
		);

		$selected_book_ids = false;

		//print_r($selected_book_ids);

        
        echo '<fieldset>';
		echo '<legend>Искать в книгах: <input type="submit" name="hide_books" value="-свернуть-"></legend>';

		echo '<input type="hidden" name="show_books" value="1">';

		foreach ($book_types as $param => $book_type)
		{
			$param_name = 'select_books_'.$param;

			echo "<input class='csl_search_param' type='submit' id='$param_name' name='$param_name' value='{$book_type["name"]}'>";

			if (!empty($_POST[$param_name]) )
				$selected_book_ids = $db->get_book_ids_of_types($book_type['db_types']);
		}

		if (!$selected_book_ids)
		{
			$selected_book_ids = empty($_POST['book_ids']) ? $db->get_book_ids_of_types(array('b','c','f')) : $_POST['book_ids'];
		}


		/*echo '<input class="csl_search_param" type="submit" name="select_books_common" value="Общеупотребимые">';
		echo '<input class="csl_search_param" type="submit" name="select_books_bible" value="Елизаветинская Библия">';
		echo '<input class="csl_search_param" type="submit" name="select_books_lithurgic" value="Богослужебные">';
		echo '<input class="csl_search_param" type="submit" name="select_books_fathers" value="Святоотеческая литература">';
		echo '<input class="csl_search_param" type="submit" name="select_books_bulgarian" value="Совр. болгарское издание">';
		echo '<input class="csl_search_param" type="submit" name="select_books_old" value="Дореформенные">';
		echo '<input class="csl_search_param" type="submit" name="select_books_all" value="Все">';*/
		echo '<br/>';



        if (!empty($books) && is_array($books) )
        {
            $old_checkbox_type='';
            
            foreach ($books as $book)
            {
                $book_id = $book['book_id'];
                $checkbox_id = 'book_'.$book_id;

				$checked = in_array($book_id, $selected_book_ids) ? "checked='checked'" : '';
                
                if ($old_checkbox_type != $book['type'])
                {
                   	echo '<hr/>';

                   $old_checkbox_type = $book['type'];
                }
                
                echo "<nobr><input type='checkbox' id='$checkbox_id' name='book_ids[]' value='$book_id' $checked><label for='$checkbox_id'>{$book['name']}</label></nobr> ";
            }
        }

        echo '</fieldset>';
        echo '</div>';
}


function show_and_get_results_per_page()
{
	$allowed_results_per_page = array(10, 20, 50);
	
	$rpp = $allowed_results_per_page[0];

	if (isset($_POST['results_per_page']) && in_array(intval($_POST['results_per_page']), $allowed_results_per_page) )
		$rpp = intval($_POST['results_per_page']);

	echo '<label for="results_per_page">Результатов на странице:</label>';
	echo '<select name="results_per_page" id="results_per_page">';
	
	foreach ($allowed_results_per_page as $rpp_option)
	{
		$selected = ($rpp_option == $rpp) ? "selected='selected'" : '';
		echo "<option value='$rpp_option' $selected>$rpp_option</option>";
	}

	echo '</select>';

	return $rpp;
}

    
    ?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Поиск в церковнославянских текстах</title>
        <link rel="stylesheet" href="csl.css" />
    </head>
    <body>
    <h3>Поиск в церковнославянских текстах</h3>
    <?php
        
        $db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, $logger );
        
        if ($db === false)
        {
            $logger->log(9, "Failed to connect to database!");
            $logger->fail_with_error_message("Failed to connect to database!" , '</body></html>');
        }
        
        
		$search_query = isset($_POST['search'])  ? $_POST['search'] : '';

		$search_query = htmlspecialchars(trim($search_query));

      	echo '<form action="search_ext.php" method="post">';
        echo '<input id="csl_search_box" name="search" type="search" placeholder="Поиск" autocomplete="off" value="'.$search_query.'" spellcheck="false"/>';
		echo '<input type="submit" name="do_search" value="Искать"><br/><br/>';
 

		show_query_params();

		show_encoding_params();

		show_book_selection($db);

		$rpp = show_and_get_results_per_page();


        
        if (isset($_POST['do_search']))
        {
            $query = $db->escape_string($_POST['search']);
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
            }
            
            $book_ids = (!empty($_POST['book_ids']) && is_array($_POST['book_ids'])) ? $_POST['book_ids'] : false;
            
            //  precaution against SQL injection
            if ($book_ids)
                $book_ids = array_map('intval', $book_ids);
            
            $match_type = empty($_REQUEST['match_type']) ? 'contains' : $_REQUEST['match_type'];
            
            $result =  false;

            //print_r($book_ids);

            if ($result === false)
                $logger->log(1, "Search for '$query' failed");
            
        }
    

		echo '</form>';
        
        
        $db->close();
    
        if (DEBUG_MODE == 1)
            $logger->printEntries();
        
    ?>
    </body>
</html>
