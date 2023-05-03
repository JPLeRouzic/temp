<!-- layout.html.php
This is the main layout for your theme.
This template displays the <head>...</head> and it includes another template to display either:
    - one post
    - multiple posts.
    - a static page
It is used for example in /News/
Variables are passed through the render function, for example:
        render('add-content', array(
            'title' => 'Add content - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'type' => $type,
            'is_admin' => true,
            'bodyclass' => 'add-content',
        ));
-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo head_contents(); ?> <!-- charset, viewport, referrer and icon -->
        <title>Padirac Innovation - News from research in neurodegenerative diseases</title>
        <meta name="title" property="og:title" content="<?php echo $title; ?>">
        <link rel="canonical" href="<?php echo $canonical; ?>" >

        <meta name="description" property='og:description' content='
        <?php
        if (is_a($description, 'Desc')) {
            $descript1 = $description->value;
        } else {
            $descript1 = $description;
        }
        $strlndesc = strlen($descript1);
        echo substr($descript1, 0, min($strlndesc, 150));
        ?>
              '>

        <!-- Google Fonts
         <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">
        -->
        <!-- CSS Libraries  -->
<!--        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"> -->
        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/bootstrap.min.css" rel="stylesheet">
        
        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/lib/slick/slick.css" rel="stylesheet">
        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/lib/slick/slick-theme.css" rel="stylesheet">

        <!-- Funny glyphs -->
        <!--   Do not change as it calls also font awesome fa-xxxx -->
