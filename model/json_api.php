<?php

// The JSON API
function api_json() {

    header('Content-type: application/json');

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int) $page1 : 1;
    $perpage = config('json.count');

    // Turn an array of posts into a JSON
    echo generate_json(get_posts(null, $page, $perpage));
}

function generate_json(array $posts) {
    return json_encode($posts);
}
