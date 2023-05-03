<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

/**
 * PubMedAPI
 * 
 * This is a standalone PHP wrapper around the PubMed API. Interaction with the PubMed server is
 * is accomplished in two steps. Given a search term, the first query returns a list of PMIDs.
 * The second query returns metadata of each article. This class executes both steps in from one
 * method.
 * 
 * https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&retmode=xml&retmax=30&retstartf=0&term=%22amyotrophic%22
 * ESearch: Search a text query in a single Entrez database.
 * ESummary: Retrieve document summaries for each UID.
 * EFetch: Retrieve full records for each UID.
 * 
 * Copyright (c) 2012 Asif Rahman
 * Asif Rahman <asiftr@gmail.com>
 * https://github.com/asifr
 */
define('PUBMEDAPI_VERSION', '1'); // Sat 10 Nov 2012

class PubMedAPI {

    public $retmax = 30; // Max number of results to return
    public $retstart = 0; // The search result number to start displaying data, useful for pagination
    public $count = 0; // Sets to the number of search results
    public $term = '';
    public $db = 'pubmed';
    public $retmode = 'xml';
    public $exact_match = true; // Exact match narrows the search results by wrapping in quotes
    public $mindate ;
    public $maxdate ;

    function __construct() {
     $this->mindate = date("Y/m/d", strtotime( '-7 days' )) ; // YYYY/MM/DD
     $this->maxdate = date("Y/m/d") ; // YYYY/MM/DD
	}

    // For accessing PubMed through proxy servers
    static public $curl_site_url = '';
    private $esearch = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?';
    private $efetch = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?';

    public function query($term, $compact = false, $callback = false) {
        $this->term = $term;
        if ($this->exact_match) {
            $this->term = urlencode(sprintf('"%s"', $this->term));
        }

        $xml = $this->pubmed_esearch($this->term);
	// echo '<br>xml: ' ; var_dump($xml) ;

        $this->count = (int) $xml->Count;

        // esearch returns a list of IDs so we have to concatenate the list and do an efetch
        $results = array();
        if (isset($xml->IdList->Id) && !empty($xml->IdList->Id)) {
            $ids = array();
            foreach ($xml->IdList->children() as $id) {
                $ids[] = (string) $id;
            }
	    // efetch done in query_pmid
            $results = $this->query_pmid(implode(',', $ids), $compact);
        }

        // Custom callback methods are executed and returned at this point.
        // Provides a single argument: the $results array
        if ($callback !== false) {
            return call_user_func($callback, $results);
        }

        return $results;
    }

    public function query_pmid($pmid, $compact = false) {
        $XML = $this->pubmed_efetch($pmid);
        return $this->parse($XML, $compact);
    }

    // Retuns an XML object
    protected function pubmed_esearch($term) {
        // Setup the URL for esearch
        $q = array();
        $params = array(
            'db' => $this->db,
            'retmode' => $this->retmode,
            'retmax' => $this->retmax,
//            'sort' => 'most+recent',
            'retstartf' => $this->retstart,
            'mindate' => $this->mindate,
            'maxdate' => $this->maxdate,
            'term' => str_replace(['%2522', '%255D', '%255B', '%2529', '%2528', '%2B'], ['%22', ']', '[', ')', '(', '+'], stripslashes(urlencode($term)))
        );
        foreach ($params as $key => $value) {
            $q[] = $key . '=' . $value;
        }
        $httpquery = implode('&', $q);
 
        $url = $this->esearch . $httpquery;
        // var_dump($url) ;

        $XML = self::proxy_simplexml_load_file($url); // results of esearch, XML formatted
//        echo '<br>url: ' ; var_dump($url) ;
//        echo '<br>XML: ' ; var_dump($XML) ;
        return $XML;
    }

    // Returns an XML object
    protected function pubmed_efetch($pmid) {
        // Setup the URL for efetch
        $params = array(
            'db' => $this->db,
            'retmode' => $this->retmode,
            'retmax' => $this->retmax,
            'id' => (string) $pmid
        );
        $q = array();
        foreach ($params as $key => $value) {
            $q[] = $key . '=' . $value;
        }
        $httpquery = implode('&', $q);
        $url = $this->efetch . $httpquery;
//	echo '<br>url125: ' ; var_dump($url) ;
        $XML = self::proxy_simplexml_load_file($url);
        return $XML;
    }

