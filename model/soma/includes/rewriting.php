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

    $flag_TSH = 0;
    foreach ($words as $word) {
        // We will insert "The scientists here" only one time
        if ((strcmp((string) $word, "We") === 0) && ($flag_TSH === 1)) {
            // We substitute "The researchers" to "The scientists here" as it was already used
            $word = "The researchers";
        }
        if ((strcmp((string) $word, "We") === 0) && ($flag_TSH === 0)) {
            $flag_TSH = 1;
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
    return $abstract;
}

function this_word_tokenizer(string $text): array {
    $result = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $text, -1, PREG_SPLIT_NO_EMPTY);
    return $result;
}

// Is it an rewriting?    
function isWordRewriting($word) {
    $KeyAcronysms = array(
        /* 1 */ "We",
        /* 2 */ "we",
        /* 3 */ "Us",
        /* 4 */ "us",
        /* 5 */ "Our",
        /* 6 */ "our",
        /* 7 */ "Introduction",
        /* 8 */ "Methods",
        /* 9 */ "Discussion",
        /* 10 */ "Conclusion",
        /* 11 */ "Here",
        /* 12 */ "In this work",
        /* 13 */ "In conclusion",
        /* 14 */ "These", // Not 'these'
        /* 15 */ "This study",
        /* 16 */ "this study",
        /* 17 */ "This review",
        /* 18 */ "this review",
        /* 19 */ "All things considered",
        /* 20 */ "Altogether",
        /* 21 */ "Given these points",
        /* 22 */ "As demonstrated above",
        /* 23 */ "As noted",
        /* 24 */ "As shown above",
        /* 25 */ "Briefly",
        /* 26 */ "By and large",
        /* 27 */ "In brief",
        /* 28 */ "In summary",
        /* 29 */ "To conclude",
        /* 30 */ "To summarise",
        /* 31 */ "To sum up",
        /* 32 */ "Ultimately",
        /* 33 */ "As you can see",
        /* 34 */ "Generally speaking",
        /* 35 */ "In a word",
        /* 36 */ "In any event",
        /* 37 */ "In essence",
        /* 38 */ "In short",
        /* 39 */ "In the end",
        /* 40 */ "In the final analysis",
        /* 41 */ "On the whole",
        /* 42 */ "Overall",
        /* 43 */ "Therefore",
        /* 44 */ "To end"
    );

    $ValuesAcronysms = array(
        /* 1 */ "The scientists",
        /* 2 */ "the authors",
        /* 3 */ "Them",
        /* 4 */ "them",
        /* 5 */ "The authors'",
        /* 6 */ "authors'",
        /* 7 */ "", // We remove "Introduction"
        /* 8 */ "", // We remove "Methods"
        /* 9 */ "", // We remove "Discussion"
        /* 10 */ "", // We remove "Conclusion"
        /* 11 */ "In a publication",
        /* 12 */ "",
        /* 13 */ "",
        /* 14 */ "Some",
        /* 15 */ "A study",
        /* 16 */ "a study",
        /* 17 */ "A review",
        /* 18 */ "a review",
        /* 19 */ "",
        /* 20 */ "",
        /* 21 */ "",
        /* 22 */ "",
        /* 23 */ "",
        /* 24 */ "",
        /* 25 */ "",
        /* 26 */ "",
        /* 27 */ "",
        /* 28 */ "",
        /* 29 */ "",
        /* 30 */ "",
        /* 31 */ "",
        /* 32 */ "",
        /* 33 */ "",
        /* 34 */ "",
        /* 35 */ "",
        /* 36 */ "",
        /* 37 */ "",
        /* 38 */ "",
        /* 39 */ "",
        /* 40 */ "",
        /* 41 */ "",
        /* 42 */ "",
        /* 43 */ "",
        /* 44 */ ""
    );

    $tyty = array_search($word, $KeyAcronysms);

    if ((isset($tyty)) && ($tyty !== false)) {
        $tata = $ValuesAcronysms[$tyty];
        return $tata;
    } else {
        return false;
    }
}

/*
 * Make a string's first character uppercase in multi-bytes strings
 */

function mb_ucfirst(string $str, string $encoding = null): string {
    if ($encoding === null) {
        $encoding = mb_internal_encoding();
    }
    return mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) . mb_substr($str, 1, null, $encoding);
}
