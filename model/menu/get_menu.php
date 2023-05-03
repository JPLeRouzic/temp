<?php

// Auto generate menu from static page
function get_menu(string $custom) {
    $posts = get_static_pages();
    $req = $_SERVER['REQUEST_URI'];

    if (!empty($posts)) {

        asort($posts);

        echo '<ul class="nav ' . $custom . '">';
        if ($req == site_path() . '/' || stripos($req, site_path() . '/?page') !== false) {
            echo '<li class="item first active"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        } else {
            echo '<li class="item first"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        }

        if ($req == site_path() . '/blog' || stripos($req, site_path() . '/blog?page') !== false) {
            echo '<li class="item active"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
        } else {
            echo '<li class="item"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
        }

        $i = 0;
        $len = count($posts);

        foreach ($posts as $indexp => $v) {

            if ($i == $len - 1) {
                $class = 'item last';
            } else {
                $class = 'item';
            }
            $i++;

            // Replaced string
            $replaced = substr($v, 0, strrpos($v, '/')) . '/';
            $base = str_replace($replaced, '', $v);
            $url = site_url() . str_replace('.md', '', $base);

            $title = get_title_from_file($v);

            if ($req == site_path() . "/" . str_replace('.md', '', $base) || stripos($req, site_path() . "/" . str_replace('.md', '', $base)) !== false) {
                $active = ' active';
//                $reqBase = '';
            } else {
                $active = '';
            }


            echo '<li class="' . $class . $active . '">';
            echo '<a href="' . $url . '">' . ucwords($title) . '</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {

        echo '<ul class="nav ' . $custom . '">';
        if ($req == site_path() . '/') {
            echo '<li class="item first active"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        } else {
            echo '<li class="item first"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        }
        echo '</ul>';
    }
}
