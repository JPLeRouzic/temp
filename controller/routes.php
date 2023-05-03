<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$path = $_SERVER['REQUEST_URI'];
// string(6) "/News/"

$slougarray = explode(site_url(), $path);
// array(2) { 
//      [0]=> string(0) "" 
//      [1]=> string(95) "2021/09/als-prognostic-prediction-by-hypermetabolism-varies-depending-on-the-nutritional-status" 
//      }
$slugarray = explode('/', $slougarray[1]);

/*
 * First test if first URL sub-element is numeric, 
  - if it is, then jump to process that subset
  including the required modules

 * Then test if first URL sub-element is 'index'
  - then process it because it is a call to the frontpage
  including the required modules

 * Then test if first URL sub-element is 'static'
  - it is a call to a static page
  including the required modules

 * Then process the rest of the request according to the URL sub-elements
  - it is a call to a some administrative page
  including the required modules
 */

/*
 * This is place where GET requests are managed
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (is_numeric($slugarray[0])) {
        /* First URL element is numeric */
        // Show blog post with year-month
        route('GET', '/:year/:month/:name', 'get_year_month_name');

        // Edit blog post with year-month
        route('GET', '/:year/:month/:name/edit', 'get_year_month_name_edit');

        // Delete blog post
        route('GET', '/:year/:month/:name/delete', 'get_year_month_name_delete');
        /* End URL element is numeric */
    } else {
        /* First URL element is NOT numeric */

        // The front page of the blog which presents posts starting from last
        // https://padiracinnovation.org/News/index
        route('GET', '/index', 'frontpage_imp');

        // The login page of the blog
        // https://padiracinnovation.org/News/login
        route('GET', '/login', 'login_ui');

        // The logout page of the blog
        // https://padiracinnovation.org/News/logout
        route('GET', '/logout', 'logout');

        /*
         * ************ Admin pages ***********************
         */
        // Show the page to fill in with new category information
        // https://padiracinnovation.org/News/add/category
        route('GET', '/add/category', 'add_category_get');

        // Show the "Add post" page (GET), in the example the type of content is 'post'
        // https://padiracinnovation.org/News/add/content?type=post
        route('GET', '/add/content', 'get_add_content');

        // Show the static add page (GET)
        // https://padiracinnovation.org/News/add/page
        route('GET', '/add/page', 'get_add_page');

        // Show the "add tag" page, a form to be filled with information about the new tag
        // https://csrf.4lima.de/News/add/tag
        route('GET', '/add/tag', 'get_add_tag');

        // The JSON API
        route('GET', '/api/json', 'api_json');

        /* Show the archive page */
        route('GET', '/archive/:req', 'archive_req');

        // Show the author page
        // https://padiracinnovation.org/News/author/admin
        route('GET', '/author/:name', 'author_name');

        // List the posts with this category
        // https://padiracinnovation.org/News/category/english/
        route('GET', '/category/:category', 'show_posts_in_category');

        // Edit the information for this category 
        // https://padiracinnovation.org/News/category/english/edit
        route('GET', '/category/:category/edit', 'edit_category_get');

        // Delete category
        // /News/category/english/delete?destination=admin/categories
        route('GET', '/category/:category/delete', 'category_delete_get');

        // Edit the profile
        route('GET', '/edit/profile', 'get_edit_profile');

        // Generate OPML file
        route('GET', '/feed/opml', 'feed_opml');

        // Show the RSS feed
        route('GET', '/feed/rss', 'feed_rss');

        // Show blog post without year-month
        route('GET', '/post/:name', 'get_post_name');

        // Form to delete blog post
        route('GET', '/post/:name/delete', 'get_post_name_delete');

        // Form to edit the post content
        route('GET', '/post/:name/edit', 'get_post_name_edit');

        // Show the search page
        route('GET', '/search/:keyword', 'search_keyword');

        // List the posts with this tag
        // https://padiracinnovation.org/News/tag/als/
        // (als is a tag)
        route('GET', '/tag/:tag', 'get_show_tag');

        // Delete tag
        // /News/tag/english/delete?destination=admin/tags
        // /News/tag/tagtotourl/delete?destination=admin/tags
        route('GET', '/tag/:tag/delete', 'get_tag_delete');

        // Edit the tag information (do not work)
        // https://padiracinnovation.org/News/tag/als/edit
        route('GET', '/tag/:tag/edit', 'get_tag_edit');

        /*
         * **************** ADMIN menu *********************
         */
        // Show admin page
        route('GET', '/admin', 'show_my_content');

        // Show Backup page
        route('GET', '/admin/backup', 'admin_backup');

        // Show Create backup page
        route('GET', '/admin/backup-start', 'admin_backup_start');

        // Display page showing all categories
        // https://padiracinnovation.org/News/admin/categories
        route('GET', '/admin/categories', 'get_all_categories');

        // Show comments page
        route('GET', '/admin/commenti', 'admin_comments');

        // Show Config page
        route('GET', '/admin/config', 'get_admin_config');

        // Show the "Add content" page where one can choose the type of post they want to create
        // https://padiracinnovation.org/News/admin/content
        route('GET', '/admin/content', 'get_admin_content');

        // Show admin/draft
        // https://padiracinnovation.org/News/admin/draft
        route('GET', '/admin/draft', 'get_admin_draft');

        // Show import RSS page
        // https://padiracinnovation.org/News/admin/import
        route('GET', '/admin/import', 'admin_import_get');

        // Show Menu builder
        route('GET', '/admin/menu', 'get_admin_menu');

        // Show admin/mine
        route('GET', '/admin/mine', 'get_admin_mine');

        // Show phpinfophpinfo page
        route('GET', '/admin/phpinfo', 'admin_phpinfo');

        // Show admin/popular 
        route('GET', '/admin/popular', 'get_admin_popular');

        // Show admin/posts 
        https://csrf.4lima.de/News/admin/posts
        route('GET', '/admin/posts', 'get_admin_posts');

        // Show stats page
        route('GET', '/admin/stats', 'admin_stats');

        // Show stub page
        route('GET', '/admin/stub', 'admin_stub');

        // Show tag page which lists existing tags and a link to add a new tag
        route('GET', '/admin/tags', 'get_admin_tags');

        // Show admin/trending 
        route('GET', '/admin/trending', 'get_admin_trending');

        // Show admin/unpopular 
        route('GET', '/admin/unpopular', 'get_admin_unpopular');

        // Provide Apache's recent logs and some statistics
        route('GET', '/admin/logsparser', 'admin_tests');

        /*
         * **************** STATIC PAGES ***********************
         */
        // Show static pages.
        route('GET', '/static/:static', 'get_static');

        // Show the add static page
        route('GET', '/static/:static/add', 'get_static_add');

        // Show edit the static page
        route('GET', '/static/:static/edit', 'get_static_edit');

        // Deleted the static page
        route('GET', '/static/:static/delete', 'get_static_delete');
    }
}
/*
 * This is place where POST requests are managed
 */ elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*
     *  *********** POST *************
     */
    if (is_numeric($slugarray[0])) {
        /* First URL element is numeric */
        // Get edited data from blog post
        route('POST', '/:year/:month/:name/edit', 'post_year_month_name_edit');

        // Get deleted data from blog post
        route('POST', '/:year/:month/:name/delete', 'post_year_month_name_delete');
        /* End URL element is numeric */
    }
    /*
     * ************ Admin pages ***********************
     */
    // Submitted add category (process POST)
    // https://padiracinnovation.org/News/add/category
    route('POST', '/add/category', 'add_category_post');

    // Submitted add post data (POST)
    // https://padiracinnovation.org/News/add/content?type=post
    route('POST', '/add/content', 'post_add_content');

    // Submitted static add page data (POST)
    route('POST', '/add/page', 'post_add_page');

    // Manage the submitted new tag
    route('POST', '/add/tag', 'post_add_tag');

    // Submitted Config page data
    route('POST', '/admin/config', 'post_admin_config');

    // Submitted import page data
    route('POST', '/admin/import', 'admin_import_post');

    // Process menu data
    route('POST', '/admin/menu', 'post_admin_menu');

    // POST deleted category data
    route('POST', '/category/:category/delete', 'category_delete_post');

    // Process the information for this category 
    // https://padiracinnovation.org/News/category/english/edit
    route('POST', '/category/:category/edit', 'edit_category_post');

    // Get submitted data from edit profile page
    route('POST', '/edit/profile', 'post_edit_profile');

    // Get submitted login data
    route('POST', '/login', 'admin_page');

    // Get deleted data from form
    route('POST', '/post/:name/delete', 'post_post_name_delete');

    // Post resulting from form to edit content
    route('POST', '/post/:name/edit', 'post_post_name_edit');

    // Get edited data from tag page
    route('POST', '/tag/:tag/edit', 'post_tag_edit');

    // Get deleted tag data
    route('POST', '/tag/:tag/delete', 'post_tag_delete');

    /*
     * **************** STATIC PAGES ***********************
     */
    // Submitted data from add static page
    route('POST', '/static/:static/add', 'post_static_add');

    // Get edited data from static page
    route('POST', '/static/:static/edit', 'post_static_edit');

    // Get deleted data for static page
    route('POST', '/static/:static/delete', 'post_static_delete');
}
/* If page not found? */
route('GET', '.*', 'not_found');
