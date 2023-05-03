<?php

// Show various page (top-level), admin, login, sitemap, static page.
function get_static($static) {
    $post1 = get_static_post($static);

    if (!$post1) {
        not_found('73, not found : ' . $static);
    }

    $post = $post1[0];

    if (config("views.counter") == "true") {
        add_view($post->file);
    }

    $vroot = rtrim(config('views.root'), '/');

    $pv = $vroot . '/static--' . strtolower($static) . '.html.php';
    if (file_exists($pv)) {
        $pview = 'static--' . strtolower($static);
    } else {
        $pview = 'static';
    }

    render($pview, array(
        'title' => $post->title->value . ' - ' . blog_title(),
        'description' => $post->description,
        'canonical' => $post->url,
        'bodyclass' => 'in-page ' . strtolower($static),
        'p' => $post,
        'type' => 'staticPage',
        'is_page' => true,
    ));
}
