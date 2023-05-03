<?php

// The front page of the blog
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// The front page of the blog which presents posts starting from last
// https://padiracinnovation.org/News/index
function frontpage_imp() {
    // Used with the search box (when POST)
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

        $tl = strip_tags(blog_tagline());

    if ($tl) {
        $tagline = ' - ' . $tl;
    } else {
        $tagline = '';
    }

    // a front page
    render('front', array(
        'title' => blog_title() . $tagline,
        'description' => strip_tags(blog_description()),
        'canonical' => site_url(),
        'bodyclass' => 'no-posts',
        'type' => 'is_frontpage',
        'is_front' => true,
    ));

    die;
}
