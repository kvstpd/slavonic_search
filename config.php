<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
/*define('DB_TYPE', 'mysql');
define('DB_NAME', 'csl_search');
define('DB_SERVER', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASSWORD', 'whatever');*/

    
define('DB_TYPE', 'sqlite');
define('DB_NAME', 'sl_dict.db');
define('DB_NAME_SLAVONISER', 'slavoniser.db');
define('DB_SERVER', '');
define('DB_USER', '');
define('DB_PASSWORD', '');

define('ADMIN_MODE', '1');
define('DEBUG_MODE', '1');

define('SHOW_LOGGER_IMMEDIATELY', false);

define('SEARCH_ITEM_LIMIT', 100);
    
define('DEFAULT_SEARCH_WORDS_DISTANCE', 2);

?>
