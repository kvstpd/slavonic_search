<?php
// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.


function csl_is_separator($char)
{
    if (strpos(csl_get_separators(), $char) !== false)
        return true;
    
    if (substr(csl_pre_simplify_book($char), 0, 1) == ' ')
        return true;
    
    return false;
}

    
function csl_get_separators()
{
    return "abcdefghijklmnopqrstuvwxyz \n\t\r,;.:!?-\"/\\_()[]{}|#*1234567890";
}




   
function csl_unicode_to_ucs($str)
{
    // mb_chr(0x0487) must point to ''
    // leave out 'І' => mb_chr(0x0406),
   /* $combining_chars_with_variants = array(
                             mb_chr(0x0486), // pridyh (3 m # b )
                             mb_chr(0x0301), // acute (1 m  ~ b)
                             mb_chr(0x0300), // grave (2 m  @ b)
                             mb_chr(0x0483), // titlo (7 m  & b  (\ b probably wont use) )
                             mb_chr(0x0311), // inv breve (6 m ^ b)
                             mb_chr(0x033E), // payerok (8 m _ b)
                             mb_chr(0x2DED) . mb_chr(0x0487), // c-titlo (c m C b), comes with mb_chr(0x0487) which is probably OK
                             mb_chr(0x0486) . mb_chr(0x0301), // prid+ acute (4 m $ b)
                             mb_chr(0x0486) . mb_chr(0x0300),// prid+ grave (5 m % b)
                             );*/
    


    /*

    
     
     */
    
    
    $trr = array (
                  // must be 'capitalized' when after capital
                  mb_chr(0x0486) => '3', // pridyh (3 m # b )
                  mb_chr(0x0301) => '1', // acute (1 m  ~ b)
                  mb_chr(0x0300) => '2', // grave (2 m  @ b)
                  mb_chr(0x0483) => '7', // titlo (7 m  & b  (\ b probably wont use) )
                  mb_chr(0x0311) => '6', // inv breve (6 m ^ b)
                  mb_chr(0x033E) => '8', // payerok (8 m _ b)
                  mb_chr(0x2DED) . mb_chr(0x0487) => 'c', // c-titlo (c m C b), comes with mb_chr(0x0487) which is probably OK
                  mb_chr(0x2DED)  => 'c', // c-titlo (c m C b), comes without mb_chr(0x0487) but it is added in UCS fonts
                  mb_chr(0x0486) . mb_chr(0x0301) => '4', // prid+ acute (4 m $ b)
                  mb_chr(0x0486) . mb_chr(0x0300) => '5',// prid+ grave (5 m % b)
                  
                  mb_chr(0xA673) => '*',
                  mb_chr(0x2DE1) . mb_chr(0x0487) => '+', # combining VE
                  mb_chr(0x043E) . mb_chr(0x0301) => '0',
                  mb_chr(0x0436) . mb_chr(0x0483) => '9', # zhe with titlo above
                  mb_chr(0x2DEF) => '<', # combining HA
                  mb_chr(0x2DE9) . mb_chr(0x0487) => '=', # combining EN
                  mb_chr(0x2DEC) . mb_chr(0x0487) => '>', # combining ER
                  mb_chr(0x2DF1) . mb_chr(0x0487) => '?', # combining CHE
                  mb_chr(0x0430) . mb_chr(0x0300) => 'A', # latin A maps to AZ with grave accent
                  mb_chr(0x0463) . mb_chr(0x0311) => 'B', # latin B maps to Yat' with inverted breve
                  mb_chr(0x0434) . mb_chr(0x2DED) . mb_chr(0x0487) => 'D',
                  mb_chr(0x0434) . mb_chr(0x2DED)  => 'D',
                  mb_chr(0x0435) . mb_chr(0x0300) => 'E', # latin E maps to e with grave accent
                  mb_chr(0x0472) => 'F', # F maps to THETA
                  mb_chr(0x0433) . mb_chr(0x0483) => 'G', # G maps to ge with TITLO
                  mb_chr(0x0461) . mb_chr(0x0301) => 'H', # latin H maps to omega with acute accent
                  mb_chr(0x0406) => 'I',
                  mb_chr(0x0456) . mb_chr(0x0300) => 'J',
                  mb_chr(0xA656) . mb_chr(0x0486) => 'K', # YA with psili
                  mb_chr(0x043B) . mb_chr(0x2DE3) => 'L', # el with cobining de
                  mb_chr(0x0476) => 'M', # capital IZHITSA with kendema
                  mb_chr(0x047A) . mb_chr(0x0486) => 'N', # capital WIDE ON with psili
                  mb_chr(0x047A) => 'O', # just capital WIDE ON
                  mb_chr(0x0470) => 'P', # capital PSI
                  mb_chr(0x047C) => 'Q', # capital omega with great apostrophe
                  mb_chr(0x0440) . mb_chr(0x0483) => 'R', # lowercase re with titlo
                  mb_chr(0x0467) . mb_chr(0x0300) => 'S', # lowercase small yus with grave
                  mb_chr(0x047E) => 'T', # capital OT
                  mb_chr(0x041E) . mb_chr(0x0443) => 'U', # diagraph capital UK
                  mb_chr(0x0474) => 'V', # capital IZHITSA
                  mb_chr(0x0460) => 'W', # capital OMEGA
                  mb_chr(0x046E) => 'X', # capital XI
                  mb_chr(0xA64B) . mb_chr(0x0300) => 'Y', # monograph uk with grave
                  mb_chr(0x0466) => 'Z', # capital SMALL YUS
                  mb_chr(0x0430) . mb_chr(0x0301) => 'a', # latin A maps to AZ with acute accent
                  mb_chr(0x2DEA) . mb_chr(0x0487) => 'b', # combining ON
                  mb_chr(0x2DE3) => 'd', # combining DE
                  mb_chr(0x0435) . mb_chr(0x0301) => 'e', # latin E maps to e with acute accent
                  mb_chr(0x0473) => 'f', # lowercase theta
                  mb_chr(0x2DE2) . mb_chr(0x0487) => 'g', # combining ge
                  mb_chr(0x044B) . mb_chr(0x0301) => 'h', # ery with acute accent
                  mb_chr(0x0456) => 'i',
                  mb_chr(0x0456) . mb_chr(0x0301) => 'j', # i with acute accent
                  mb_chr(0xA657) . mb_chr(0x0486) => 'k', # iotaed a with psili
                  mb_chr(0x043B) . mb_chr(0x0483) => 'l', # el with titlo
                  mb_chr(0x0477) => 'm', # izhitsa with ''
                  mb_chr(0x047B) . mb_chr(0x0486) => 'n', # wide on with psili
                  mb_chr(0x047B) => 'o', # wide on
                  mb_chr(0x0471) => 'p', # lowercase psi
                  mb_chr(0x047D) => 'q', # lowercase omega with great apostrophe
                  mb_chr(0x0440) . mb_chr(0x2DED) . mb_chr(0x0487) => 'r', # lowercase er with combining es
                  mb_chr(0x0440) . mb_chr(0x2DED)  => 'r', # lowercase er with combining es
                  mb_chr(0x0467) . mb_chr(0x0301) => 's', # lowercase small yus with acute accent
                  mb_chr(0x047F) => 't', # lowercase ot
                  mb_chr(0x1C82) . mb_chr(0x0443) => 'u', # diagraph uk
                  mb_chr(0x0475) => 'v', # lowercase izhitsa
                  mb_chr(0x0461) => 'w', # lowercase omega
                  mb_chr(0x046F) => 'x', # lowercase xi
                  mb_chr(0xA64B) . mb_chr(0x0301) =>'y' , # monograph uk with acute accent
                  mb_chr(0x0467) => 'z', # lowercase small yus
                  mb_chr(0xA64B) . mb_chr(0x0311) => '{', # monograph uk with inverted breve
                  mb_chr(0x0467) . mb_chr(0x0486) . mb_chr(0x0300) => '|', # lowercase small yus with apostroph
                  mb_chr(0x0438) . mb_chr(0x0483) => '}', # the numeral eight

                  mb_chr(0x0475) . mb_chr(0x0301) => 'Ђ', # lowercase izhitsa with acute
                  mb_chr(0x0410) . mb_chr(0x0486) . mb_chr(0x0301) => 'Ѓ', # uppercase A with psili and acute
                  mb_chr(0x201A) => '‚',
                  mb_chr(0x0430) . mb_chr(0x0486) . mb_chr(0x0301) => 'ѓ', # lowercase A with psili and acute
                  mb_chr(0x201E) => '„',
                  mb_chr(0x046F) . mb_chr(0x0483) => '…', # the numberal sixty
                  mb_chr(0x0430) . mb_chr(0x0311) => '†', # lowercase a with inverted breve
                  mb_chr(0x0456) . mb_chr(0x0311) => '‡', # lowercase i with inverted breve
                  mb_chr(0x2DE5) => '€', # combining ze
                  mb_chr(0x0467) . mb_chr(0x0311) => '‰', # lowercase small yus with inverted breve
                  mb_chr(0x0466) . mb_chr(0x0486) => 'Љ', # upercase small yus with psili
                  mb_chr(0x0456) . mb_chr(0x0483) => '‹', # the numeral ten
                  mb_chr(0x0460) . mb_chr(0x0486) => 'Њ', # capital OMEGA with psili
                  mb_chr(0x041E) . mb_chr(0x0443) . mb_chr(0x0486) . mb_chr(0x0301) => 'Ќ', # diagraph uk with apostroph
                  mb_chr(0xA656) . mb_chr(0x0486) . mb_chr(0x0301) => 'Ћ', # uppercase Iotated A with apostroph
                  mb_chr(0x047A) . mb_chr(0x0486) . mb_chr(0x0301) => 'Џ', # uppercase Round O with apostroph
                  mb_chr(0x0475) . mb_chr(0x2DE2) . mb_chr(0x0487) => 'ђ', # lowercase izhitsa with combining ge
                  mb_chr(0x2018) => '‘',
                  mb_chr(0x2019) => '’',
                  mb_chr(0x201C) => '“' ,
                  mb_chr(0x201D) => '”',
                  mb_chr(0x2DE4) => '•', # combining zhe
                  mb_chr(0x2013) => '–',
                  mb_chr(0x2014) => '—',
                  mb_chr(0x0442) . mb_chr(0x0483) => '™',
                  mb_chr(0x0467) . mb_chr(0x0486) => 'љ', # lowercase small yus with psili
                  mb_chr(0x0475) . mb_chr(0x0311) => '›', # izhitsa with inverted breve
                  mb_chr(0x0461) . mb_chr(0x0486) => 'њ', # lowercase omega with psili
                  mb_chr(0x1C82) . mb_chr(0x0443) . mb_chr(0x0486) . mb_chr(0x0301) => 'ќ', # diagraph uk with apostroph
                  mb_chr(0xA657) . mb_chr(0x0486) . mb_chr(0x0301) => 'ћ', # lowercase iotaed a with apostroph
                  mb_chr(0x047B) . mb_chr(0x0486) . mb_chr(0x0301) => 'џ', # lowercase Round O with apostroph
                  mb_chr(0x041E) . mb_chr(0x0443) . mb_chr(0x0486) => 'Ў', # Capital Diagraph Uk with psili
                  mb_chr(0x1C82) . mb_chr(0x0443) . mb_chr(0x0486) => 'ў', # lowercase of the above
                  mb_chr(0x0406) . mb_chr(0x0486) . mb_chr(0x0301) => 'Ј', # Uppercase I with apostroph
                  mb_chr(0x0482) => '¤', # cyrillic thousands sign
                  mb_chr(0x0410) . mb_chr(0x0486) => 'Ґ', # capital A with psili
                  mb_chr(0x0445) . mb_chr(0x0483) => '¦', # lowercase kha with titlo
                  mb_chr(0x0447) . mb_chr(0x0483) => '§', # the numeral ninety
                  mb_chr(0x0463) . mb_chr(0x0300) => 'Ё', # lowecase yat with grave accent
                  mb_chr(0x0441) . mb_chr(0x0483) => '©', # the numeral two hundred
                  mb_chr(0x00AB) => '«',
                  mb_chr(0x00AC) => '¬',
                  mb_chr(0x0440) . mb_chr(0x2DE3) => '®', # lowercase er with dobro titlo
                  mb_chr(0x0406) . mb_chr(0x0486) => 'Ї',
                  mb_chr(0xA67E) => '°', # kavyka
                  mb_chr(0xA657) . mb_chr(0x0486) . mb_chr(0x0300) => '±',
                  mb_chr(0x0456) . mb_chr(0x0308) => 'і',
                  mb_chr(0x0430) . mb_chr(0x0486) => 'ґ',
                  mb_chr(0x0443) => 'µ', # small letter u (why encoded at the micro sign?!)
                  mb_chr(0x0463) . mb_chr(0x0301) => 'ё', # lowercase yat with acute accent
                  mb_chr(0x0430) . mb_chr(0x0483) => '№', # the numeral one
                  mb_chr(0x0454) => 'є', # wide E
                  mb_chr(0x00BB) => '»',
                  mb_chr(0x0456) . mb_chr(0x0486) . mb_chr(0x0301) => 'ј', # lowercase i with apostroph
                  mb_chr(0x0405) => 'Ѕ',
                  mb_chr(0x0455) => 'ѕ',
                  mb_chr(0x0456) . mb_chr(0x0486) => 'ї', # lowercase i with psili
                  mb_chr(0xA64A) => 'У',
                  mb_chr(0x0462) => 'Э', # capital yat
                  mb_chr(0xA656) => 'Я', # capital Iotified A
                  mb_chr(0xA64B) => 'у', # monograph Uk (why?!)
                  mb_chr(0x0463) => 'э', # lowercase yat
                  mb_chr(0xA657) => 'я', # iotaed a
                  mb_chr(0x0487)  => '', // just in case some titlos are left out, at least remove its covering

				 mb_chr(0x0407) => 'I', # I with ..  that aren't seen
				 mb_chr(0x0457) => 'і', # i with ..

	                 mb_chr(0x043E) . mb_chr(0x0443) => 'u', # diagraph small uk
                  mb_chr(0x043E) . mb_chr(0x0443) . mb_chr(0x0486) . mb_chr(0x0301) => 'ќ', # diagraph small uk with apostroph
                 mb_chr(0x043E) . mb_chr(0x0443) . mb_chr(0x0486) => 'ў', # Capital Diagraph Uk with psili
 

					'{' => '[',
					'}' => ']',

				'й' => 'й',
				'v̏' => 'm',
				'ѷ' => 'm',
				'ѷ'  => 'm',
				'Є' => 'Е',
				'ѝ' => 'и2',
				'ѐ' => 'E',
				'🕅' => 'МР&К',
                  );
    
	$pre_res =  strtr($str, $trr);
	

    $capitals_ucs = "FIKMNOPQTUVWXZЃЉЊЌЋЏЎЈҐЇІЅЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ";
    
	$need_capitalizing = '12345678c';
	$capitalized = '~@#$%^&_C';

/* $combining_chars_with_variants = array(
                             mb_chr(0x0486), // pridyh (3 m # b )
                             mb_chr(0x0301), // acute (1 m  ~ b)
                             mb_chr(0x0300), // grave (2 m  @ b)
                             mb_chr(0x0483), // titlo (7 m  & b  (\ b probably wont use) )
                             mb_chr(0x0311), // inv breve (6 m ^ b)
                             mb_chr(0x033E), // payerok (8 m _ b)
                             mb_chr(0x2DED) . mb_chr(0x0487), // c-titlo (c m C b), comes with mb_chr(0x0487) which is probably OK
                             mb_chr(0x0486) . mb_chr(0x0301), // prid+ acute (4 m $ b)
                             mb_chr(0x0486) . mb_chr(0x0300),// prid+ grave (5 m % b)
                             );*/
    



	$res = $pre_res;
	$len = mb_strlen($pre_res);

	$prev_ch = '';

	for ($pos = 0; $pos < $len; $pos++)
	{
		$ch = mb_substr($res, $pos, 1);

		//if ($prev_ch == 'ы')
		//	continue;			

		if (strstr($need_capitalizing, $ch) !== false )
		{
			if (strstr($capitals_ucs, $prev_ch) !== false )
			{
				//echo '---'.$prev_ch.'---<br/>';

				$ch = strtr($ch, $need_capitalizing, $capitalized);

				$res = 	mb_substr($res, 0, $pos).$ch.mb_substr($res,$pos +1);
					//mb_substr_replace($res, $ch, $pos, 1);
			}
		}
		else
		{
			$prev_ch = $ch;
		}		
	}


    return $res;
}
    
    
    

    
function csl_ucs_to_unicode($str)
{
    $trr = array (
                  '#' => mb_chr(0x0486),
                  '$' => mb_chr(0x0486) . mb_chr(0x0301),
                  '%' => mb_chr(0x0486) . mb_chr(0x0300),
                  '&' => mb_chr(0x0483),
                  '*' => mb_chr(0xA673),
                  '+' => mb_chr(0x2DE1) . mb_chr(0x0487), # combining VE
                  '0' => mb_chr(0x043E) . mb_chr(0x0301),
                  '1' => mb_chr(0x0301),
                  '2' => mb_chr(0x0300),
                  '3' => mb_chr(0x0486),
                  '4' => mb_chr(0x0486) . mb_chr(0x0301),
                  '5' => mb_chr(0x0486) . mb_chr(0x0300),
                  '6' => mb_chr(0x0311), # combining inverted breve
                  '7' => mb_chr(0x0483), # titlo
                  '8' => mb_chr(0x033E), # combining vertical tilde
                  '9' => mb_chr(0x0436) . mb_chr(0x0483), # zhe with titlo above
                  '<' => mb_chr(0x2DEF), # combining HA
                  '=' => mb_chr(0x2DE9) . mb_chr(0x0487), # combining EN
                  '>' => mb_chr(0x2DEC) . mb_chr(0x0487), # combining ER
                  '?' => mb_chr(0x2DF1) . mb_chr(0x0487), # combining CHE
                  '@' => mb_chr(0x0300),
                  'A' => mb_chr(0x0430) . mb_chr(0x0300), # latin A maps to AZ with grave accent
                  'B' => mb_chr(0x0463) . mb_chr(0x0311), # latin B maps to Yat' with inverted breve
                  'C' => mb_chr(0x2DED) . mb_chr(0x0487), # combining ES
                  'D' => mb_chr(0x0434) . mb_chr(0x2DED) . mb_chr(0x0487),
                  'E' => mb_chr(0x0435) . mb_chr(0x0300), # latin E maps to e with grave accent
                  'F' => mb_chr(0x0472), # F maps to THETA
                  'G' => mb_chr(0x0433) . mb_chr(0x0483), # G maps to ge with TITLO
                  'H' => mb_chr(0x0461) . mb_chr(0x0301), # latin H maps to omega with acute accent
                  'I' => mb_chr(0x0406),
                  'J' => mb_chr(0x0456) . mb_chr(0x0300),
                  'K' => mb_chr(0xA656) . mb_chr(0x0486), # YA with psili
                  'L' => mb_chr(0x043B) . mb_chr(0x2DE3), # el with cobining de
                  'M' => mb_chr(0x0476), # capital IZHITSA with kendema
                  'N' => mb_chr(0x047A) . mb_chr(0x0486), # capital WIDE ON with psili
                  'O' => mb_chr(0x047A), # just capital WIDE ON
                  'P' => mb_chr(0x0470), # capital PSI
                  'Q' => mb_chr(0x047C), # capital omega with great apostrophe
                  'R' => mb_chr(0x0440) . mb_chr(0x0483), # lowercase re with titlo
                  'S' => mb_chr(0x0467) . mb_chr(0x0300), # lowercase small yus with grave
                  'T' => mb_chr(0x047E), # capital OT
                  'U' => mb_chr(0x041E) . mb_chr(0x0443), # diagraph capital UK
                  'V' => mb_chr(0x0474), # capital IZHITSA
                  'W' => mb_chr(0x0460), # capital OMEGA
                  'X' => mb_chr(0x046E), # capital XI
                  'Y' => mb_chr(0xA64B) . mb_chr(0x0300), # monograph uk with grave
                  'Z' => mb_chr(0x0466), # capital SMALL YUS
                  '\\' => mb_chr(0x0483), # yet another titlo
                  '^' => mb_chr(0x0311), # combining inverted breve
                  '_' => mb_chr(0x033E), # yet another yerik
                  'a' => mb_chr(0x0430) . mb_chr(0x0301), # latin A maps to AZ with acute accent
                  'b' => mb_chr(0x2DEA) . mb_chr(0x0487), # combining ON
                  'c' => mb_chr(0x2DED) . mb_chr(0x0487), # combining ES
                  'd' => mb_chr(0x2DE3), # combining DE
                  'e' => mb_chr(0x0435) . mb_chr(0x0301), # latin E maps to e with acute accent
                  'f' => mb_chr(0x0473), # lowercase theta
                  'g' => mb_chr(0x2DE2) . mb_chr(0x0487), # combining ge
                  'h' => mb_chr(0x044B) . mb_chr(0x0301), # ery with acute accent
                  'i' => mb_chr(0x0456),
                  'j' => mb_chr(0x0456) . mb_chr(0x0301), # i with acute accent
                  'k' => mb_chr(0xA657) . mb_chr(0x0486), # iotaed a with psili
                  'l' => mb_chr(0x043B) . mb_chr(0x0483), # el with titlo
                  'm' => mb_chr(0x0477), # izhitsa with ''
                  'n' => mb_chr(0x047B) . mb_chr(0x0486), # wide on with psili
                  'o' => mb_chr(0x047B), # wide on
                  'p' => mb_chr(0x0471), # lowercase psi
                  'q' => mb_chr(0x047D), # lowercase omega with great apostrophe
                  'r' => mb_chr(0x0440) . mb_chr(0x2DED) . mb_chr(0x0487), # lowercase er with combining es
                  's' => mb_chr(0x0467) . mb_chr(0x0301), # lowercase small yus with acute accent
                  't' => mb_chr(0x047F), # lowercase ot
                  'u' => mb_chr(0x1C82) . mb_chr(0x0443), # diagraph uk
                  'v' => mb_chr(0x0475), # lowercase izhitsa
                  'w' => mb_chr(0x0461), # lowercase omega
                  'x' => mb_chr(0x046F), # lowercase xi
                  'y' => mb_chr(0xA64B) . mb_chr(0x0301), # monograph uk with acute accent
                  'z' => mb_chr(0x0467), # lowercase small yus
                  '{' => mb_chr(0xA64B) . mb_chr(0x0311), # monograph uk with inverted breve
                  '|' => mb_chr(0x0467) . mb_chr(0x0486) . mb_chr(0x0300), # lowercase small yus with apostroph
                  '}' => mb_chr(0x0438) . mb_chr(0x0483), # the numeral eight
                  '~' => mb_chr(0x0301), # yet another acute accent
                  ### SECOND HALF IS THE CYRILLIC BLOCK
                  'Ђ' => mb_chr(0x0475) . mb_chr(0x0301), # lowercase izhitsa with acute
                  'Ѓ' => mb_chr(0x0410) . mb_chr(0x0486) . mb_chr(0x0301), # uppercase A with psili and acute
                  '‚' => mb_chr(0x201A),
                  'ѓ' => mb_chr(0x0430) . mb_chr(0x0486) . mb_chr(0x0301), # lowercase A with psili and acute
                  '„' => mb_chr(0x201E),
                  '…' => mb_chr(0x046F) . mb_chr(0x0483), # the numberal sixty
                  '†' => mb_chr(0x0430) . mb_chr(0x0311), # lowercase a with inverted breve
                  '‡' => mb_chr(0x0456) . mb_chr(0x0311), # lowercase i with inverted breve
                  '€' => mb_chr(0x2DE5), # combining ze
                  '‰' => mb_chr(0x0467) . mb_chr(0x0311), # lowercase small yus with inverted breve
                  'Љ' => mb_chr(0x0466) . mb_chr(0x0486), # upercase small yus with psili
                  '‹' => mb_chr(0x0456) . mb_chr(0x0483), # the numeral ten
                  'Њ' => mb_chr(0x0460) . mb_chr(0x0486), # capital OMEGA with psili
                  'Ќ' => mb_chr(0x041E) . mb_chr(0x0443) . mb_chr(0x0486) . mb_chr(0x0301), # diagraph uk with apostroph
                  'Ћ' => mb_chr(0xA656) . mb_chr(0x0486) . mb_chr(0x0301), # uppercase Iotated A with apostroph
                  'Џ' => mb_chr(0x047A) . mb_chr(0x0486) . mb_chr(0x0301), # uppercase Round O with apostroph
                  'ђ' => mb_chr(0x0475) . mb_chr(0x2DE2) . mb_chr(0x0487), # lowercase izhitsa with combining ge
                  '‘' => mb_chr(0x2018),
                  '’' => mb_chr(0x2019),
                  '“' => mb_chr(0x201C),
                   '”' => mb_chr(0x201D),
                   '•' => mb_chr(0x2DE4), # combining zhe
                  '–' => mb_chr(0x2013),
                   '—' => mb_chr(0x2014),
                   '™' => mb_chr(0x0442) . mb_chr(0x0483),
                  'љ' => mb_chr(0x0467) . mb_chr(0x0486), # lowercase small yus with psili
                  '›' => mb_chr(0x0475) . mb_chr(0x0311), # izhitsa with inverted breve
                  'њ' => mb_chr(0x0461) . mb_chr(0x0486), # lowercase omega with psili
                  'ќ' => mb_chr(0x1C82) . mb_chr(0x0443) . mb_chr(0x0486) . mb_chr(0x0301), # diagraph uk with apostroph
                   'ћ' => mb_chr(0xA657) . mb_chr(0x0486) . mb_chr(0x0301), # lowercase iotaed a with apostroph
                   'џ' => mb_chr(0x047B) . mb_chr(0x0486) . mb_chr(0x0301), # lowercase Round O with apostroph
                  'Ў' => mb_chr(0x041E) . mb_chr(0x0443) . mb_chr(0x0486), # Capital Diagraph Uk with psili
                  'ў' => mb_chr(0x1C82) . mb_chr(0x0443) . mb_chr(0x0486), # lowercase of the above
                  'Ј' => mb_chr(0x0406) . mb_chr(0x0486) . mb_chr(0x0301), # Uppercase I with apostroph
                  '¤' => mb_chr(0x0482), # cyrillic thousands sign
                  'Ґ' => mb_chr(0x0410) . mb_chr(0x0486), # capital A with psili
                  '¦' => mb_chr(0x0445) . mb_chr(0x0483), # lowercase kha with titlo
                  '§' => mb_chr(0x0447) . mb_chr(0x0483), # the numeral ninety
                  'Ё' => mb_chr(0x0463) . mb_chr(0x0300), # lowecase yat with grave accent
                  '©' => mb_chr(0x0441) . mb_chr(0x0483), # the numeral two hundred
                  '«' => mb_chr(0x00AB),
                  '¬' => mb_chr(0x00AC),
                  '®' => mb_chr(0x0440) . mb_chr(0x2DE3), # lowercase er with dobro titlo
                  'Ї' => mb_chr(0x0406) . mb_chr(0x0486),
                  '°' => mb_chr(0xA67E), # kavyka
                  '±' => mb_chr(0xA657) . mb_chr(0x0486) . mb_chr(0x0300),
                  'І' => mb_chr(0x0406),         // maybe add  . mb_chr(0x0308), ?
                  'і' => mb_chr(0x0456) . mb_chr(0x0308),
                  'ґ' => mb_chr(0x0430) . mb_chr(0x0486),
                  'µ' => mb_chr(0x0443), # small letter u (why encoded at the micro sign?!)
                  'ё' => mb_chr(0x0463) . mb_chr(0x0301), # lowercase yat with acute accent
                  '№' => mb_chr(0x0430) . mb_chr(0x0483), # the numeral one
                  'є' => mb_chr(0x0454), # wide E
                  '»' => mb_chr(0x00BB),
                  'ј' => mb_chr(0x0456) . mb_chr(0x0486) . mb_chr(0x0301), # lowercase i with apostroph
                  'Ѕ' => mb_chr(0x0405),
                  'ѕ' => mb_chr(0x0455),
                  'ї' => mb_chr(0x0456) . mb_chr(0x0486), # lowercase i with psili
                  'У' => mb_chr(0xA64A),
                  'Э' => mb_chr(0x0462), # capital yat
                  'Я' => mb_chr(0xA656), # capital Iotified A
                  'у' => mb_chr(0xA64B), # monograph Uk (why?!)
                  'э' => mb_chr(0x0463), # lowercase yat
                  'я' => mb_chr(0xA657), # iotaed a

					'<М>>' => '🕅',
					'МР&К' => '🕅',
    );


    $result = strtr($str, $trr);
    
    return $result;
    //strtr($result, array(mb_chr(0xA415).mb_chr(0x0486) => mb_chr(0xA404).mb_chr(0x0486)) ); what was that? A4** is not even Cyrillic
}



?>
