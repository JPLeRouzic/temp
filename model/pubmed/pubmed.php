<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once 'includes/summarizer.php';
require_once 'includes/html_functions.php';
require_once 'includes/PubMedAPI.php';
require_once 'includes/get_long_text.php';
require_once 'includes/newSysPost.php';
require_once 'includes/acronyms.php';
require_once 'includes/rewriting.php';

//static $nbcachefiles = 0; // Number of pmid files in cache

/*
 * It either get Pubmed articles from a cache or from Pubmed's API
 * It returns array('title' => $pmidTitle, 'url' => $pmidURL, 'summary' => $summary);
 * For speed, it first list the cache where possibly are stored those articles.
 *      Each cached article is stored as a serialized array ($pmidTitle, $pmidURL, $summary)
 *      There is a date for the "freshness" of the cache.
 *          - It can be used to detect when the cache content is too old and there is a need to "refresh" it.
 *      There must be at least 5 valid articles in the cache.
 * If there is 5 "fresh" cached articles:
 *      They are returned in one array (array of array)
 * 
 * If the cache is not "fresh":
 *      We empty the cache
 *      We fetch a number of articles from Pubmed and apply several criteria to select 5.
 *      These 5 articles are stored in the cache
 *      These 5 articles are returned in one array (array of array)
 * 
 * These five articles have a fixed URL each, so they are overwritten at each new publication.
 * This is to give search engines the impression there are always something new happening
 * It's also because we want to have time to have oversight over the automatic creation of articles
 */

