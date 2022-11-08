<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.

function open_handle_for_book_path($path)
{
	$slash_pos = strpos($path, '/');
	$file_path = false;
		
	if ($slash_pos === false)
	{
		$file_path = 'books/'.$path;
	}
	else
	{
		// 'zip://'.substr($result['path'], 0, $slash_pos).'.zip#'.
		$file_path = $path;
	}
		
	return fopen($file_path, 'r');
}

function get_backward_context_for_position($file, $position, $context_size)
{
	$start = $position - $context_size;

	if ($start > 0)
	{
		fseek($file, $start);
		return fread($file, $context_size);
	}
	else
	{
		fseek($file, 0);
		return fread($file, $position);
	}	
}


function get_last_verse_chapter_page($text)
{
	$ret = array('v' => null, 'c' => null, 'p' => null, 'n' => null);

	$len = strlen($text);
	$last_pipe_pos = strrpos($text, '|');
	
	if ($last_pipe_pos > 0)
	{
		$nl_pos = strrpos($text, "\n", ($len - $last_pipe_pos));

		if ($nl_pos !== false)
			$ret['v'] = substr($text, $nl_pos + 1, $last_pipe_pos - $nl_pos - 1);
	}

	$last_grate_pos = strrpos($text, '#');
	
	if ($last_grate_pos !== false)
	{
		$nl_pos = strpos($text, "\n", $last_grate_pos);
		
		if ($nl_pos !== false)
			$ret['c'] =  substr($text, $last_grate_pos + 1, $nl_pos - $last_grate_pos - 1);
	}


	$last_dsb_pos = strrpos($text, '[[');
	
	if ($last_dsb_pos !== false)
	{
		$close_pos = strpos($text, "]]", $last_dsb_pos + 2);
		
		if ($close_pos !== false)
			$ret['n'] = substr($text, $last_dsb_pos + 2, $close_pos - $last_dsb_pos - 2);
	}


	$is_leaf = false;
	$last_page_pos = strrpos($text, '(с.');
	
	if ($last_page_pos === false)
	{
		$last_page_pos = strrpos($text, '(л.');
		$is_leaf = true;	
	}	

	if ($last_page_pos !== false)
	{
		$last_page_pos += strlen('(с.');
		//do
		
		//while (substr($text, $last_page_pos, 1) == ' ');
		//	$last_page_pos++;

		$close_bracket_pos = strpos($text, ")", $last_page_pos);

		if ($close_bracket_pos !== false)
			$ret['p'] =  ($is_leaf ? 'лист ' : 'cтр. ') . trim(substr($text, $last_page_pos, $close_bracket_pos-$last_page_pos));
	}

	return $ret;
}



function csl_default_book_types()
{
    return array(
                 'f' => 'Святоотеческая литература',
                 'o' => 'Дореформенная орфография',
                 'n' => 'Неполная диакритика',
                 'b' => 'Елизаветинская Библия',
                 'c' => 'Богослужебные книги'
                 );
}


