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
        <title>Поиск в церковнославянских текстах</title>
        <link rel="stylesheet" href="csl.css" />
        <script src="search.js"></script>
    </head>
    <body>
	<h3>Поиск в церковнославянских текстах</h3>
<?php
    
    show_main_menu();
    
    
    if ((ADMIN_MODE != 1) || is_command_specified('search') )
    {
        $db = csl_db_connect(DB_TYPE, DB_NAME, DB_SERVER, DB_USER, DB_PASSWORD, $logger );
        
        show_word_search($db);
        
        $db->close();
    }
    
    
    if (DEBUG_MODE == 1)
        $logger->printEntries();
?>            
            
    </body>
</html>