function get_pubmed(): array {
    static $cache_life = 86400; // Caching time, in seconds, default 1 day 
    static $cache_folder = 'content/pubmed/'; // Cache content
    $summarizer = new Summarizer();

    $cachePMIDs = array();

    // Read PMIDs in the cache, if too old, delete content
    readpmidcache($cache_folder, $cache_life, $cachePMIDs);

    $nbValidPMIDs1 = count($cachePMIDs);
    if ($nbValidPMIDs1 >= 5) {
        /*
         * The cache is enough "fresh":
         * 
         * $nbValidPMIDs1 >= 5 so there is enough valid PMIDs in cache 
         * Only keep 5 entries in $results
         * 
         */
        $results = array_slice($cachePMIDs, 0, 5);
        // We must return Post!
    
        return $results;
    } else {
        /*
         * The cache is not "fresh":
         * 
         *      We fetch a number of articles from Pubmed and apply several criteria to select 5.
         *      These 5 articles are stored in the cache
         *      These 5 articles are returned in one array (array of array)
         */
        $indx = 3; // 3 attemps at reading Pubmed at most
        while ($indx > 0) {
            // Make a PubMedAPI request to replace cache invalidated pmids.
            $PubMedAPI = new PubMedAPI();
            // Total number of unique identifiers (UIDs) from the retrieved set to be shown in the output (default=20)
            $PubMedAPI->retmax = 30;
            $results = $PubMedAPI->query('"Amyotrophic OR Alzheimer OR Parkinson OR Aging OR neurogenesis OR TDP-43 OR UPR"', false);
            if (!empty($results)) {
                break;
            }
            sleep(5);
            $indx--;
        }
    }

    /*
     * results: array(30) {
      .  [0]=>
      .  array(15) {
      .    	["pmid"]=>
      .    		string(8) "36922318"
      .    	["volume"]=>
      .    		string(0) ""
      .    	["issue"]=>
      .    		string(0) ""
      .    	["year"]=>
      .    		string(4) "2023"
      .    	["month"]=>
      .    		string(3) "Feb"
      .    	["pages"]=>
      .    		string(0) ""
      .    	["issn"]=>
      .    		string(9) "1545-7214"
      .    	["journal"]=>
      .    		string(116) "The American journal of geriatric psychiatry : official journal of the American Association for Geriatric Psychiatry"
      .    	["journalabbrev"]=>
      .    		string(23) "Am J Geriatr Psychiatry"
      .    	["title"]=>
      .    		string(260) "Corrigendum to Optimizing Outcomes of Treatment-Resistant Depression in Older Adults (OPTIMUM): Study Design and Treatment Characteristics of the First 396 Participants Randomized. Am J Geriatr Psychiatry 2019;27(10):1138-1152. doi: 10.1016/j.jagp.2019.04.005."
      .    	["abstract"]=>
      .    		string(0) ""
      .    	["affiliation"]=>
      .    		string(0) ""
      .    	["authors"]=>
      .    array(13) {
      .    		[0]=>
      .      		    string(12) "Cristancho P"
      .    		[1]=>
      .      		    string(8) "Lenard E"
      .    		[2]=>
      .      		    string(8) "Lenze EJ"
      .    		[3]=>
      .      		    string(9) "Miller JP"
      .    		[4]=>
      .      		    string(8) "Brown PJ"
      .    		[5]=>
      .      		    string(8) "Roose SP"
      .    		[6]=>
      .      		    string(15) "Montes-Garcia C"
      .    		[7]=>
      .      		    string(13) "Blumberger DM"
      .    		[8]=>
      .      		    string(10) "Mulsant BH"
      .    		[9]=>
      .      		    string(11) "Lavretsky H"
      .    		[10]=>
      .      		    string(10) "Rollman BL"
      .    		[11]=>
      .      		    string(11) "Reynolds CF"
      .    		[12]=>
      .      		    string(7) "Karp JF"
      .        }
      .    	["articleid"]=>
      .    		string(57) "36922318,10.1016/j.jagp.2023.02.042,S1064-7481(23)00216-6"
      .    	["keywords"]=>
      .    array(0) {
      .    }
      .  }
      .  [1]=>
     */

    // Fill $cacheArrayAll with the new content
    // We remove PMIDs that look not interesting
    $nameMD = 1 ;
    foreach ($results as $result) {

        // Search if this article has already been posted on PI's platform
        $path = 'content/users/system/blog/english/post/' . $result['pmid'];
        if (is_file($path)) {
            echo $result['pmid'] . ' is already posted';
            break;
        }

        // Sometimes API can't get the title
        if (strlen($result['title']) < 8) {
            // $result['title'] = getTitle($result['pmid']) ;
            continue;
        }

        // Get rid of too short abstracts
        if (strlen($result['abstract']) < 40) {
            // There is no abstract, skip it.
            continue;
        }

        $isreview = true;
        // An article must not be a review, and be about in-vivo experiments
        $abstract = isContentReview($result['pmid'], $result['abstract'], $isreview);
        if ($isreview == true) {
            // This is a review, skip it.
            continue;
        }

        $url = site_url() . '1956/11/' . $nameMD ;

        // Store $result in $cachePMIDs
        cachePMIDinMemory($result, $abstract, $summarizer, $cachePMIDs, $nameMD, $url);
        $nameMD++ ;
    } // foreach ($results as $result)
    
    // We keep only 5 PMIDs
    $nbValidPMIDs2 = count($cachePMIDs);
    if ($nbValidPMIDs2 >= 5) {
        // $nbValidPMIDs2 >= 5 so there is enough valid PMIDs in cache 
        // Only keep 5 entries in $cachePMIDs
        $cachePMIDs = array_slice($cachePMIDs, 0, 5);
    }
    
//    $urlpart = 0 ;
    // Now store the new pmids in the file cache and write a post
    foreach ($cachePMIDs as $pmid => $val) {
    
        $dir = __DIR__ . '/../../content/pubmed/';

        // Cache this abstract for this pmid
        $val1 = serialize($val);
        file_put_contents($dir . $val['pmid'], $val1);
        // Update the cache timestamp
        file_put_contents($dir . 'date.txt', '');

        /*
         *  Make these articles posts of the blog
         */
        // Modify the formatting of the cached article to make it conform to HTMLy format
        $content = asInHTMLy($val['content'], $val['pmid']);

        // hard code category
        $category = 'english';
        // Create description
        $description1 = $summarizer->summary($content, 0, 0, 50, 2, 4);
        $description = implode(' ', $description1);

        // convert tag array to string
        $tags = implode(', ', $val['tags']);
        if (empty($tags)) {
            $tags = 'Aging';
        }

//        $urlpart++ ;
//        /* Deal with desired URL */
//        $target_URL = preg_replace("/[^A-Za-z0-9]/", '-', (string) $urlpart);

        // Create a new post in DB and incorporate the URL of this new post
        add_my_content($val['title'], $tags, $content, $description, $val['nameMD']/*, $val['date'], $val['url']*/);

//        $date = date('Y-m', time()) ;
//        $cachePMIDs[$pmid]['date'] = $date ;
        // A post URL is site_url() . year . month . name_md
//        $cachePMIDs[$pmid]['url'] = site_url() . '1956/11/' . $target_URL;
    }

    return $cachePMIDs;
}

