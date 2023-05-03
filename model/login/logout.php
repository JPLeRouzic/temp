<?php

function logout() {
    /*
     * Logout page
     */
    if (is_logged()) {
        unset($_SESSION[config("site.url")]['user']);
        header('location: login');
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}
