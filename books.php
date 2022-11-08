<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("config.php");
include_once("headers.php");



if (ADMIN_MODE == 1)
	CslLogger::defaultLogger()->setLogImmediately(SHOW_LOGGER_IMMEDIATELY);

CslLogger::defaultLogger()->setLogMode('html');



$db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, CslLogger::defaultLogger() );






header("Content-type: text/html; charset=UTF-8");
?>
<html>
    <head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Библиотека</title>
        <link rel="stylesheet" href="csl.css" />
		<style>
			table.books { border: 1px solid black;  border-collapse: collapse; margin-bottom: 15pt;}
			
			table.books thead tr td { height: 30pt; border: 1px solid black; padding: 4px;  }
			table.books tr td { border: 1px solid black; padding: 4px;  }

			.civ_h { font-weight: bold; font-size: 14pt; }
			.civ_t { font-size: 14pt; }
			.csl_t { font-size: 14pt; font-family: 'Ponomar Unicode'; }		

			.fix_button { font-size: 12pt; margin-left: 150px; }
		</style>
    </head>
    <body>
		<h1>Библиотека книг на церковнославянском языке</h1>		
		
		<?php
			if ($db === false)
			{
				CslLogger::defaultLogger()-> CslLogger::defaultLogger()->fail_with_error_message("Unable to connect to database!");
				echo '<p></p></body></html>';
				exit();
			}

			if (!empty($_REQUEST['fix']) && $_REQUEST['fix'])
			{	
				$book_data = csl_default_book_data();

				foreach ($book_data as $path=>$fix)
				{
					$db->update_book_by_path($path, $db->escape_string($fix[0]), $db->escape_string($fix[1]) );
					//echo '<p>'.( ? '1' : '0').'</p>';
				}
			}


			$result = $db->get_books();

			//print_r($result);

			echo '<form action="books.php" method="get">
			<input type="hidden" name="fix" value="1">
			<input type="submit" value="Загрузить" class="fix_button">
			</form>';
			
			echo '<table class="books"><thead><tr><td class="civ_h">Файл</td><td class="civ_h">Название</td><td class="civ_h">Описание</td><td class="civ_h">Дополнительно</td></thead>';

            $book_types = csl_default_book_types();
            
			foreach ($result as $row)
			{
                $comment = ' ';
                
                if (isset($book_types[$row['type']]) )
                {
                    $comment = $book_types[$row['type']];
                }
                
				echo "<tr><td class='civ_t'>{$row['path']}</td><td class='civ_t'>{$row['name']}</td><td class='civ_t'>{$row['description']}</td><td class='civ_t'>$comment</td></tr>";
			}

			echo '</tr></table>';
			



	
?>
    </body>
</html>
