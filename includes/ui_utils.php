<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.

function make_radio_group($css_class, $param_name, $allowed_options, $default_option)
{
    $selected_option = $default_option;

    if (isset($_POST[$param_name]) && isset($allowed_options[$_POST[$param_name]]) )
        $selected_option = $_POST[$param_name];
    
    foreach ($allowed_options as $option => $name)
    {
        $checked = ($selected_option == $option) ? "checked='checked'" : '';
        echo "<input type='radio' class='$css_class' id='$param_name-$option' name='$param_name' value='$option' $checked><label for='$param_name-$option'>$name</label>";
    }
}

function make_integer_field($css_class, $name, $name_shown, $default_value, $min, $max)
{
        $current_value = isset($_POST[$name]) ? intval($_POST[$name]) : $default_value;
        if ($current_value < $min)
            $current_value = $min;
        if ($current_value > $max)
            $current_value = $max;
        //
        echo "<label class='$css_class' for='$name'>$name_shown</label>";
        echo "<input class='$css_class' id='$name' name='$name' type='number' autocomplete='off' spellcheck='false' value='$current_value' min='$min' max='$max'/>";
}
    
    
?>
