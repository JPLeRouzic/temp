<!-- front.html.php
This is the main layout for your theme.
This template displays the <head>...</head> and it includes another template to display either:
    - one post
    - multiple posts.
    - a static page
It is used for example in /News/
Variables are passed through the render function, for example:
        render('add-content', array(
            'title' => 'Add content - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'picture' => image_url(),  => url of a picture
            'type' => $type,
            'is_admin' => true,
            'bodyclass' => 'add-content'
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
        
      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-regular-400.woff2" rel="stylesheet">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-regular-400.woff" rel="stylesheet">

      <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/webfonts/fa-regular-400.ttf" rel="stylesheet">        

        <!-- Template Stylesheet -->
        <link href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/style.css" rel="stylesheet">

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

        <!-- Theme CSS --> 
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

    </head>
    <?php
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }
    ?>

    <body style="background-color:#dae3e7;">
        <!-- Top Header Start -->
        <div class="top-header" style="background-color:#dae3e7;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4">
                        <div class="logo">
                            <a href="<?php site_url(); ?>">
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

        <!-- Header Start -->
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

        <!-- Category News Start-->
        <?php $char = 80; ?>
        <div class="cat-news">
            <div class="container-fluid">
                <div class="row"> 
                    <!-- Alzheimer -->
                    <?php
                    $Alzposts = get_tagsS('Alzheimer', 1, 5, false);
                    ?>

                    <div class="col-md-6">
                        <h2>Alzheimer</h2>
                        <div class="row cn-slider">
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $Alzposts[0]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $Alzposts[0]->img_url; ?>" width="450" height="293" alt= "<?php echo $Alzposts[0]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- First article in Alzheimer -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $Alzposts[0]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($Alzposts[0]->title->value)) > $char) {
                                                    $recentTitle = shorten($Alzposts[0]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $Alzposts[0]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p>
                                            </div>
                                        </div>
                                    </div>
                                </a> 
                            </div>
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $Alzposts[1]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $Alzposts[1]->img_url; ?>" width="450" height="293" alt= "<?php echo $Alzposts[1]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- Second article in Alzheimer -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $Alzposts[1]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($Alzposts[1]->title->value)) > $char) {
                                                    $recentTitle = shorten($Alzposts[1]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $Alzposts[1]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $Alzposts[2]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $Alzposts[2]->img_url; ?>" width="450" height="293" alt= "<?php echo $Alzposts[2]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- Third article in Alzheimer -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $Alzposts[2]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($Alzposts[2]->title->value)) > $char) {
                                                    $recentTitle = shorten($Alzposts[2]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $Alzposts[2]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Parkinson -->
                    <?php
                    $Parkposts = get_tagsS('Parkinson', 1, 3, false);
                    ?>
                    <div class="col-md-6">
                        <h2>Parkinson</h2>
                        <div class="row cn-slider">
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $Parkposts[0]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $Parkposts[0]->img_url; ?>" width="450" height="293" alt= "<?php echo $Parkposts[0]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- First article in Parkinson -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $Parkposts[0]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($Parkposts[0]->title->value)) > $char) {
                                                    $recentTitle = shorten($Parkposts[0]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $Parkposts[0]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p> 
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $Parkposts[1]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $Parkposts[1]->img_url; ?>" width="450" height="293" alt= "<?php echo $Parkposts[1]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- Second article in Parkinson -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $Parkposts[1]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($Parkposts[1]->title->value)) > $char) {
                                                    $recentTitle = shorten($Parkposts[1]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $Parkposts[1]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p>
                                            </div>
                                        </div>
                                    </div>
                                </a> 
                            </div>
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $Parkposts[2]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $Parkposts[2]->img_url; ?>" width="450" height="293" alt= "<?php echo $Parkposts[2]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- Third article in Parkinson -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $Parkposts[2]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($Parkposts[2]->title->value)) > $char) {
                                                    $recentTitle = shorten($Parkposts[2]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $Parkposts[2]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p>
                                            </div>
                                        </div>
                                    </div>
                                </a> 
                            </div>
                        </div>
                    </div>
                    <!-- ALS -->
                    <?php
                    $ALSposts = get_tagsS('ALS', 1, 3, false);
                    ?>
                    <div class="col-md-6">
                        <h2>ALS/MND</h2>
                        <div class="row cn-slider">
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $ALSposts[0]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $ALSposts[0]->img_url; ?>" width="450" height="293" alt= "<?php echo $ALSposts[0]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- First article in ALS -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $ALSposts[0]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($ALSposts[0]->title->value)) > $char) {
                                                    $recentTitle = shorten($ALSposts[0]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $ALSposts[0]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p>
                                            </div>
                                        </div>
                                    </div>
                                </a> 
                            </div>
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $ALSposts[1]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $ALSposts[1]->img_url; ?>" width="450" height="293" alt= "<?php echo $ALSposts[1]->title->value; ?>"/> 
                                        <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- Second article in ALS -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $ALSposts[1]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($ALSposts[1]->title->value)) > $char) {
                                                    $recentTitle = shorten($ALSposts[1]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $ALSposts[1]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p> 
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a class="tn-title" href="<?php echo $ALSposts[2]->url; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $ALSposts[2]->img_url; ?>" width="450" height="293" alt= "<?php echo $ALSposts[2]->title->value; ?>"/> 
                                    </div>
                                    <div class="cn-content">
                                            <div class="cn-content-inner">
                                                <!-- Third article in ALS -->
                                                <i class="far fa-clock"></i><?php echo date('d F Y', $ALSposts[2]->date); ?>
                                                <?php
                                                if (strlen(strip_tags($ALSposts[2]->title->value)) > $char) {
                                                    $recentTitle = shorten($ALSposts[2]->title->value, $char) . '...';
                                                } else {
                                                    $recentTitle = $ALSposts[2]->title->value;
                                                }
                                                ?>
                                                <?php echo '<p style="color:yellow;">' . $recentTitle ?> . '</p> 
                                            </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Pubmed -->
                    <?php
                    $PUBposts = get_pubmed();
                    $char = 120;
                    ?>
                    <div class="col-md-6">  
                        <h2>Neurodegenerative<br>publications</h2>
                        <div class="row cn-slider">
                            <div class="col-md-4">
                                <a class="tn-title" href="<?php echo $PUBposts[0]['url']; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $PUBposts[0]['img_url']; ?>" width="80" height="80" alt= "<?php echo $PUBposts[0]['title']; ?>"/>
                                    </div>
                                    <div class="mn-content">
                                        <!-- First article in latest Pubmed -->
                                        <i class="far fa-clock"></i><?php echo $PUBposts[0]['date']; ?>
                                        <?php
                                        if (strlen(strip_tags($PUBposts[0]['title'])) > $char) {
                                            $recentTitle = shorten($PUBposts[0]['title'], $char) . '...';
                                        } else {
                                            $recentTitle = $PUBposts[0]['title'];
                                        }
                                        ?>
                                        <?php echo '<p style="color:black;">' . $recentTitle ?> . '</p>
                                    </div>
                                </a> 
                            </div>
                            <div class="col-md-4">
                                <a class="tn-title" href="<?php echo $PUBposts[1]['url']; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $PUBposts[1]['img_url']; ?>" width="80" height="80" alt= "<?php $PUBposts[1]['title']; ?>"/>
                                    </div>
                                    <div class="mn-content">
                                        <!-- Second article in latest Pubmed -->
                                        <i class="far fa-clock"></i><?php echo $PUBposts[1]['date']; ?>
                                        <?php
                                        if (strlen(strip_tags($PUBposts[1]['title'])) > $char) {
                                            $recentTitle = shorten($PUBposts[1]['title'], $char) . '...';
                                        } else {
                                            $recentTitle = $PUBposts[1]['title'];
                                        }
                                        ?>
                                        <?php echo '<p style="color:black;">' . $recentTitle ?> . '</p> 
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a class="tn-title" href="<?php echo $PUBposts[2]['url']; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $PUBposts[2]['img_url']; ?>" width="80" height="80" alt= "<?php $PUBposts[2]['title']; ?>"/>
                                    </div>
                                    <div class="mn-content">
                                        <!-- Third article in latest Pubmed -->
                                        <i class="far fa-clock"></i><?php echo $PUBposts[2]['date']; ?>
                                        <?php
                                        if (strlen(strip_tags($PUBposts[2]['title'])) > $char) {
                                            $recentTitle = shorten($PUBposts[2]['title'], $char) . '...';
                                        } else {
                                            $recentTitle = $PUBposts[2]['title'];
                                        }
                                        ?>
                                        <?php echo '<p style="color:black;">' . $recentTitle ?> . '</p> 
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a class="tn-title" href="<?php echo $PUBposts[3]['url']; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $PUBposts[3]['img_url']; ?>" width="80" height="80" alt= "<?php $PUBposts[3]['title']; ?>"/>
                                    </div>
                                    <div class="mn-content">
                                        <!-- Fourth article in latest Pubmed -->
                                        <i class="far fa-clock"></i><?php echo $PUBposts[3]['date']; ?>
                                        <?php
                                        if (strlen(strip_tags($PUBposts[3]['title'])) > $char) {
                                            $recentTitle = shorten($PUBposts[3]['title'], $char) . '...';
                                        } else {
                                            $recentTitle = $PUBposts[3]['title'];
                                        }
                                        ?>
                                        <?php echo '<p style="color:black;">' . $recentTitle ?> . '</p> 
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a class="tn-title" href="<?php echo $PUBposts[4]['url']; ?>">
                                    <div class="cn-img">
                                        <img src="<?php echo $PUBposts[4]['img_url']; ?>" width="80" height="80" alt= "<?php $PUBposts[4]['title']; ?>"/>
                                    </div>
                                    <div class="mn-content">
                                        <!-- Fifth article in latest Pubmed -->
                                        <i class="far fa-clock"></i><?php echo $PUBposts[4]['date']; ?>
                                        <?php
                                        if (strlen(strip_tags($PUBposts[4]['title'])) > $char) {
                                            $recentTitle = shorten($PUBposts[4]['title'], $char) . '...';
                                        } else {
                                            $recentTitle = $PUBposts[4]['title'];
                                        }
                                        ?>
                                        <?php echo '<p style="color:black;">' . $recentTitle ?> . '</p> 
                                    </div>
                                </a>
                            </div>
                        </div> <!-- end Latest Pubmed news -->
                        <!-- End news -->

                        <div class="row"> <!-- Category, tags, manuscript -->
                        </div> <!-- category, tags, manuscript -->  
                    </div> <!-- row -->

                </div>
            </div>
            <!-- Main News End-->
        </div>
    </div>
</div>


<!-- Footer Start -->
<div class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h3 class="title">Useful Links</h3>
                    <ul>
                        <li><a href="<?php echo site_url(); ?>static/als-hypermetabolism-and-diet">ALS weight calculator</a></li>
                        <li><a href="<?php echo site_url(); ?>static/biomarkers-and-als">Tracking ALS biomarkers</a></li>
                        <li><a href="<?php echo site_url(); ?>static/peptidepoc">Designing Peptide vaccines</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h3 class="title">Quick Links</h3>
                    <ul>
                        <li><a href=<?php echo site_url(); ?>static/medrxiv>Interesting medRxiv articles</a></li>
                        <li><a href=<?php echo site_url(); ?>static/pubmed>Interesting Pubmed articles</a></li>
                        <li><a href=<?php echo site_url(); ?>static/donate>Please support us</a></li>

                        https://padiracinnovation.org/News/donate
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h3 class="title">Get in Touch</h3>
                    <div class="contact-info">
                        <p><i class="fa fa-map-marker"></i>Le Rouzic, Hameau de la Goduçais, Le Minihic sur Rance, 35870, France</p>
                        <p><i class="fa fa-envelope"></i><a href="/cdn-cgi/l/email-protection#204a45414e5049455252450e4c45524f555a49436050414449524143494e4e4f564154494f4e0e4f5247">Contact the author</a></p>


<!--                            <p><i class="fa fa-phone"></i>+123-456-7890</p> -->
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

            <!--                <div class="col-lg-3 col-md-6">
                                <div class="footer-widget">
                                    <h3 class="title">Newsletter</h3>
                                    <div class="newsletter">
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sed porta dui. Class aptent taciti sociosqu ad litora torquent per conubia nostra inceptos
                                        </p>
                                        <form>
                                            <input class="form-control" type="email" placeholder="Your email here">
                                            <button class="btn">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div> -->
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Footer Bottom Start -->
<div style="display: none;" class="footer-bottom">
    <div class="container">
        <div class="row">
            <div class="col-md-6 copyright">
                <p>Copyright &copy; <a href="https://htmlcodex.com">HTML Codex</a>. All Rights Reserved</p>
            </div>

            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
            <div class="col-md-6 template-by">
                <p>Template By <a href="https://htmlcodex.com">HTML Codex</a></p>
            </div>
        </div>
    </div>
</div>
<!-- Footer Bottom End -->

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
