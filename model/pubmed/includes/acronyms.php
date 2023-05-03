<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function acronyms(string $abstract, &$tags) {
    $words = myword_tokenize($abstract);
//    var_dump($words) ;

    foreach ($words as $word) {
        // Is it an acronym?
        $substitute = isWordAcronym($word);

        if ($substitute !== false) {
            // It is an acronym, so replace it by its abbrevation in $abstract
            $abstract = str_replace($word, $substitute, $abstract);
            $tags[] = $word ;
        }
    }
//    var_dump($abstract) ;
    return $abstract;
}

function myword_tokenize($sentence) {
    //Splits text into words and also does some cleanup.
    $words = preg_split('/[\'\s\r\n\t$]+/', $sentence);
    $rez = array();
    foreach ($words as $word) {
        $word = preg_replace('/(^[^a-z0-9]+|[^a-z0-9]$)/i', '', $word);
        if (strlen($word) > 0) {
            array_push($rez, $word);
        }
    }
    return $rez;
}

// Is it an acronym?    
function isWordAcronym($word) {
    $KeyAcronysms = array(
        "AD",
        "PD",
        "ALS",
        "DLB",
        "GDS",
        "Αβ",
        "ADHD",
        "AIDS",
        "CJD",
        "COPD",
        "COVID",
        "DM",
        "DMD",
        "FTD",
        "GBS",
        "IBM",
        "MND",
        "MS",
        "NBIA",
        "OCD",
        "PLS",
        "PSP",
        "SMA",
        "CNS",
        "CSF",
        "IV"
    );

    $ValuesAcronysms = array(
        "Alzheimer's disease",
        "Parkinson disease",
        "Amyotrophic Lateral Sclerosis",
        "Dementia with Lewy Bodies",
        "Global Deterioration Scale",
        "beta-amyloid",
        "Attention deficit hyperactivity disorder",
        "Acquired immune deficiency syndrome",
        "Creutzfeldt–Jakob disease",
        "Chronic obstructive pulmonary disease",
        "Coronavirus disease",
        "Diabetes mellitus",
        "Duchenne muscular dystrophy",
        "Frontotemporal dementia",
        "Guillain–Barré syndrome",
        "Inclusion body myositis",
        "Motor neuron disease",
        "Multiple sclerosis",
        "Neurodegeneration with brain iron accumulation",
        "Obsessive-compulsive disorder",
        "Primary lateral sclerosis",
        "Progressive supranuclear palsy",
        "Spinal muscular atrophy",
        "central nervous system",
        "cerebrospinal fluid",
        "intravenous"
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
