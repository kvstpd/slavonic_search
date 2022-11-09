<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("config.php");
include_once("headers.php");



$context_size = 65536;



if (DEBUG_MODE == 1)
	CslLogger::defaultLogger()->setLogImmediately(SHOW_LOGGER_IMMEDIATELY);

CslLogger::defaultLogger()->setLogMode('json');

    
header("Content-type: application/json; charset=utf-8");


$db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, CslLogger::defaultLogger() );

    

if ($db === false)
{
    CslLogger::defaultLogger()->fail_with_error_message("Failed to connect to database");
}
    
if (empty($_REQUEST['word_id']) )
{
    CslLogger::defaultLogger()->fail_with_error_message("No 'word_id' parameter passed");
}
    
$word_id = intval($_REQUEST['word_id']);
    
$books_ids = isset($_REQUEST['book_ids']) ? $db->escape_string($_REQUEST['book_ids']) : false;

    
$result = $db->word_occurences($word_id, SEARCH_ITEM_LIMIT, $books_ids);

    
if ($result === false)
{
    CslLogger::defaultLogger()->fail_with_error_message("Searching for occurences of word_id $word_id failed");
}
else
{
	// SELECT occurence_id, name as book, path as path, position FROM occurences IN

	$book_path = '';
	$handle = null;

	$to_json = array();


	foreach ($result as $row)
	{
		if ($row['path'] != $book_path)
		{
			$book_path = $row['path'];

			if ($handle)
				fclose($handle);

			$handle = open_handle_for_book_path($book_path);
		}
		
		$context = get_backward_context_for_position($handle, $row['position'], $context_size);

		$vcp = get_last_verse_chapter_page($context);

		unset($context);
		

		if (isset($vcp['c']))
		{
			$row['ref'] = $row['book'] . ', ' . $vcp['c'];

			if (isset($vcp['v']))
				$row['ref'] .= ':'.$vcp['v'];
		}
		else if (isset($vcp['n']))
		{
			$row['ref'] = $row['book']; // . ', ' . $vcp['n'] . csl_book_toc($book_path);
			$toc = csl_book_toc($book_path);
			if ($toc && isset($toc[ ($vcp['n'] - 1) ] ) ) // Obi Wan here
				$row['ref'] .= ', ' .$toc[ ($vcp['n'] - 1) ];
		}
		else
		{
			$row['ref'] =  $row['book'] . ', ';

			if (isset($vcp['p']))
				$row['ref'] .= $vcp['p'];
		}


		unset($row['path']);
		unset($row['book']);

		//$row['ссс'] = $context;

		$to_json[] = $row;
	}

	if ($handle)
		fclose($handle);

	
	//print_r($to_json);


    echo '{ "success" : true, "result" : ';
    echo json_encode($to_json, JSON_UNESCAPED_UNICODE);
    
    echo ' , "logEntries" : [';
    CslLogger::defaultLogger()->log(0, 'ended here '.json_last_error_msg());
    CslLogger::defaultLogger()->printEntries();
    echo ' ] }';
}
    
exit(0);
?>