<!--          <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">  -->
      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/all.min.css" rel="stylesheet" type="text/css">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-solid-900.woff2" rel="stylesheet">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-solid-900.woff" rel="stylesheet">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-solid-900.ttf" rel="stylesheet">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-brands-400.woff2" rel="stylesheet">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-brands-400.woff" rel="stylesheet">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-brands-400.ttf" rel="stylesheet">

        <!-- Template Stylesheet -->
        <!-- begin -->
        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/Lato.css" rel="stylesheet" type="text/css">
        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/Montserrat.css" rel="stylesheet" type="text/css">
        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/Crimson.css" rel="stylesheet" type="text/css">     

        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/style.css" rel="stylesheet">

        <!-- JS libraries -->
        <script defer src="<?php
              echo site_url();
              echo config('views.root');
        ?>/js/includefiles.js"></script>
        <script defer type="text/javascript" src="<?php
              echo site_url();
              echo config('views.root');
        ?>/js/pubmed.js"></script>

        <!-- Voice interface -->
	    <!-- <script defer src="<?php echo site_url(); echo config('views.root'); ?>/js/speakClient.js"></script> -->
        <!-- end -->
    </head>
    <?php
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }
    ?>
    <body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="https://schema.org/Blog">


        <?php
        if (is_logged()) {
            toolbar();
        }
        ?>
        <!-- ******HEADER****** --> 
        <?php ?>

        <!-- header.html -->
        <div class="top-header" style="background-color:#dae3e7;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4">
                        <div class="logo">
                            <a href="<?php echo site_url(); ?>">
                                <img src="<?php
                                echo site_url();
                                echo 'assets/PI_logo_4.avif" alt="Padirac innovation"';
                                ?> >
                                     </a>
                                     </div>
                                     </div>
                                     <div class="col-lg-3 col-md-4">
                                <div class="search">
                                    <form id="search" class="navbar-form search" role="search">
                                        <div class="input-group">
                                            <input type="search" name="search" class="form-control" placeholder="Search the site">
                                            <span class="input-group-btn"><button type="submit" class="btn btn-default btn-submit"><i class="fa fa-search"></i></button></span>
                                        </div>
                                    </form>
                                </div>

                        </div>
                        <div class="col-lg-6 col-md-4">
                            <div class="social">
                                <a href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . site_url(); ?>"><i class="fab fa-facebook"></i></a>
                                <a href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . site_url(); ?>"><i class="fab fa-twitter"></i></a>
                                <a href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . site_url(); ?>"><i class="fab fa-hacker-news"></i></a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . site_url(); ?>"><i class="fab fa-linkedin"></i></a>
                                <a href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . site_url(); ?>"><i class="fab fa-reddit"></i></a>
                                <a href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . site_url(); ?>"><i class="fab fa-tumblr-square"></i></a>
                                <a href="https://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . site_url(); ?>"><i class="fab fa-weibo"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top Header End -->

        <div class="header">
            <div class="container">
                <nav class="navbar navbar-expand-md bg-dark navbar-dark">
                    <a href="#" class="navbar-brand">MENU</a>
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav m-auto">
                            <a href="<?php echo site_url(); ?>" class="nav-item nav-link active">Home</a>
                            <a href="<?php echo site_url(); ?>static/articles-importants" class="nav-item nav-link">Posts of importance</a>
                            <a href="<?php echo site_url(); ?>static/donate" class="nav-item nav-link">How to help us</a>
                            <a href="<?php echo site_url(); ?>feed/rss" class="nav-item nav-link">RSS feed</a>
                            <a href="/cdn-cgi/l/email-protection#79131c181709101c0b0b1c57151c0b160c03101a3909181d100b181a101717160f180d10161757160b1e">Contact the author</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Header End -->


        <!--      <div class="container sections-wrapper"> -->
        <div class="row">
            <div class="col-sm-8"><!-- First column-->
                <div>
                    <?php
                    // See post.html.php for HTML rendering of the content of the post
                    echo get_content_stash();
                    ?>   
                </div>
            </div><!-- End first column-->

            <div class="col-sm-4"><!-- Second column-->
                <aside class="recent-posts aside section">
                    <div class="section-inner">
                        <!-- Tab navigation -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Recent posts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Popular posts</a>
                            </li>
                        </ul>
                        <!-- End tab navigation -->

                        <!-- Tab content -->
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <!-- Tab 1 content -->
                                <?php $lists = recent_posts(); ?>
                                <?php $char = 60; ?>
                                <?php foreach ($lists as $l): ?>
                                    <?php
                                    if (strlen($l->title->value) > $char) {
                                        $recentTitle = shorten($l->title->value, $char) . '...';
                                    } else {
                                        $recentTitle = $l->title->value;
                                    }
                                    ?>
                                    <div class="item">
                                        <h3 class="title"><a href="<?php echo $l->url; ?>"><?php echo $recentTitle; ?></a></h3>
                                        <div class="content">
                                            <p><?php echo shorten($l->description->value, 75); ?>...</p>
                                            <a class="more-link" href="<?php echo $l->url; ?>"><i class="fa fa-link"></i> Read more</a>
                                            <span class="share pull-right">
                                                <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-facebook"></i></a>

                                                <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&text=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-twitter"></i></a>

                                                <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-hacker-news"></i></a>

                                                <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>"><i class="fa fa-linkedin"></i></a>

                                                <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-reddit"></i></a>

                                                <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-tumblr-square"></i></a>

                                                <a target="_blank" href="https://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&appkey=&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-weibo"></i></a>

                                            </span>
                                        </div><!-- content-->
                                    </div>
                                <?php endforeach; ?>
                                <!-- End Tab 1 content -->
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <!-- Tab 2 content -->
                                <?php $lists = popular_posts(); ?>
                                <?php $char = 60; ?>
                                <?php foreach ($lists as $l): ?>
                                    <?php
                                    if (strlen($l->title->value) > $char) {
                                        $recentTitle = shorten($l->title->value, $char) . '...';
                                    } else {
                                        $recentTitle = $l->title->value;
                                    }
                                    ?>
                                    <div class="item">
                                        <h3 class="title"><a href="<?php echo $l->url; ?>"><?php echo $recentTitle; ?></a></h3>
                                        <div class="content">
                                            <p><?php echo shorten($l->description->value, 75); ?>...</p>
                                            <a class="more-link" href="<?php echo $l->url; ?>"><i class="fa fa-link"></i> Read more</a>
                                            <span class="share pull-right">
                                                <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-facebook"></i></a>

                                                <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&text=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-twitter"></i></a>

                                                <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-hacker-news"></i></a>

                                                <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>"><i class="fa fa-linkedin"></i></a>

                                                <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-reddit"></i></a>

                                                <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-tumblr-square"></i></a>

                                                <a target="_blank" href="https://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&appkey=&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-weibo"></i></a>

                                            </span>
                                        </div><!-- content-->
                                    </div>    
                                <?php endforeach; ?>
                                <!-- End Tab 2 content -->
                            </div>
                        </div>
                        <!-- End tab content -->
                    </div><!-- section-inner-->
                </aside><!-- section-->

                <aside class="archive aside section">
                    <div class="section-inner">
                        <h2 class="heading">Archive</h2>
                        <div class="content">
                            <?php /* echo */ archive_list(); ?>
                        </div><!-- content-->
                    </div><!-- section-inner-->
                </aside><!-- section-->

                <aside class="archive aside section">
                    <div class="section-inner">
                        <h2 class="heading">Search</h2>
                        <form id="search" class="navbar-form search" role="search">
                            <div class="input-group">
                                <input type="search" name="search" class="form-control" placeholder="Type to search">
                                <span class="input-group-btn"><button type="submit" class="btn btn-default btn-submit"><i class="fa fa-angle-right"></i></button></span>
                            </div>
                        </form>
                    </div><!-- section-inner-->
                </aside><!-- section-->

                <aside class="category-list aside section">
                    <div class="section-inner">
                        <h2 class="heading">Languages</h2>
                        <div class="content">
                            <?php
                            $catlist = category_list();
                            echo '<ul>';

                            foreach ($catlist as $k => $catvalue) {
                                echo '<li><a href="' . site_url() . 'category/' . $catvalue[1] . '">' . $catvalue[1] . '</a></li>';
                            }

                            echo '</ul>';
                            ?>
                        </div><!-- content-->
                    </div><!-- section-inner-->
                </aside><!-- section-->

                <!-- Forum  section 
                <?php
                date_default_timezone_set('UTC');
                /* The comment section */
                if ($_SERVER['REQUEST_URI'] !== '/News/Home') { // No comment in front page
//				include('model/comments/index.php'); 
                    // FIXME
                }
                ?>
                <!-- section-->

                <!-- Now the Paypal button -->
                <br>Please, help us continue to provide valuable information:
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick" />
                    <input type="hidden" name="hosted_button_id" value="V6BQ5CYG47MHS" />
                    <input type="image" src="https://www.paypalobjects.com/en_US/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
                    <img alt="" border="0" src="https://www.paypal.com/en_FR/i/scr/pixel.gif" width="1" height="1" />
                </form>
                <br>

                <!-- Tags -->
                <aside class="tags aside section"><!-- section tags -->
                    <div class="section-inner">
                        <h2 class="heading">Tags</h2>
                        <div class="tag-cloud" >
                            <?php