/*
 * It first list the cache where possibly are stored those articles.
 *      Each cached article is stored as a serialized array ($pmidTitle, $pmidURL, $summary)
 *      There is a date for the "freshness" of the cache ("date.txt").
 *          - It can be used to detect when the cache content is too old and there is a need to "refresh" it.
 *          - If the cache is invalid, then delete all content
 *      There must be at least 5 valid articles in the cache.
 *  Push the cached articles in $cachePMIDs
 * 
 */

function readpmidcache($cache_folder, $cache_life, &$cachePMIDs) {
    // open the cache directory 
    $myDirectory = opendir($cache_folder);

    // Test if cache is obsolete with file modification time
    $filemtime = filemtime($cache_folder . "date.txt");
    if (($filemtime != false) && ((time() - $cache_life) > $filemtime)) {
        // It is obsolete, so remove the files
        while (($cached_pmid = readdir($myDirectory)) && ($cached_pmid !== false)) {
            if ((strcmp($cached_pmid, '.') == 0) || (strcmp($cached_pmid, '..') == 0)) {
                continue;
            }
            unlink($cache_folder . $cached_pmid);
        }
    } else {
        // As this cache is not obsolete, read its content and add it to $cachePMIDs.
        while (($cached_pmid = readdir($myDirectory)) && ($cached_pmid !== false)) {
            if ((strcmp($cached_pmid, '.') == 0) || 
                    (strcmp($cached_pmid, '..') == 0) ||
                    (strcmp($cached_pmid, 'data.txt') == 0)
                    ) {
                continue;
            }
            $cacheValid1 = file_get_contents($cache_folder . $cached_pmid);

            if ($cacheValid1 !== false) {
                $cacheValid = unserialize($cacheValid1);
                if(!$cacheValid) {
                     continue ;
                 }

                // Update $cachePMIDs with the new article
                $cachePMIDs[] = $cacheValid;
            }
        }
    }
    // close the cache directory
    closedir($myDirectory);
}

// Fill $cachePMIDs
function cachePMIDinMemory($result, $abstract, $summarizer, &$cachePMIDs, $nameMD, $url) {
//    $pmidURL = 'https://www.ncbi.nlm.nih.gov/pubmed/' . $result['pmid'];
    $pmidTitle = $result['title'];

    //replace some Unicode characters with ASCII
    $text = normalizeHtml($abstract);

    // Call the summarizer only if the summary is very long
    if (strlen($text) > 1200) {
        //generate the summary, starting two sentences after beginning, and ending two sentences before end
        $res = $summarizer->summary($text, 0, 0, 50, 5, 15);

        //$rez is an array of sentences. Turn it into contiguous text by using implode().
        $text = implode(' ', $res);
    }

    // Develop acronyms and find tags
    $tags1 = array();
    $summary2 = acronyms($text, $tags1);
    $tags = array_unique($tags1);

    if (empty($tags)) {
        $tags[] = 'Aging';
    }

    // Rewrite from first to third person
    $summary4 = rewritings($summary2);

    // Make some cosmetic tuning
    $content = postFilterAll($summary4);

    // Find date of creation
    //        $filename = $post_date . '_' . $post_tag . '_' . $target_URL . '.md';
    // $date = explode('_', $pmidURL);
    // Write the body of the post in the cache in memory
//    echo "<br>result['pmid']: " . $result['pmid'] ;
    $uno = array(
        'pmid' => $result['pmid'],
        'nameMD' => $nameMD,
        'title' => $pmidTitle,
        'url' => $url, 
        'img_url' => get_picture_URL($content),
        'date' => date('Y-m-d', time()) ,
        'tags' => $tags,
        'content' => $content);
     $cachePMIDs[] = $uno ;
}
