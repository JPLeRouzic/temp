<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the author page
// https://padiracinnovation.org/News/author/admin
function author_name(string $name) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int) $page1 : 1;
    $perpage = config('profile.perpage');

    $posts = get_profile_posts($name, $page, $perpage);

    $total = get_count($name, 'dirname');

    $author = get_author($name);

    if (!isset($author)) {
        $author = default_profile($name);
    }

    $vroot = rtrim(config('views.root'), '/');

    $pv = $vroot . '/profile--' . strtolower($name) . '.html.php';
    if (file_exists($pv)) {
        $pview = 'profile--' . strtolower($name);
    } else {
        $pview = 'profile';
    }

    if (empty($posts) || $page < 1) {
        render($pview, array(
            'title' => 'Profile for:  ' . $author->name . ' - ' . blog_title(),
            'description' => 'Profile page and all posts by ' . $author->name . ' on ' . blog_title() . '.',
            'canonical' => site_url() . 'author/' . $name,
            'page' => $page,
            'posts' => null,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_profile',
            'bodyclass' => 'in-profile author-' . $name,
            'pagination' => has_pagination($total, $perpage, $page),
            'is_profile' => true,
                ));
        die;
    }

    render($pview, array(
        'title' => 'Profile for:  ' . $author->name . ' - ' . blog_title(),
        'description' => 'Profile page and all posts by ' . $author->name . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'author/' . $name,
        'page' => $page,
        'posts' => $posts,
        'about' => $author->about,
        'name' => $author->name,
        'type' => 'is_profile',
        'bodyclass' => 'in-profile author-' . $name,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_profile' => true,
            ) );
}
