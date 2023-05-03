<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

function admin_phpinfo() {
    if (is_logged()) {
        echo '<html><body>';
        phpinfo();
        echo '</body></html>';
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

?>
