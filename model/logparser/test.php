<?php
// Show stats page

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once 'model/logparser/apache_log_parser.class.php';

function admin_tests() {
  if(is_logged()) {
        echo '<html><body>';
     $log_parser = new apache_log_parser("/var/log/apache2/", "access.log", "error.log");
    // used for test purpose
    echo $log_parser->output();
        echo '</body></html>';
    } else {
        $login = site_url(). 'login';
        header("location: $login");
    }
    die;
}


