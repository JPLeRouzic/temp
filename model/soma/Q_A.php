<?php

// Define the search terms and API endpoints
$terms = $_GET['terms'];
$wiki_url = "https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&exsentences=3&exlimit=10&explaintext=1&exintro=1&redirects=1&titles=" . urlencode($terms);
$pubmed_url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=" . urlencode($terms) . "&retmode=json";

// Fetch data from Wikipedia API
$wiki_json = file_get_contents($wiki_url);
$wiki_data = json_decode($wiki_json, true);

// Fetch data from Pubmed API
$pubmed_json = file_get_contents($pubmed_url);
$pubmed_data = json_decode($pubmed_json, true);

// Parse the Wikipedia results and output relevant text snippets as HTML
echo "<h1>Results for \"$terms\"</h1>";
echo "<h2>Wikipedia results</h2>";
foreach ($wiki_data['query']['pages'] as $page) {
    if (isset($page['extract'])) {
        $extract = $page['extract'];
        $snippets = array();
        preg_match_all('/(?:[^\.\?\!]|(?:\.(?=\d)))+[\.\?\!]/', $extract, $sentences); // split into sentences
        foreach ($sentences[0] as $sentence) {
            if (preg_match('/\b' . preg_quote($terms) . '\b/i', $sentence)) { // check if search terms appear in sentence
                $snippets[] = $sentence;
            }
        }
        if (!empty($snippets)) {
            echo "<h3><a href=\"https://en.wikipedia.org/wiki/" . urlencode($page['title']) . "\">" . $page['title'] . "</a></h3>";
            foreach ($snippets as $snippet) {
                echo "<p>" . $snippet . "</p>";
            }
        }
    }
}

// Parse the Pubmed results and output relevant text snippets as HTML
echo "<h2>Pubmed results</h2>";
foreach ($pubmed_data['esearchresult']['idlist'] as $id) {
    $pubmed_summary_url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=pubmed&id=" . $id . "&retmode=json";
    $pubmed_summary_json = file_get_contents($pubmed_summary_url);
    $pubmed_summary_data = json_decode($pubmed_summary_json, true);
    $pubmed_title = $pubmed_summary_data['result'][$id]['title'];
//    var_dump($pubmed_summary_data['result'][$id]) ;
//    die() ;
//    $pubmed_summary = $pubmed_summary_data['result'][$id]['summary'];
    /*
     * array(43) { 
     * ["uid"]=> string(8) "36951082" 
     * ["pubdate"]=> string(11) "2023 Mar 25" 
     * ["epubdate"]=> string(0) "" 
     * ["source"]=> string(15) "Zhen Ci Yan Jiu" 
     * ["authors"]=> array(5) { [0]=> array(3) { ["name"]=> string(7) "Wang SL" ["authtype"]=> string(6) "Author" ["clusterid"]=> string(0) "" } [1]=> array(3) { ["name"]=> string(6) "Sun YZ" ["authtype"]=> string(6) "Author" ["clusterid"]=> string(0) "" } [2]=> array(3) { ["name"]=> string(5) "Yu TY" ["authtype"]=> string(6) "Author" ["clusterid"]=> string(0) "" } [3]=> array(3) { ["name"]=> string(7) "Zhao GR" ["authtype"]=> string(6) "Author" ["clusterid"]=> string(0) "" } [4]=> array(3) { ["name"]=> string(5) "Sun Y" ["authtype"]=> string(6) "Author" ["clusterid"]=> string(0) "" } } ["lastauthor"]=> string(5) "Sun Y" ["title"]=> string(154) "[Early electroacupuncture intervention delays progression of disease in mice with amyotrophic lateral sclerosis by down-regulating TLR4/NF-κB signaling]." ["sorttitle"]=> string(149) "early electroacupuncture intervention delays progression of disease in mice with amyotrophic lateral sclerosis by down regulating tlr4 nf b signaling" ["volume"]=> string(2) "48" ["issue"]=> string(1) "3" ["pages"]=> string(6) "287-93" ["lang"]=> array(1) { [0]=> string(3) "chi" } ["nlmuniqueid"]=> string(7) "8507710" ["issn"]=> string(9) "1000-0607" ["essn"]=> string(0) "" ["pubtype"]=> array(1) { [0]=> string(15) "Journal Article" } ["recordstatus"]=> string(33) "PubMed - as supplied by publisher" ["pubstatus"]=> string(1) "4" ["articleids"]=> array(2) { [0]=> array(3) { ["idtype"]=> string(6) "pubmed" ["idtypen"]=> int(1) ["value"]=> string(8) "36951082" } [1]=> array(3) { ["idtype"]=> string(3) "doi" ["idtypen"]=> int(3) ["value"]=> string(29) "10.13702/j.1000-0607.20211379" } } ["history"]=> array(3) { [0]=> array(2) { ["pubstatus"]=> string(6) "entrez" ["date"]=> string(16) "2023/03/23 05:03" } [1]=> array(2) { ["pubstatus"]=> string(6) "pubmed" ["date"]=> string(16) "2023/03/24 06:00" } [2]=> array(2) { ["pubstatus"]=> string(7) "medline" ["date"]=> string(16) "2023/03/24 06:00" } } ["references"]=> array(0) { } ["attributes"]=> array(1) { [0]=> string(12) "Has Abstract" } ["pmcrefcount"]=> string(0) "" ["fulljournalname"]=> string(38) "Zhen ci yan jiu = Acupuncture research" ["elocationid"]=> string(34) "doi: 10.13702/j.1000-0607.20211379" ["doctype"]=> string(8) "citation" ["srccontriblist"]=> array(0) { } ["booktitle"]=> string(0) "" ["medium"]=> string(0) "" ["edition"]=> string(0) "" ["publisherlocation"]=> string(0) "" ["publishername"]=> string(0) "" ["srcdate"]=> string(0) "" ["reportnumber"]=> string(0) "" ["availablefromurl"]=> string(0) "" ["locationlabel"]=> string(0) "" ["doccontriblist"]=> array(0) { } ["docdate"]=> string(0) "" ["bookname"]=> string(0) "" ["chapter"]=> string(0) "" ["sortpubdate"]=> string(16) "2023/03/25 00:00" ["sortfirstauthor"]=> string(7) "Wang SL" ["vernaculartitle"]=> string(0) "" }
     */
    
// Construct the URL for the PubMed API request
$url = "https://api.ncbi.nlm.nih.gov/lit/ctxp/v1/pmp/?format=citation&id=" . $pubmed_summary_data['result'][$id]['uid'] . "&api_key=0e9bcf97b7a590d16e116965832208c24608";

// Make the API request and get the response as JSON
$response = file_get_contents($url);

// Decode the JSON response into an array
$data = json_decode($response, true);

// Get the abstract from the response
$pubmed_summary = $data['data']['attributes']['abstract'];
    
    
    $snippets = array();
    preg_match_all('/(?:[^\.\?\!]|(?:\.(?=\d)))+[\.\?\!]/', $pubmed_summary, $sentences); // split into sentences
    foreach ($sentences[0] as $sentence) {
        if (preg_match('/\b' . preg_quote($terms) . '\b/i', $sentence)) { // check if search terms appear in sentence
            $snippets[] = $sentence;
        }
    }
        if (!empty($snippets)) {
            echo "<h3><a href=\"https://en.wikipedia.org/wiki/" . urlencode($page['title']) . "\">" . $page['title'] . "</a></h3>";
            foreach ($snippets as $snippet) {
                echo "<p>" . $snippet . "</p>";
            }
        }
    }

