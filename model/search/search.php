<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the search page
function search_keyword($keyword) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int)$page1 : 1;
    $perpage = config('search.perpage');

    $posts = get_keyword($keyword, $page, $perpage);
    
    $tsearch = new Search() ;
    $tsearch->title = $keyword;
    
    $vroot = rtrim(config('views.root'), '/');
    
    if (!$posts || $page < 1) {
        // a non-existing page or no search result
        render('404-search', array(
            'title' => 'Search results not found! - ' . blog_title(),
            'description' => 'Search results not found!',
            'search' => $tsearch,
            'keyword404' =>  $keyword,
            'canonical' => site_url(),
            'bodyclass' => 'error-404-search',
            'is_404search' => true,
        ));
        die;
    }

    $total = keyword_count($keyword);
    
    $pv = $vroot . '/main--search.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--search';
    } else {
        $pview = 'main';
    }

    render($pview, array(
        'title' => 'Search results for: ' . tag_i18n($keyword) . ' - ' . blog_title(),
        'description' => 'Search results for: ' . tag_i18n($keyword) . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'search/' . strtolower($keyword),
        'page' => $page,
        'posts' => $posts,
        'search' => $tsearch,
        'bodyclass' => 'in-search search-' . strtolower($keyword),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Search results for: ' . tag_i18n($keyword),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_search' => true,
    ));
}

