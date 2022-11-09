<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
include_once("config.php");
include_once("headers.php");

    
$context_size = 2048;

    

if (DEBUG_MODE == 1)
	CslLogger::defaultLogger()->setLogImmediately(SHOW_LOGGER_IMMEDIATELY);

CslLogger::defaultLogger()->setLogMode('json');

    
header("Content-type: application/json; charset=utf-8");

$db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, CslLogger::defaultLogger() );

    

if ($db === false)
{
    CslLogger::defaultLogger()->fail_with_error_message("Failed to connect to database");
}
    
if (empty($_REQUEST['occurence_id']) )
{
    CslLogger::defaultLogger()->fail_with_error_message("No occurence parameter passed");
}

$encoding = empty($_REQUEST['encoding']) ? 'unicode' : $_REQUEST['encoding'] ;

$occurence_id = intval($_REQUEST['occurence_id']);
    
$result = $db->get_occurence($occurence_id);

    
$file = open_handle_for_book_path($result['path']);


if (!$file)
{
    CslLogger::defaultLogger()->fail_with_error_message("Cannot read book file!");
}
    
$abs_position =  $result['position'];
$abs_length = $result['length'];

$local_position = $context_size/2;
$start = $abs_position - $local_position;


if ($start <= 0)
{
    $start = 0;
    $local_position = $abs_position;
}
else
{
    fseek($file, $start);
}

$context = fread($file, $context_size);

fclose($file);

// Dont forget about й variations!

$first_space = 0;

if ($start > 0)
{
	$first_space = strpos($context, ' ') + 1;

	if ($first_space > $local_position)
    	$first_space = $local_position;
}

$last_space = strrpos($context, ' ');
    

$context = substr($context, $first_space, $last_space - $first_space );

/*$word_len = strlen($result['word']);

// actual word length may differ because of й variations, fix it in that case
while ( !csl_is_separator( substr($context, $local_position - $first_space + $word_len, 1 )) )
{
    $word_len++;
}*/
    
//$context = substr_replace($context, '<span style="color:red;">'.$result['word'].'</span>', $local_position - $first_space,  $word_len);

$orig_word = substr($context, $local_position - $first_space,  $abs_length);
$context = substr_replace($context, '<span style="color:red;">'.$orig_word.'</span>', $local_position - $first_space,  $abs_length);


$context = csl_strip_toc_markup($context);
    
if ($encoding == 'ucs')
	$context = csl_unicode_to_ucs($context);
else
	$context = csl_normalise($context, false);

//$byte_array = unpack('C*', $orig_word);
//implode('-', $byte_array);

    
$context =  nl2br($context);//;.' '.ord($orig_word[1]);
//strtr($context, array("\n" => '<br/>'));
    
    
if ($result === false)
{
    CslLogger::defaultLogger()->fail_with_error_message("Search for '$query' failed");
}
else
{
    echo '{ "success" : true';
    echo ' , "result" : ['.json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo '] , "logEntries" : [';
    CslLogger::defaultLogger()->log(0, 'ended here');
    CslLogger::defaultLogger()->printEntries();
    echo ' ] }';
}
    
exit(0);
?>
