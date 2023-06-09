<?php

// Return archive page.
function get_archive(string $req, int $page, int $perpage): array {
    $globpath = 'content/users/*/blog/*/post/*.md';

    $posts = get_posts_sorted($globpath);

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('_', $v['basename']);
        if (strpos($str[0], "$req") !== false) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        not_found('get_archive 17');
    }

    return get_posts($tmp, $page, $perpage);
}

// Return an archive list, categorized by year and month.
function archive_list():void {

    $dir = "content/widget";
    $filename = "content/widget/archive.cache";

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    if (!file_exists($filename)) {
        file_dont_exist($filename);
    } else {
        // Read archive from archive index file
        $uno = file_get_contents($filename);
        if ($uno != false) {
            $by_year = unserialize($uno);
        }
    }

    # Most recent year first
    krsort($by_year);

    # Iterate for display
    $i = 0;
    foreach ($by_year as $year => $months) {
        fileforeachyear($i, $year, $months);
    }
}

function file_dont_exist($filename) {
    $globpath = 'content/users/*/blog/*/post/*.md';
    $posts = get_post_unsorted($globpath);
    $by_year = array();
    foreach ($posts as $index => $v) {
        /*
         * array(2) { 
         *     ["basename"]=> string(66) "2022-01-14-08-49-47_als_muscle-wasting.md" 
         *     ["dirname"]=> string(37)
         */

        $arr = explode('_', $v['basename']);

        // Replaced string
        $str = $arr[0];
        $replaced = substr($str, 0, strrpos($str, '/')) . '/';

        $date = str_replace($replaced, '', $arr[0]);
        $data = explode('-', $date);
        $col[] = $data;
    }

    foreach ($col as $row) {

        if (isset($row[0])) {
            $y = $row[0];
        } else {
            $y = '';
        }
        if (isset($row[1])) {
            $m = $row[1];
        } else {
            $m = '';
        }
        $by_year[$y][] = $m;
    }

    $ar = serialize($by_year);
    file_put_contents($filename, print_r($ar, true));
}

function fileforeachyear(&$i, $year, $months) {
    if ($i == 0) {
        $class = 'expanded';
        $arrow = '&#9660;';
    } else {
        $class = 'collapsed';
        $arrow = '&#9658;';
    }
    $i++;

    $by_month = array_count_values($months);
    # Sort the months
    krsort($by_month);

    $script = <<<EOF
                    if (this.parentNode.className.indexOf('expanded') > -1){this.parentNode.className = 'collapsed';this.innerHTML = '&#9658;';} else {this.parentNode.className = 'expanded';this.innerHTML = '&#9660;';}
EOF;
    echo '<ul class="archivegroup">';
    echo '<li class="' . $class . '">';
    echo '<a href="javascript:void(0)" class="toggle" onclick="' . $script . '">' . $arrow . '</a> ';
    echo '<a href="' . site_url() . 'archive/' . $year . '">' . $year . '</a> ';
    echo '<span class="count">(' . count($months) . ')</span>';
    echo '<ul class="month">';

    foreach ($by_month as $month => $count) {
        $uno = mktime(0, 0, 0, $month, 1, 2010);
        if ($uno != false) {
            $name = date('F', $uno);
        } else {
            $name = date('F', 0);
        }
        echo '<li class="item"><a href="' . site_url() . 'archive/' . $year . '-' . $month . '">' . $name . '</a>';
        echo ' <span class="count">(' . $count . ')</span></li>';
    }

    echo '</ul>';
    echo '</li>';
    echo '</ul>';
}
