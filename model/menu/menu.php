<?php

// Show Menu builder
function get_admin_menu() {
    if (is_logged()) {
        config('views.root', 'views/admin/views');
        render('menu', array(
            'title' => 'Menu builder - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-menu',
            'is_admin' => true,
            'bodyclass' => 'admin-menu'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

function post_admin_menu() {

    if (is_logged()) {
        $json = from($_REQUEST, 'json');
        file_put_contents('content/data/menu.json', json_encode($json, JSON_UNESCAPED_UNICODE));
        echo json_encode(array(
            'message' => 'Menu saved successfully!',
        ));
    }
}