// $tags = tag_cloud(true); 
                            $tags = ['Alzheimer', 'ALS', 'Parkinson', 'Cancer', 'Aging', 'Misc'];
                            foreach ($tags as $tag):
                                ?>
                                <a class="more-link" href="<?php echo site_url(); ?>tag/<?php echo $tag; ?>"><?php echo tag_i18n($tag); ?></a> 
                            <?php endforeach; ?>
                        </div><!-- tag-cloud-->
                    </div><!-- section-inner-->
                </aside><!-- section tags -->
            </div><!-- End second column-->

        </div><!-- End row-->
        <!-- end of container-->

        <!-- ******FOOTER layout.html.php ****** --> 
        <footer class="footer">
            <div class="container text-center">
                <a href="mailto:jeanpierre.lerouzic@padiracinnovation.org">Contact the author</a>
            </div>
        </footer><!-- footer-->


        <!-- Back to Top -->
        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="<?php
                            echo site_url();
                            echo config('views.root');
                            ?>/lib/easing/easing.min.js"></script>
        <script src="<?php
                            echo site_url();
                            echo config('views.root');
                            ?>/lib/slick/slick.min.js"></script>

        <!-- Template Javascript -->
        <script src="<?php
                            echo site_url();
                            echo config('views.root');
                            ?>/js/main.js">
        </script>
<script defer data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    </body>
</html> 