function csl_default_book_data()
{
	return array(
        'Gen.txt' => array(1, 'Быт.', 'Бытие', 'b'),
        'Ex.txt' => array(2, 'Исх.', 'Исход', 'b'),
        'Lev.txt' => array(3, 'Лев.', 'Левит', 'b'),
        'Num.txt' => array(4,'Числ.', 'Числа', 'b'),
        'Deut.txt' => array(5, 'Втор.', 'Второзаконие', 'b'),

        'Josh.txt' => array(6, 'Нав.', 'Книга Иисуса Навина', 'b'),
        'Judg.txt' => array(7, 'Суд.', 'Книга Судей Израилевых', 'b'),
        'Ruth.txt' => array(8, 'Руфь.', 'Книга Руфи', 'b'),
        'I_Kings.txt' => array(9, '1 Цар.', '1-я книга Царств', 'b'),
        'II_Kings.txt' => array(10, '2 Цар.', '2-я книга Царств', 'b'),
        'III_Kings.txt' => array(11, '3 Цар.', '3-я книга Царств', 'b'),
        'IV_Kings.txt' => array(12, '4 Цар.', '4-я книга Царств', 'b'),
        'I_Paral.txt' => array(13, '1 Пар.', '1-я книга Паралипоменон', 'b'),
        'II_Paral.txt' => array(14, '2 Пар.', '2-я книга Паралипоменон', 'b'),
        'I_Esdra.txt' => array(16, '1 Ездр.', '1-я книга Ездры', 'b'),
        'Nehem.txt' => array(17, 'Неем.', 'Книга Неемии', 'b'),
        'II_Esdra.txt' => array(18, '2 Ездр.', '2-я книга Ездры', 'b'),
        'Tobit.txt' => array(20, 'Тов.', 'Книга Товита', 'b'),
        'Judith.txt' => array(21, 'Иудифь.', 'Книга Иудифи', 'b'),
        'Esther.txt' => array(22, 'Есф.', 'Книга Есфири', 'b'),

        'Job.txt' => array(23, 'Иов.', 'Книга Иова', 'b'),
        'Psalm.txt' => array(24, 'Пс.', 'Псалтирь', 'b'),
        'Prov.txt' => array(25, 'Притч.', 'Книга Притчей Соломоновых', 'b'),
        'Eccles.txt' => array(26, 'Еккл.', 'Екклесиаст', 'b'),
        'Song.txt' => array(27, 'Песн.', 'Песнь Песней Соломона', 'b'),
        'Wisd.txt' => array(28, 'Прем.', 'Книга Премудрости Соломона', 'b'),
        'Sirach.txt' => array(29, 'Сир.', 'Книга Премудрости Иисуса, Сына Сирахова', 'b'),

        'Isa.txt' => array(30, 'Ис.', 'Книга пророка Исаии', 'b'),
        'Jerem.txt' => array(31, 'Иер.', 'Книга пророка Иеремии', 'b'),
        'Lamen.txt' => array(32, 'Плач.', 'Плач Иеремии', 'b'),
        'Epistle.txt' => array(33, 'Посл. Иер.', 'Послание Иеремии', 'b'),
        'Baruch.txt' => array(34, 'Вар.', 'Книга пророка Варуха', 'b'),
        'Ezek.txt' => array(35, 'Иез.', 'Книга пророка Иезекииля', 'b'),
        'Dan.txt' => array(36, 'Дан.', 'Книга пророка Даниила', 'b'),
        'Hos.txt' => array(37, 'Ос.', 'Книга пророка Осии', 'b'),
        'Joel.txt' => array(38, 'Иоиль.', 'Книга пророка Иоиля', 'b'),
        'Amos.txt' => array(39, 'Ам.', 'Книга пророка Амоса', 'b'),
        'Obad.txt' => array(40, 'Авд.', 'Книга пророка Авдия', 'b'),
        'Jona.txt' => array(41, 'Иона.', 'Книга пророка Ионы', 'b'),
        'Mica.txt' => array(42, 'Мих.', 'Книга пророка Михея', 'b'),
        'Nahum.txt' => array(43, 'Наум.', 'Книга пророка Наума', 'b'),
        'Habak.txt' => array(44, 'Авв.', 'Книга пророка Аввакума', 'b'),
        'Zeph.txt' => array(45, 'Соф.', 'Книга пророка Софонии', 'b'),
        'Hagg.txt' => array(46, 'Агг.', 'Книга пророка Аггея', 'b'),
        'Zech.txt' => array(47, 'Зах.', 'Книга пророка Захарии', 'b'),
        'Mal.txt' => array(48, 'Мал.', 'Книга пророка Малахии', 'b'),

        'I_Macc.txt' => array(49, '1 Макк.', '1-я книга Маккавейская', 'b'),
        'II_Macc.txt' => array(50, '2 Макк.', '2-я книга Маккавейская', 'b'),
        'III_Macc.txt' => array(51, '3 Макк.', '3-я книга Маккавейская', 'b'),

        'III_Esdra.txt' => array(52, '3 Ездр.', '3-я книга Ездры', 'b'),

        'Mt.txt' => array(53, 'Мф.', 'Евангелие от Матфея', 'b'),
        'Mk.txt' => array(54, 'Мк.', 'Евангелие от Марка', 'b'),
        'Lk.txt' => array(55, 'Лк.', 'Евангелие от Луки', 'b'),
        'Jn.txt' => array(56, 'Ин.', 'Евангелие от Иоанна', 'b'),

        'Acts.txt' => array(57, 'Деян.', 'Деяния апостолов', 'b'),

        'Jas.txt' => array(58, 'Иак.', 'Соборное послание ап. Иакова', 'b'),
        'I_Pet.txt' => array(59, '1 Пет.', '1-е соборное послание ап. Петра', 'b'),
        'II_Pet.txt' => array(60, '2 Пет.', '2-е соборное послание ап. Петра', 'b'),
        'I_Jn.txt' => array(61, '1 Ин.', '1-е соборное послание ап. Иоанна', 'b'),
        'II_Jn.txt' => array(62, '2 Ин.', '2-е соборное послание ап. Иоанна', 'b'),
        'III_Jn.txt' => array(63, '3 Ин.', '3-е соборное послание ап. Иоанна', 'b'),
        'Jude.txt' => array(64, 'Иуд.', 'Соборное послание ап. Иуды', 'b'),

        'Rom.txt' => array(65, 'Рим.', 'Послание к Римлянам ап. Павла', 'b'),
        'I_Cor.txt' => array(66, '1 Кор.', '1-е послание к Коринфянам ап. Павла', 'b'),
        'II_Cor.txt' => array(67, '2 Кор.', '2-е послание к Коринфянам ап. Павла', 'b'),
        'Gal.txt' => array(68, 'Гал.', 'Послание к Галатам ап. Павла', 'b'),
        'Eph.txt' => array(69, 'Еф.', 'Послание к Ефесянам ап. Павла', 'b'),
        'Philip.txt' => array(70, 'Флп.', 'Послание к Филиппийцам ап. Павла', 'b'),
        'Col.txt' => array(71, 'Кол.', 'Послание к Колоссянам ап. Павла', 'b'),
        'I_Thess.txt' => array(72, '1 Фес.', '1-е послание к Фессалоникийцам ап. Павла', 'b'),
        'II_Thess.txt' => array(73, '2 Фес.', '2-е послание к Фессалоникийцам ап. Павла', 'b'),
        'I_Tim.txt' => array(74, '1 Тим.', '1-е послание к Тимофею ап. Павла', 'b'),
        'II_Tim.txt' => array(75, '2 Тим.', '2-е послание к Тимофею ап. Павла', 'b'),
        'Tit.txt' => array(76, 'Тит.', 'Послание к Титу ап. Павла', 'b'),
        'Philemon.txt' => array(77, 'Флм.', 'Послание к Филимону ап. Павла', 'b'),
        'Heb.txt' => array(78, 'Евр.', 'Послание к Евреям ап. Павла', 'b'),

        'Apoc.txt' => array(79, 'Откр.', 'Откровение Иоанна Богослова', 'b'),

        'Composite.txt' => array(80, 'Сост. Чт.', 'Составные чтения Библии', 'b'),

        'Evangelion.txt' => array(1, 'Богослужебное Евангелие', 'Богослужебное Евангелие. Издание Московской Патриархии. Москва, 1984', 'c'),
        'Apostolos.txt' => array(2, 'Богослужебный Апостол', 'Богослужебный Апостол. Издание Московской Патриархии. Москва, 1989', 'c'),

        'Chasoslov.txt' => array(3, 'Часослов', 'Часослов. Издание Московской Патриархии. Москва, 1991', 'c'),
        'AugmentedPsalter.txt' => array(4, 'Следованная Псалтирь', 'Следованная Псалтирь. Издание Московской Патриархии. Москва, 1978', 'c'),

        'Oktoih.txt' => array(5, 'Октоих', 'Октоих. Издание Московской Патриархии. Москва, 1981', 'c'),

        'MineyaSeptember.txt' => array(6, 'Минея. Сентябрь', 'Минея. Сентябрь. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1997', 'c'),
        'MineyaOctober.txt' => array(7, 'Минея. Октябрь', 'Минея. Октябрь. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1997', 'c'),
        'MineyaNovember.txt' => array(8, 'Минея. Ноябрь', 'Минея. Ноябрь. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1997', 'c'),
        'MineyaDecember.txt' => array(9, 'Минея. Декабрь', 'Минея. Декабрь. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1997', 'c'),
        'MineyaYanvar.txt' => array(10, 'Минея. Январь', 'Минея. Январь. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),
        'MineyaFebvral.txt' => array(11, 'Минея. Февраль', 'Минея. Февраль. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),
        'MineyaMart.txt' => array(12, 'Минея. Март', 'Минея. Март. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),
        'MineyaAprel.txt' => array(13, 'Минея. Апрель', 'Минея. Апрель. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),
        'MineyaMay.txt' => array(14, 'Минея. Май', 'Минея. Май. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),
        'MineyaIun.txt' => array(15, 'Минея. Июнь', 'Минея. Июнь. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),
        'MineyaIyul.txt' => array(16, 'Минея. Июль', 'Минея. Июль. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),
        'MineyaAugust.txt' => array(17, 'Минея. Август', 'Минея. Август. Издание Московского Сретенского монастыря, издательство «Правило веры». Москва, 1996', 'c'),

        'MineyaObshaya.txt' => array(18, 'Минея общая', 'Минея общая. Издание Издательского Совета Русской Православной Церкви, 2002', 'c'),

        'PostnayaTriod.txt' => array(19, 'Постная Триодь', 'Постная Триодь. Издание Московской Патриархии. Москва, 1992', 'c'),
        'TsvetnayaTriod.txt' => array(20, 'Цветная Триодь', 'Цветная Триодь. Издание Московской Патриархии. Москва, 1992', 'c'),

        'Irmologii.txt' => array(21, 'Ирмологий', 'Ирмологий. Издание Свято-Троицкой Сергиевой Лавры, 1995 (Репринт издания синодальной типографии. Москва, 1913)', 'c'),

        'Sluzhebnik.txt' => array(22, 'Служебник', 'Служебник. Издание Синодальной типографии. Москва, 1906', 'c'),
        'Trebnik.txt' => array(23, 'Требник', 'Требник. Издание Синодальной типографии. Москва, 1906', 'c'),
        'StJamesLiturgyROCOR.txt' => array(24, 'Литургия ап. Иакова (РПЦЗ)', 'Божественная Литургия Святаго Апостола Иакова. Издание Братства прп. Иова Почаевского. Владимирова, 1938', 'c'),

        'Tipikon.txt' => array(25, 'Типикон', 'Типикон. Издание Синодальной типографии (?). Москва, 1906?', 'c'),
                 

        'Dobrotolyubie.txt' => array(1, 'Добротолюбие', 'Добротолюбие. Издание православного братства святых князей Бориса и Глеба. Тутаев, 2000', 'f'),


        'Triodʹ Cvetnaya.txt' => array(1, 'Цветная Триодь (старопечат.)', 'Цветная Триодь (старопечатная)', 'o'),

                 
        'StJamesLiturgyBulg.txt' => array(1, 'Литургия ап. Иакова (Болг.)', 'Божественная Литургия Святаго Апостола Иакова. Издание Священного Синода Болгарской Православной Церкви. София, 1948', 'n'),
    
	);

}


function csl_strip_toc_markup($text)
{
    $ret = '';
    $pos = 0;
    
    $last_dsb_pos = strpos($text, '[[', $pos);
        
    if ($last_dsb_pos === false)
        return $text;
    
    do
    {
        $close_pos = strpos($text, "]]", $last_dsb_pos + 2);
        
        // that's wrong but in case it happens try to avoid truncating text
        if ($close_pos == false)
            break;
        
        $ret .= substr($text, $pos, $last_dsb_pos);
        
        $pos = $close_pos + 2;
    }
    while ( ($last_dsb_pos = strpos($text, '[[', $pos)) !== false);
    
    $ret = $ret.substr($text, $pos);
    
    return $ret;
}
    

function csl_book_toc($book)
{
	static $toc_cache = array();

	if (isset($toc_cache[$book]) )
		return $toc_cache[$book];

	// change .txt to .toc
	$toc_filename = substr($book,0, -2).'oc';

	$file = open_handle_for_book_path($toc_filename);

	if ($file === false)
		return false;

	$toc = array();

	while ($line = fgets($file))
	{
		//if (strlen($line) > 1 )
		$toc[] = $line;
	}

	fclose($file);

	if (count($toc) > 0)
		$toc_cache[$book] = $toc;

	return $toc;
}


?>
