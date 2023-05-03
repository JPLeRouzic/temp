<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show Backup page
function admin_backup() {
    if (is_logged()) {
        config('views.root', 'views/admin/views');
        render('backup', array(
            'title' => 'Backup content - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-backup',
            'is_admin' => true, 
            'bodyclass' => 'admin-backup', 
            ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}


// Show Create backup page
function admin_backup_start() {
    if (is_logged()) {
        // set the theme as 'views/admin/views' FIXME is this OK?
        config('views.root', 'views/admin/views');
        render('backup-start', array(
            'title' => 'Backup content started - ' . blog_title(), 
            'description' => strip_tags(blog_description()), 
            'canonical' => site_url(), 'type' => 
            'is_admin-backup-start', 'is_admin' => true, 
            'bodyclass' => 'admin-backup-start', 
            ));
    } else {
        $login = site_url() .
                'login';
        header("location: $login");
    }
    die;
}


