<?php

function show_my_content() {

    if (!is_logged()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $pview = 'main';

    config('views.root', 'views/admin/views');

    render($pview, array(
        'title' => 'Blog - ' . blog_title(),
        'description' => blog_title() . ' Blog Homepage',
        'canonical' => site_url() . 'blog',
//        'page' => $page,
//        'posts' => $posts,
        'bodyclass' => 'in-blog',
         //       'pagination' => has_pagination($total, $perpage, $page),
        'is_blog' => true,
            ), 'layout');
}
