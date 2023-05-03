<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the add tag
function get_add_tag() {
    if (is_logged()) {

        config('views.root', 'views/admin/views');

        render('add-page', array(
            'title' => 'Add tags - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_tag',
            'is_admin' => true,
            'bodyclass' => 'add-tag'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Submitted add tag 
function post_add_tag() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content) && is_logged()) {
        // Everything looks OK, then let's create this tag
        if (empty($url)) {
            $url = $title;
        }
        add_tag($title, $url, $content, $description);
    } else {
        // Something is wrong
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'views/admin/views');
        render('add-page', array(
            'title' => 'Add tag - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_tag',
            'is_admin' => true,
            'bodyclass' => 'add-tag'
        ));
    }
}

// Show tag page
function get_admin_tags() {
    if (is_logged()) {
        config('views.root', 'views/admin/views');
        // see tags.html.php
        render('tags', array(
            'title' => 'Tags - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-tags',
            'is_admin' => true,
            'bodyclass' => 'admin-tags'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

// Show the tag page
function get_show_tag(string $tagS) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int) $page1 : 1;
    $perpage = config('tag.perpage');
    $ttag = new Tag($tagS);

    if (empty($perpage)) {
        $perpage = 10;
    }

    $tag = new Tag($tagS);

    $posts = get_tags($tag, $page, $perpage);
//    $posts = get_related($tag, $perpage);
    $total = get_tagcount($tag);

    if (empty($posts) || $page < 1) {
        // a non-existing page
       not_found('get_show_tag');
    }

    $vroot = rtrim(config('views.root'), '/');

    $pv = $vroot . '/main--tag--' . strtolower($tag->value) . '.html.php';
    $ps = $vroot . '/main--tag.html.php';
    if (file_exists($pv)) {
        $pview = 'main--tag--' . strtolower($tag->value);
    } else if (file_exists($ps)) {
        $pview = 'main--tag';
    } else {
        $pview = 'main';
    }

    render($pview, array(
        'title' => 'Posts tagged: ' . tag_i18n($tag->value) . ' - ' . blog_title(),
        'description' => 'All posts tagged: ' . tag_i18n($tag->value) . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'tag/' . strtolower($tag->value),
        'page' => $page,
        'posts' => $posts,
        'tag' => $ttag,
        'bodyclass' => 'in-tag tag-' . strtolower($tag->value),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_tag' => true,
            ));
}

// Show edit the tag page
function get_tag_edit(string $tag) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $tagpost = get_tag_info($tag);
        if ($tagpost->value == 'none') {
            not_found('get_tag_edit');
        }

        render('edit-tag', array(
            'title' => 'Edit tag - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_tag',
            'is_admin' => true,
            'bodyclass' => 'edit-tag',
            'p' => $tagpost
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get edited data from tag page
function post_tag_edit() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    if (!is_logged()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');

    if ($proper && !empty($title) && !empty($content)) {
        if (empty($url)) {
            $url = $title;
        }

        edit_tag(new Title($title), $url, $content, $oldfile, $destination, new Desc($description));

        //
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'views/admin/views');

        render('edit-tag', array(
            'title' => 'Edit tag - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_tag',
            'is_admin' => true,
            'bodyclass' => 'edit-tag'
        ));
    }
}

// Delete tag
function get_tag_delete(string $tag) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $post = get_tag_info($tag);

        render('delete-tag', array(
            'title' => 'Delete tag - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_tag',
            'is_admin' => true,
            'bodyclass' => 'delete-tag',
            'p' => $post,
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get deleted tag data
function post_tag_delete() {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && is_logged()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
}

// Get tag folder.
function get_tag_folder() {
    static $_dfolder = array();
    if (empty($_dfolder)) {
        $tmp = array();
        $tmp = glob('content/data/*/blog/*/', GLOB_ONLYDIR);
        if (is_array($tmp)) {
            foreach ($tmp as $dir) {
                $_dfolder[] = $dir;
            }
        }
    }
    return $_dfolder;
}
