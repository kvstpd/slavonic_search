<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.
    
    
// Commands like "search" or "rebuild" can be specified both using Web query and command line
function is_command_specified($option)
{    
    return ( isset($_REQUEST[$option]) || getopt("", array($option)) );
}
    
    
?>
