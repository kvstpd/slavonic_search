<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.

include_once("CslLogger.php");
include_once("CslDatabaseSqlite.php");
include_once("CslDatabaseMysql.php");



function csl_db_connect($type, $db_name, $server, $user, $password, $logger)
{
    try
    {
        if ($type == "mysql")
        {
            return new CslDatabaseMysql($server, $db_name, $user, $password, $logger);
        }
        else if ($type == "sqlite")
        {
            return new CslDatabaseSqlite($db_name, $logger);
        }
        else
        {
            $logger->log(9, "Unknown database type: $type");
            return false;
        }
    }
    catch (Exception $e) {
        $logger->log(9, "Error connecting to DB: " + $e->getMessage() );
    }
    
    return false;
}
    
?>