    public function parse($xml, $compact = false) {
        $data = array();
        foreach ($xml->PubmedArticle as $art) {
            if ($compact) {
                // Compact
                $data[] = array(
                    'pmid' => (string) $art->MedlineCitation->PMID,
                    'volume' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->Volume,
                    'issue' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->Issue,
                    'year' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->PubDate->Year,
                    'month' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->PubDate->Month,
                    'journal' => (string) $art->MedlineCitation->Article->Journal->Title,
                    'journalabbrev' => (string) $art->MedlineCitation->Article->Journal->ISOAbbreviation,
                    'title' => (string) $art->MedlineCitation->Article->ArticleTitle,
                );
            } else {
                // Full metadata
                // Authors array contains concatendated LAST NAME + INITIALS
                $authors = array();
                if (isset($art->MedlineCitation->Article->AuthorList->Author)) {
                    try {
                        foreach ($art->MedlineCitation->Article->AuthorList->Author as $k => $a) {
                            $authors[] = (string) $a->LastName . ' ' . (string) $a->Initials;
                        }
                    } catch (Exception $e) {
                        $a = $art->MedlineCitation->Article->AuthorList->Author;
                        $authors[] = (string) $a->LastName . ' ' . (string) $a->Initials;
                    }
                }

                // Keywords array
                $keywords = array();
                if (isset($art->MedlineCitation->MeshHeadingList->MeshHeading)) {
                    foreach ($art->MedlineCitation->MeshHeadingList->MeshHeading as $k => $m) {
                        $keywords[] = (string) $m->DescriptorName;
                        if (isset($m->QualifierName)) {
                            if (is_array($m->QualifierName)) {
                                $keywords = array_merge($keywords, $m->QualifierName);
                            } else {
                                $keywords[] = (string) $m->QualifierName;
                            }
                        }
                    }
                }

                // Article IDs array
                $articleid = array();
                if (isset($art->PubmedData->ArticleIdList)) {
                    foreach ($art->PubmedData->ArticleIdList->ArticleId as $id) {
                        $articleid[] = $id;
                    }
                }


                $data[] = array(
                    'pmid' => (string) $art->MedlineCitation->PMID,
                    'volume' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->Volume,
                    'issue' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->Issue,
                    'year' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->PubDate->Year,
                    'month' => (string) $art->MedlineCitation->Article->Journal->JournalIssue->PubDate->Month,
                    'pages' => (string) $art->MedlineCitation->Article->Pagination->MedlinePgn,
                    'issn' => (string) $art->MedlineCitation->Article->Journal->ISSN,
                    'journal' => (string) $art->MedlineCitation->Article->Journal->Title,
                    'journalabbrev' => (string) $art->MedlineCitation->Article->Journal->ISOAbbreviation,
                    'title' => (string) $art->MedlineCitation->Article->ArticleTitle,
                    'abstract' => (string) $art->MedlineCitation->Article->Abstract->AbstractText,
                    'affiliation' => (string) $art->MedlineCitation->Article->Affiliation,
                    'authors' => $authors,
                    'articleid' => implode(',', $articleid),
                    'keywords' => $keywords
                );
            }
        }
        return $data;
    }

    public static function proxy_simplexml_load_file($url) {
        $xml_string = '';
        if (isset(self::$proxy_name) && !empty(self::$proxy_name)) {
            $proxy_fp = fsockopen(self::$proxy_name, self::$proxy_port);
            if ($proxy_fp) {
                fputs($proxy_fp, "GET " . $url . " HTTP/1.0\r\nHost: " . self::$proxy_name . "\r\n");
                fputs($proxy_fp, "User-Agent: \"$_SERVER[HTTP_USER_AGENT]\"\r\n");
                fputs($proxy_fp, "Proxy-Authorization: Basic " . base64_encode(self::$proxy_username . ":" . self::$proxy_password) . "\r\n\r\n");

                while (!feof($proxy_fp)) {
                    $xml_string .= fgets($proxy_fp, 128);
                }

                fclose($proxy_fp);
                $xml_string = strstr($xml_string, "<?xml");
                $xml = simplexml_load_string($xml_string);
                #JSTOR hack
                if (empty($xml) && strpos($url, 'jstor') !== false) {
                    $xml = new XMLReader();
                    $xml->xml($xml_string);
                }
            }
        } else {
            ini_set('user_agent', $_SERVER['HTTP_USER_AGENT']);
            $xml = self::load_xml_from_url($url);
            #JSTOR hack
            if (empty($xml) && strpos($url, 'jstor') !== false) {
                $xml = new XMLReader();
                $xml->open($url);
            }
        }
        return $xml;
    }

    public static function load_file_from_url($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_REFERER, self::$curl_site_url);
        $str = curl_exec($curl);
        curl_close($curl);
        return $str;
    }

    public static function load_xml_from_url($url) {
        return simplexml_load_string(self::load_file_from_url($url));
    }
}

?>
