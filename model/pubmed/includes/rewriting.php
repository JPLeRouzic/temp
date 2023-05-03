<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function rewritings(string $abstract) {
    $words = this_word_tokenizer($abstract);
//    var_dump($words) ;

    $abstractOut = '';
    $flag_TSH = 0 ;
    foreach ($words as $word) {
	// We will insert "The scientists here" only one time
	if((strcmp((string) $word, "We") === 0) && ($flag_TSH === 1)) {
		// We substitute "The researchers" to "The scientists here" as it was already used
		$word = "The researchers" ;
		}
	if((strcmp((string)$word, "We") === 0) && ($flag_TSH === 0)) {
		$flag_TSH = 1 ;
		}

        // Is it an rewriting?
        $substitute = isWordRewriting($word);

        if ($substitute !== false) {
            // It is an rewriting, so replace it by its abbrevation in $abstract
//            $abstract = str_replace($word, $substitute, $abstract);

            /*
             * The \b matches a word boundary.
             * If $text contains UTF-8 text, you'll have to add the Unicode modifier "u", 
             * so that non-latin characters are not misinterpreted as word boundaries:
             */
            $regex = '/\b' . $word . '\b/u';
            $abstract = preg_replace($regex, $substitute, $abstract);
        }
    }
//    var_dump($abstract) ;
    return $abstract;
}

function this_word_tokenizer(string $text): array {
    $result = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $text, -1, PREG_SPLIT_NO_EMPTY);
    return $result;
}

// Is it an rewriting?    
function isWordRewriting($word) {
    $KeyAcronysms = array(
        "We",
        "we",
        "Us",
        "us",
        "Our",
        "our",
        "Conclusions",
        "Here",
        "Thus",
        "Therefore",
        "Importantly",
        "Notably",
        "Despite",
        "Although",
        "Furthermore",
        "However",
        "Additionally",
        "Taken",
        "Another",
        "Given"
    );

    $ValuesAcronysms = array(
        "<br>The scientists here",
        "the authors",
        "Them",
        "them",
        "<br>The authors'",
        "authors'",
        "", // We remove "Conclusion"
        "<br>Here",
        "<br><br>Thus",
        "<br><br>Therefore",
        "<br><br>Importantly",
        "<br><br>Notably",
        "<br>Despite",
        "<br>Although",
        "<br><br>Furthermore",
        "<br><br>However",
        "<br><br>Additionally",
        "<br>Taken",
        "<br>Another",
        "<br>Given"
    );

    $tyty = array_search($word, $KeyAcronysms);
//    var_dump($tyty);

    if ((isset($tyty)) && ($tyty !== false)) {
        $tata = $ValuesAcronysms[$tyty];
        return $tata;
    } else {
        return false;
    }
}
