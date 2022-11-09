<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("config.php");
include_once("headers.php");



header("Content-type: text/html; charset=UTF-8");

$logger = CslLogger::defaultLogger();

$logger->setLogMode('html');



$page_size = 10;

$levels = array(
	5 => array('name' => 'Мирянин', 'min' => 3, 'max' => 5, 'voc' => 'брате'),
	6 => array('name' => 'Свещеносец', 'exact' => 6, 'voc' => 'брате'),
	7 => array('name' => 'Чтец', 'exact' => 7, 'voc' => 'брате'),
	8 => array('name' => 'Иподиакон', 'exact' => 8, 'voc' => 'брате честный'),
	9 => array('name' => 'Диакон', 'exact' => 9, 'voc' => 'отче диаконе'),
	10 => array('name' => 'Протодиакон', 'exact' => 10, 'voc' => 'отче протодиаконе'),
	11 => array('name' => 'Архидиакон', 'exact' => 11, 'voc' => 'отче архидиаконе'),
	12 => array('name' => 'Иерей', 'exact' => 12, 'voc' => 'Ваше Преподобие'),
	13 => array('name' => 'Протоиерей', 'exact' => 13, 'voc' => 'Ваше Высокопреподобие'),
	14 => array('name' => 'Игумен', 'exact' => 14, 'voc' => 'Ваше Высокопреподобие'),
	15 => array('name' => 'Архимандрит', 'exact' => 15, 'voc' => 'Ваше Высокопреподобие'),
	16 => array('name' => 'Епископ', 'exact' => 16, 'voc' => 'Ваше Преосвященство'),
	17 => array('name' => 'Архиепископ', 'exact' => 17, 'voc' => 'Ваше Высокопреосвященство'),
	18 => array('name' => 'Митрополит', 'exact' => 18, 'voc' => 'Ваше Высокопреосвященство'),
	19 => array('name' => 'Патриарх', 'min' => 19, 'voc' => 'Ваше Святейшество')
);



function trainer_intro()
{
	echo '<p>Предлагаем потренировать Ваши способности чтеца церковных текстов. Вам будет предложено прочесть по 10 слов определённой длины, которые встречаются в церковнославянской Библии и богослужебных книгах. Постарайтесь произносить их чётко, озвучивая каждую букву, без запинки. </p>';
}


function trainer_level_select_form($levels)
{
	echo '<p>Выберите уровень сложности:</p>
		<form action="trainer.php">
			<select name="level" id="level">';
				
			foreach ($levels as $l=>$lev)
			{
				echo "<option value='$l'>{$lev['name']}</option>";
			}

		echo '	</select>
			<input type="submit" value="Начать">
		</form>';
}

function trainer_invitation($levels, $level)
{
	$obr = 	$levels[$level]['voc'];
	
	if ($level <= 8)
		echo "<p>Изволь прочести, $obr:</p>";
	else
		echo "<p>Извольте прочести, $obr:</p>";
}




function make_seed()
{
	list($usec, $sec) = explode(' ', microtime());
	return $sec + $usec * 1000000;
}

function page_randomizer($max_pages, $seed)
{
	srand($seed);
	$pages = range(0, $max_pages-1);
	shuffle($pages);

	return $pages;
}




function display_words($db, $exact, $min, $max, $page, $page_size, $seed)
{
	$total_words = $db->words_by_size(true, $exact, $min, $max, null, null);

	$max_pages = intval(ceil($total_words / $page_size));
	$page = $page % $max_pages;
	
	$randomizer = page_randomizer($max_pages, $seed);

	$page = $randomizer[$page];

	$words = $db->words_by_size(false, $exact, $min, $max, $page * $page_size, $page_size);

	// previous seed is actually constant while 'in-session', words on page need better shuffling
	srand( make_seed());

	$n_words = count($words);

	if ($n_words < $page_size)
	{
		// fill up less-than-full page with words from a more random page
		$another_page = $page;

		while ($another_page == $page)
		{
			$another_page = rand(0, $max_pages-1);
		}
		
		$more_words = $db->words_by_size(false, $exact, $min, $max, $another_page * $page_size, $page_size - $n_words);

		$words = array_merge($words, $more_words);
	}

	shuffle($words);

	echo '<table class="words"><thead><tr><td class="civ_h">Кириллица</td><td class="civ_h">Гражданский шрифт</td></thead>';

	foreach ($words as $w)
	{
		echo "<tr><td class='csl_t'>{$w['word']}</td><td class='civ_t'>{$w['simplified']}</td></tr>";
	}

	echo '</tr></table>';
}

function next_page_form($level, $page, $seed)
{
	echo '<form action="trainer.php" method="get">
			<input type="hidden" name="level" value="'.$level.'">
			<input type="hidden" name="seed" value="'.$seed.'">
			<input type="hidden" name="page" value="'.($page + 1).'">
			<input type="submit" value="Далее" class="next_button">
		</form>';
}


header("Content-type: text/html; charset=UTF-8");
?>
<html>
    <head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Тренажёр чтеца</title>
        <link rel="stylesheet" href="csl.css" />
		<style>
			table.words { border: 1px solid black;  border-collapse: collapse; margin-bottom: 15pt;}
			
			table.words thead tr td { height: 30pt; border: 1px solid black; padding: 4px;  }
			table.words tr td { border: 1px solid black; padding: 4px;  }

			.civ_h { font-weight: bold; font-size: 14pt; }
			.civ_t { font-size: 14pt; }
			.csl_t { font-size: 14pt; font-family: 'Ponomar Unicode'; }		

			.next_button { font-size: 12pt; margin-left: 150px; }
		</style>
    </head>
    <body>
		<h1>Тренажёр для православного чтеца</h1>
        <h3>Cложные церковнославянские слова</h3>
		
		
		<?php
            
            $db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, $logger );
            
            if ($db === false)
            {
                $logger->log(9, "Failed to connect to database!");
                $logger->fail_with_error_message("Failed to connect to database!" , '</body></html>');
            }
            
            
			if (empty($_REQUEST['level']))
			{
				trainer_intro();
				trainer_level_select_form($levels);
			}
			else
			{
				$level = intval($_REQUEST['level']);
				$page = empty($_REQUEST['page']) ? 0 : intval($_REQUEST['page']);

				if (empty($levels[$level]))
					$level = 5;

				trainer_invitation($levels, $level);

				$lev_data = $levels[$level];

				$min = empty($lev_data['min']) ? null : $lev_data['min'];
				$max= empty($lev_data['max']) ? null : $lev_data['max'];
				$exact = empty($lev_data['exact']) ? null : $lev_data['exact'];

				$seed = empty($_REQUEST['seed']) ? 	make_seed() : intval($_REQUEST['seed']); 			

				display_words($db, $exact, $min, $max, $page, $page_size, $seed );

				next_page_form( $level, $page, $seed );
			}
            
            
            $db->close();
        
            if (DEBUG_MODE == 1)
                $logger->printEntries();
	
?>
    </body>
</html>
