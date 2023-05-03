<?php

// Show various page (top-level), admin, login, sitemap, static page.
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the add static page
function get_static_add(string $static) {
    if (is_logged()) {

        config('views.root', 'views/admin/views');

        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_add 89');
        }

        $post = $post1[0];

        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_page',
            'is_admin' => true,
            'bodyclass' => 'add-page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Submitted data from add sub static page
function post_static_add(string $static) {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content) && is_logged()) {
        if (!empty($url)) {
            add_sub_page(new Title($title), $url, $content, new Desc($description));
        } else {
            $url = $title;
            add_sub_page(new Title($title), $url, $content, new Desc($description));
        }
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
        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_page',
            'is_admin' => true,
            'bodyclass' => 'add-page'
        ));
    }
}

// Show edit the static page
function get_static_edit(string $static) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_edit 162');
        }

        $post = $post1[0];

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'edit-page',
            'is_admin' => true,
            'p' => $post,
            'type' => 'staticPage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get edited data from static page
function post_static_edit() {
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
        if (!empty($url)) {
            edit_page(new Title($title), $url, $content, $oldfile, $destination, new Desc($description));
        } else {
            $url = $title;
            edit_page(new Title($title), $url, $content, $oldfile, $destination, new Desc($description));
        }
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

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'edit-page',
            'type' => 'staticPage',
            'is_admin' => true
        ));
    }
}

// Deleted the static page
function get_static_delete(string $static) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_delete 243');
        }

        $post = $post1[0];

        render('delete-page', array(
            'title' => 'Delete page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'delete-page',
            'is_admin' => true,
            'p' => $post,
            'type' => 'staticPage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get deleted data for static page
function post_static_delete() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && is_logged()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
}

