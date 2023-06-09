<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * In simplistic designs, each PHP script is called directly via an URL. 
 * Here we have only a single PHP script which is called directly from the web via URL. 
 * Everything else is a module included with include or require as needed. 
 * This routine intercepts the URL and activate the corresponding function
 * 
 * Included files that deal with URL routing follows this models:
 * dispatch_get('an URL template', a_function_to_manage_that template());
 * 
 * dispatch_get has two parameters:
 * - a route template, for example 'admin/popular' as in https://csrf.4lima.de/News/admin/popular
 * - a callback function to_manage_that template
 * 
 * Sometimes a GET or POST variable is passed along with every URL, in order to identify the task. 
 * ex: https://csrf.4lima.de/News/category/francais/edit?destination=admin/categories
 * 
 * It compares the incoming URL with the URL_pattern, if they are
 * identical, it prepares the callback function
 * It creates an action and puts it into the routes stack
 * 
 * Most of our functionnalities are available in the "model" folder
 * For example the URLs thrown in the "admin_toolbar.php" are intercepted there.
 * 
 * A drawback is that file is parsed at each URL request
 * 
 */

require_once 'controller/plugins/php-markdown/MarkdownExtra.inc.php';
require_once 'controller/plugins/php-rss-writer/Feed.php';
require_once 'controller/plugins/php-rss-writer/Channel.php';
require_once 'controller/plugins/php-rss-writer/Item.php';

require_once 'controller/includes/dispatch.php';
// This function build the browser content, with an HTML template and 
// under the control of dispatch.php
require_once 'controller/includes/stash.php';
require_once 'controller/includes/render.php';
require_once 'controller/includes/session.php';

// A clear definition of classes used in HTMLy
require_once 'controller/classes/Archive.php';
require_once 'controller/classes/Author.php';
require_once 'controller/classes/Category.php';
require_once 'controller/classes/Desc.php';
require_once 'controller/classes/Front.php';
require_once 'controller/classes/Post.php';
require_once 'controller/classes/Search.php';
require_once 'controller/classes/Tag.php';
require_once 'controller/classes/Title.php';
require_once 'controller/classes/Type.php';

require_once 'model/misc_functions.php';
require_once 'model/misc_theme.php';
require_once 'model/json_api.php';

require_once 'model/login/login.php';
require_once 'model/login/csrf.php';
require_once 'model/login/captcha.php';
require_once 'model/login/logout.php';

require_once 'model/archive/archive.php';
require_once 'model/archive/backup.php';
require_once 'model/archive/functions.php';

require_once 'model/author/authorpage.php';
require_once 'model/author/functions.php';
require_once 'model/author/mine.php';

require_once 'model/category/admin.php';
require_once 'model/category/category.php';
require_once 'model/category/functions.php';
require_once 'model/category/filesmng.php';

require_once 'model/content/admin.php';
require_once 'model/content/content.php';
require_once 'model/content/content_add.php';

require_once 'model/draft/draft.php';
require_once 'model/draft/functions.php';

// Scripts for acquiring Pubmed content
require_once 'model/pubmed/pubmed.php';

require_once 'model/frontpage/frontpage.php';
require_once 'model/frontpage/functions.php';

require_once 'model/menu/get_menu.php';
require_once 'model/menu/menu.php';
require_once 'model/menu/menu_funct.php';
require_once 'model/menu/parse_node.php';

require_once 'model/phpinfo/phpinfo.php';

require_once 'model/profile/profile.php';
require_once 'model/profile/functions.php';

require_once 'model/postsRecent/functions.php';
require_once 'model/postsRelated/functions.php';

require_once 'model/postsPopular/popular.php';
require_once 'model/postsPopular/functions.php';

require_once 'model/postsNotPopular/unpopular.php';
require_once 'model/postsNotPopular/functions.php';

require_once 'model/postsTrending/trending.php';
require_once 'model/postsTrending/functions.php';

require_once 'model/rss/rss.php';
require_once 'model/rss/functions.php';
require_once 'model/rss/opml.php'; // Support for importing and exporting RSS feed lists in OPML format

require_once 'model/import/import.php'; // Support for importing

require_once 'model/search/functions.php';
require_once 'model/search/search.php';

require_once 'model/pages/page.php';

require_once 'model/stats/stats.php';
require_once 'model/stats/functions.php';

require_once 'model/tags/admin.php';
require_once 'model/tags/tags.php';
require_once 'model/tags/functions.php';

require_once 'model/stub/stubfunc.php';

require_once 'model/comments/admin_comments.php'; 

require_once 'model/teaser/functions.php';

require_once 'model/toolbar/admin_toolbar.php';

require_once 'model/type/functions.php';

require_once 'model/logparser/test.php';

// require_once 'model/emails.php';

require_once 'model/config.php';
// require_once 'model/update.php';
require_once 'model/login/main_admin.php';

require_once 'views/admin/admin.php';
//require_once 'views/theme/breadcrumb.php';
require_once 'views/theme/author.php';
require_once 'views/theme/tag_bc.php';
require_once 'views/theme/copyright.php';

require_once 'model/staticPages/functions.php';
require_once 'model/staticPages/mainStaticPages.php';

require_once 'model/posts/getposts.php';
require_once 'model/posts/functions.php';
require_once 'model/posts/posts.php';

require_once 'model/staticPages/static_pages.php';

// !!! Should be last script !!! \\
require_once 'controller/routes.php';


// Set default timezone if it exists in config.ini
if (config('timezone')) {
    date_default_timezone_set(config('timezone'));
} else {
    date_default_timezone_set('Asia/Jakarta');
}

/*
 *  Serve the blog
 * We have included all needed files, now we will serve all incoming URL by 
 * setting up the routing system
 */
dispatch();
