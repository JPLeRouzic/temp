<?php
// Show news search engine page

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/* Show the archive page */
function archive_req($req) {
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int)$page1 : 1;
    $perpage = config('archive.perpage');

    $posts = get_archive($req, $page, $perpage);
// var_dump($posts) ;
// die() ;
    $total = get_count($req, 'basename');

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found('This is a non existing archive');
    }

    $time = explode('-', $req);
    $date = strtotime($req);

    if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
        $timestamp = date('d F Y', $date);
    } elseif (isset($time[0]) && isset($time[1])) {
        $timestamp = date('F Y', $date);
    } else {
        $timestamp = $req;
    }
    
    $tarchive = new Archive($timestamp);

    if (!$date) {
        // a non-existing page
        not_found('archive 44');
    }
    
    $vroot = rtrim(config('views.root'), '/');
        
    $pv = $vroot . '/main--archive.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--archive';
    } else {
        $pview = 'main';
    }

    render($pview, array(
        'title' => 'Archive for: ' . $timestamp . ' - ' . blog_title(),
        'description' => 'Archive page for: ' . $timestamp . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'archive/' . $req,
        'page' => $page,
        'posts' => $posts,
        'archive' => $tarchive,
        'bodyclass' => 'in-archive archive-' . strtolower($req),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_archive' => true,
    ));
}

