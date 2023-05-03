<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Add content
function add_my_content($title, $post_tag, $content, $description, $nameMD) {
    /* Deal with title */
    $post_title = "\n<!--t " . $title . " t-->";

    /* Deal with short description */
    $post_description = "\n<!--d " . $description . " d-->";

    /* Deal with tag */
    $posttag = "\n<!--tag " . $post_tag . " tag-->";

    /* Deal with content */
    $post_content = $post_title . $post_description . $posttag . "\n\n" . $content;
    //       echo '<br>title: ' . $title . ', post_tag: ' . $post_tag . ', pmid: ' . $pmid . ', post_content: ' . $post_content ;
    $post_content = stripslashes($post_content);
    $filename = '1956-11-03_aging_' . $nameMD . '.md';

    $dir = __DIR__ . '/../../../content/users/system/blog/English/post/';

    if (is_dir($dir)) {
        $resfpc = file_put_contents($dir . $filename, print_r($post_content, true));
        if ($resfpc === false) {
            echo '<br>Failure 1 in writing content at: ' . $dir;
            die();
        }
    } else {
        mkdir($dir, 0775, true);
        $resfpc = file_put_contents($dir . $filename, print_r($post_content, true));
        if ($resfpc === false) {
            echo '<br>Failure 2 in writing content at: ' . $dir;
            die();
        }
    }

    // https://padiracinnovation.org/News/2021/08/desiredurl
    // return date('Y/m') . '/' . $filename;
    return $filename;
}

function asInHTMLy($content0, $pmid) {

    $content1 = explode('<br>', $content0);
    if (isset($content1[1])) {
        $content2 = $content1[1];
    } else {
        $content2 = $content0;
    }
    $content3 = explode('<br><span class', $content2);
    if (isset($content3[0])) {
        $content4 = $content3[0];
    } else {
        $content4 = $content2;
    }

    $content5 = $content4 . "\n\n[Read the original article on Pubmed][1]\n\n";

    $url = '  [1]: https://www.ncbi.nlm.nih.gov/pubmed/' . $pmid;

    return $content5 . $url;
}
