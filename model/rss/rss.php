<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/* * ** RSS & OPML *** */

// Show the RSS feed
function feed_rss() {
    header('Content-Type: application/rss+xml');
    // Show an RSS feed with the 30 latest posts
    echo generate_rss(get_posts(null, 1, config('rss.count')));
}

// Generate OPML file
function feed_opml() {
    header('Content-Type: text/xml');
    // Generate OPML file for the RSS
    echo generate_opml();
}
