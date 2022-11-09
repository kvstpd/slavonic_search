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

function show_convert_params()
{
        $query_encodings = array('q_unicode' => 'Unicode', 'q_ucs' => 'UCS (как на orthlib.ru)');
        $result_encodings = array('r_unicode' => 'Unicode', 'r_ucs' => 'UCS (как на orthlib.ru)', 'r_civil' => 'Гражданский шрифт с ударениями');

        echo '<div id="csl_encoding_params">';
        echo '<fieldset>';
        echo '<legend>Исходная кодировка:</legend>';
        make_radio_group('csl_output_param', 'query_encoding', $query_encodings, 'q_unicode');
        echo '</fieldset>';
       
        echo '<fieldset>';
        echo '<legend>Кодировка результата:</legend>';
        make_radio_group('csl_output_param', 'result_encoding', $result_encodings, 'r_ucs');
        echo '</fieldset>';
        echo '</div>';
}

    
function show_word_search($db)
{
    echo '<input id="csl_search_box" type="search" placeholder="Поиск" autocomplete="off" spellcheck="false"/><button type="button" id="csl_search_button">Искать</button><br/><br/>';
    echo '<div id="csl_search_head">';
    
    echo '<div id="csl_query_params">';
    echo '<fieldset>';
    
    echo '<legend>Критерий поиска:</legend>';
    echo '<input type="radio" class="csl_search_param" id="contains" name="match_type" value="contains" checked="checked"><label for="contains">Содержит</label>';
    echo '<input type="radio" class="csl_search_param" id="begins" name="match_type" value="begins"><label for="begins">Начинается с</label>';
    echo '<input type="radio" class="csl_search_param" id="ends" name="match_type" value="ends"><label for="ends">Кончается на</label>';
    echo '<input type="radio" class="csl_search_param" id="exact" name="match_type" value="exact"><label for="exact">Точное совпадение</label>';
    
    echo '<div id="csl_multi_words">';
    echo '<input type="radio" class="csl_search_param" id="multi_and_rigid" name="multi_words" value="multi_and_rigid" checked="checked"><label for="multi_and_rigid">Все слова в указанном порядке</label>';
    echo '<input type="radio" class="csl_search_param" id="multi_and_free" name="multi_words" value="multi_and_free"><label for="multi_and_free">Все слова в любом порядке</label>';
    echo '<input type="radio" class="csl_search_param" id="multi_or" name="multi_words" value="multi_or"><label for="multi_or">Любое из слов</label><br/>';
    echo '<label for="csl_word_distance">Расстояние между словами (приблизительно символов): </label><input id="csl_word_distance" type="number" autocomplete="off" spellcheck="false" value="2" min="1" max="500"/>';
    echo '</div>';
    
    echo '</fieldset>';
    echo '</div>';
    
    echo '<div id="csl_encoding_params">';
    echo '<fieldset>';
    echo '<legend>Кодировка запроса:</legend>';
    echo '<input type="radio" class="csl_search_param" id="simplified" name="encoding" value="simplified" checked="checked"><label for="simplified">Упрощённая гражданская</label>';
    echo '<input type="radio" class="csl_search_param" id="unicode" name="encoding" value="unicode"><label for="unicode">Unicode</label>';
    echo '<input type="radio" class="csl_search_param" id="ucs" name="encoding" value="ucs"><label for="ucs">UCS (как на orthlib.ru)</label>';
    echo '</fieldset>';
    
    echo '<fieldset>';
    echo '<legend>Кодировка результата:</legend>';
    echo '<input type="radio" class="csl_output_param" id="result_unicode" name="result_encoding" value="unicode" checked="checked"><label for="result_unicode">Unicode</label>';
    echo '<input type="radio" class="csl_output_param" id="result_ucs" name="result_encoding" value="ucs"><label for="result_ucs">UCS (как на orthlib.ru)</label>';
    echo '</fieldset>';
    echo '</div>';
    
    echo '<div id="csl_books">';
    echo '<fieldset>';
    echo '<legend>Книги для поиска:</legend>';
    echo '<input type="radio" class="csl_book_select" id="book_type_common" name="book_type" value="csl_ob_book" checked="checked"><label for="book_type_common">Общеупотребимые</label>';
    echo '<input type="radio" class="csl_book_select" id="book_type_bible" name="book_type" value="csl_elis_book"><label for="book_type_bible">Елизаветинская Библия</label>';
    echo '<input type="radio" class="csl_book_select" id="book_type_present" name="book_type" value="csl_pr_book"><label for="book_type_present">Богослужебные книги</label><br/>';
    echo '<input type="radio" class="csl_book_select" id="book_type_f" name="book_type" value="csl_f_book"><label for="book_type_f">Святоотеческая литература</label>';
    echo '<input type="radio" class="csl_book_select" id="book_type_n" name="book_type" value="csl_n_book"><label for="book_type_n">Неполная диакритика</label>';
    echo '<input type="radio" class="csl_book_select" id="book_type_old" name="book_type" value="csl_old_book"><label for="book_type_old">Дореформенные</label>';
    echo '<input type="radio" class="csl_book_select" id="book_type_all" name="book_type" value="csl_book"><label for="book_type_all">Все</label><br/>';
    echo '<input type="radio" class="csl_book_select" id="book_type_choose" name="book_type" value="__choose"><label for="book_type_choose">Выбрать</label>';
    

    $books = $db->get_books();

    echo '<div id="csl_book_list">';
    echo '<fieldset id="csl_book_checkboxes" disabled="disabled">';

    if (!empty($books) && is_array($books) )
    {
        $old_checkbox_class = 'csl_search_param';
        
        foreach ($books as $book)
        {
            $book_id = $book['book_id'];
            $checkbox_id = 'book_'.$book_id;
            $checkbox_class = 'csl_search_param csl_book';
            
            if ($book['type'] == 'o')
                $checkbox_class .= ' csl_old_book';
            else if ($book['type'] == 'n')
                $checkbox_class .= ' csl_n_book';
            else if ($book['type'] == 'f')
                $checkbox_class .= ' csl_f_book csl_ob_book';
            else if ($book['type'] == 'b')
                $checkbox_class .= ' csl_elis_book csl_ob_book';
            else
                $checkbox_class .= ' csl_pr_book csl_ob_book';
            
            if ($old_checkbox_class != $checkbox_class)
            {
                echo '<hr/>';
                $old_checkbox_class = $checkbox_class;
            }
            
            echo "<nobr><input type='checkbox' class='$checkbox_class' id='$checkbox_id' name='book_id' value='$book_id'><label for='$checkbox_id'>{$book['name']}</label></nobr> ";
        }
    }

    echo '</fieldset>';
    echo '</div>';
    
    echo '</fieldset>';
    echo '</div>';
    

    
    //echo '</form>';
    echo '<div class="csl_result_container">';
        echo '<div class="csl_result_block" id="csl_search_result"></div>';
        echo '<div class="csl_result_block" id="csl_occurences"></div>';
        echo '<div class="csl_result_block" id="csl_context"></div>';
    echo '</div>';
}

function show_main_menu()
{
    echo '<p>MENU: <a href="index.php">HOME</a>';
    
    if (ADMIN_MODE == 1)
        echo ' <a href="builder.php?rebuild">Clear DB</a> <a href="builder.php?populate">Populate DB</a> <a href="builder.php?slavoniser">Make Slavoniser DB</a>';
    
    echo ' <a href="converter.php">Encoding converter</a> <a href="index.php?search">Search</a></p>';
}
    

    
?>
