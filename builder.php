<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.

    
include_once("config.php");
include_once("headers.php");
include_once("includes/admin.php");

    
header("Content-type: text/html; charset=UTF-8");
    

$logger =  CslLogger::defaultLogger();
    
$logger->setLogMode('html');
$logger->setLogImmediately(SHOW_LOGGER_IMMEDIATELY);

if (ADMIN_MODE != 1)
    $logger->fail_with_error_message("Must be in Admin mode to create or modify databases!");


set_time_limit(0);

    

?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Поиск в церковнославянских текстах</title>
        <link rel="stylesheet" href="csl.css" />
    </head>
    <body>
    <h3>Управление базами данных поиска</h3>
<?php
    
    show_main_menu();
    
    
    if (is_command_specified('slavoniser') )
    {
        populate_slavoniser_db("./books/", $logger);
    }
    else
    {
        $db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, $logger);
        
        if ($db === false)
        {
            $logger->log(9, "Failed to connect to database!");
            $logger->fail_with_error_message("Failed to connect to database!" , '</body></html>');
        }
        
        if (is_command_specified('rebuild') )
            $db->rebuild_structure();
        
        if (is_command_specified('populate') )
        {
            if (!$db->is_structure_in_place())
                $db->rebuild_structure();
            
            populate_search_db($db, "./books/", $logger);
        }
        
        $db->close();
    }
    
    if (DEBUG_MODE == 1)
        $logger->printEntries();
?>            
    </body>
</html>
