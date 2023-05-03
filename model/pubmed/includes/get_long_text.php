<?php

/*
 * An article must not be a review, and be about in-vivo experiments
 * There is a score which depends on keywords in the abstract
 * The score must be > 100
 * It sets $isreview, and returns the abstract
 */

function isContentReview(string $pmid, string $pmidabstract, bool &$isreview): string {
    $score = 100 ;

    // Studies must be about meaningful stuff, not in_vitro or gene expression or review
    // Is it a review?
    if (stristr($pmidabstract, 'Review')) {
        $score += -1;
        return $pmidabstract ;
    }
    if (stristr($pmidabstract, 'in-vivo')) {
        $score += +1;
    } else
    if (stristr($pmidabstract, 'in-vitro')) {
        $score += -1;
    } else
    if ((stristr($pmidabstract, 'animal')) || (stristr($pmidabstract, 'mice')) || (stristr($pmidabstract, 'zebrafish')) || (stristr($pmidabstract, 'elegans'))) {
        $score += -1;
    } else
    if ((stristr($pmidabstract, 'clinical')) || (stristr($pmidabstract, 'cohort')) || (stristr($pmidabstract, 'patient')) || (stristr($pmidabstract, 'hospital'))) {
        $score += +1;
    } else
    if ((stristr($pmidabstract, 'Mendelian randomization')) || (stristr($pmidabstract, 'diagnosis'))) {
        $score += +1;
    } else
    if ((stristr($pmidabstract, 'computational')) || (stristr($pmidabstract, 'Behavioural'))) {
        $score += +1;
    } 
    
    if($score < 101) {
        $isreview = true ;
        return '' ;
    } else {
        $isreview = false ;
    }

    // Remove content inside parenthesis
    $paren_num = 0;
    $new_string = '';
    $textlength = strlen($pmidabstract);
    for ($idx = 0; $idx < $textlength; $idx++) {
        // We remove the white space before the first left parenthesis
        if (($pmidabstract[$idx] == '(') && ($paren_num == 0)) {
            if ($pmidabstract[$idx - 1] == ' ') {
                $new_string = substr($new_string, 0, strlen($new_string) - 1);
            }
            $paren_num++;
        } else if ($pmidabstract[$idx] == ')') {
            $paren_num--;
        } else if ($paren_num <= 0) {
            $new_string .= $pmidabstract[$idx];
        }
    }
    $new_string1 = trim($new_string);

    return $new_string1;
}

function getTitle(string $pmid): string {
    $url = "https://www.ncbi.nlm.nih.gov/pubmed/" . $pmid;
    $rawtxt = (string) file_get_contents($url);

    $text1 = explode('class="heading-title">', $rawtxt);
    if (sizeof($text1) < 3) {
        $text2 = explode('</a>', $text1[1]);
        $title = strip_tags($text2[0]);
    } else {
        echo '<br>error 2';
        var_dump($rawtxt);
    }

    return $title;
}

// Cosmetic changes just before publication
function postFilterAll(string $cacheStringAll): string {
    $str = str_replace('<br><br><br><br>', "<br><br>", $cacheStringAll);

    return $str;
}
