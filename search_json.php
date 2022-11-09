<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("config.php");
include_once("headers.php");



    
if (DEBUG_MODE == 1)
	CslLogger::defaultLogger()->setLogImmediately(SHOW_LOGGER_IMMEDIATELY);

CslLogger::defaultLogger()->setLogMode('json');

    
header("Content-type: application/json; charset=utf-8");


$db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, CslLogger::defaultLogger() );

    

if ($db === false)
{
    CslLogger::defaultLogger()->fail_with_error_message("Failed to connect to database");
}
    
if (empty($_REQUEST['search']) )
{
    CslLogger::defaultLogger()->fail_with_error_message("No 'search' parameter passed");
}
    
$query = $db->escape_string($_REQUEST['search']);
$books_ids = isset($_REQUEST['book_ids']) ? $db->escape_string($_REQUEST['book_ids']) : false;
$encoding = empty($_REQUEST['encoding']) ? 'simplified' : $_REQUEST['encoding'] ;
    
$match_types = array('contains' => 1, 'begins'=> 1, 'ends'=> 1, 'exact'=> 1);
$match_type = empty($_REQUEST['match_type']) ? 'contains' : $_REQUEST['match_type'] ;
if (!isset($match_types[$match_type]))
    $match_type = 'contains';

$multi_word_types = array('none' => 1, 'multi_and_rigid'=> 1, 'multi_and_free'=> 1, 'multi_or'=> 1);
$multi_word_type = empty($_REQUEST['multi_type']) ? 'none' : $_REQUEST['multi_type'] ;
if (!isset($multi_word_types[$multi_word_type]))
    $multi_word_type = 'none';

$multi_word_distance = empty($_REQUEST['multi_distance']) ? DEFAULT_SEARCH_WORDS_DISTANCE : intval($_REQUEST['multi_distance']) ;

if ($multi_word_distance <= 0)
    $multi_word_distance = DEFAULT_SEARCH_WORDS_DISTANCE;


//echo '----'.$match_type.'----';

$result =  false;

if ($encoding == 'unicode')
{
	$result = $db->search_string(csl_normalise($query), $match_type, SEARCH_ITEM_LIMIT, false, $books_ids);
}
else if ($encoding == 'ucs')
{
    //echo csl_ucs_to_unicode($query);
	$result = $db->search_string(csl_normalise(csl_ucs_to_unicode($query)), $match_type, SEARCH_ITEM_LIMIT, false, $books_ids);
}
else //simplified is the default
{
	$result = $db->search_string(csl_simplify_civil($query), $match_type, SEARCH_ITEM_LIMIT, true, $books_ids);
}

    
if ($result === false)
{
    CslLogger::defaultLogger()->fail_with_error_message("Search for '$query' failed");
}
else
{
    echo '{ "success" : true, "result" : ';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    
    echo ' , "logEntries" : [';
    CslLogger::defaultLogger()->log(0, 'ended here');
    CslLogger::defaultLogger()->printEntries();
    echo ' ] }';
}
    
exit(0);
?>
