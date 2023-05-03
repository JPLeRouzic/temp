<!-- post.html
An individual blog post.
This template displays a post in main container of a single post page.
This template is included in layout.html.php
The content() function uses post.php
It is used for example in /News/etc/etc/
-->
    <div class="breadcrumb"><!-- Start breadcrumb -->
        <span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
                <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
                    <a href="<?php echo site_url() ; ?>" itemprop="url">Home</a>
                    <span content="3" itemprop="position"></span>
                </span>
        </span>
        <?php echo ' &#187; ' . $p->tag->value ?>
        <!-- Breadcrumb"s end-->
    </div>


<section class="inpost post section" itemprop="blogPost" itemscope="itemscope" itemtype="https://schema.org/BlogPosting">
    <div class="section-inner">
        <div class="content">    
            <?php
            if (is_logged()) {
                echo tab($p);
            }
            ?>   
            <div class="item">

                <div class="info text-left">
                    <?php if (!empty($p->link)) { ?>
                        <h1 class="title" itemprop="headline"><a target="_blank" href="<?php echo $p->url ?>"><?php echo $p->title; ?> <i class="fa fa-external-link"></i></a></h1>
                    <?php } else { ?>
                        <h1 class="title" itemprop="headline"><?php echo $p->title->value; ?></h1>
                    <?php } ?>
                    <p class="meta">
                        <span class="date" itemprop="datePublished"><?php echo date('d F Y', $p->date) ?></span> - Posted by  
                        <span itemprop="author"><a href="<?php echo $p->author->url; ?>"><?php echo $p->author->pseudo; ?></a></span>
                        <span class="share pull-right">
                            <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-facebook"></i></a>

                            <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&text=<?php echo $p->title->value ?>"><i class="fa fa-twitter"></i></a>

                            <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-hacker-news"></i></a>

                            <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>"><i class="fa fa-linkedin"></i></a>

                            <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-reddit"></i></a>

                            <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-tumblr-square"></i></a>

                            <a target="_blank" href="https://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&appkey=&title=<?php echo $p->title->value ?>"><i class="fa fa-weibo"></i></a>

                        </span>
                    </p>
                </div>
                
                <!-- voice -->
                <div>
                 </div>
                <!-- end voice -->
                
                <!-- Article body -->
                <div class="desc text-left" itemprop="articleBody">
                    <?php 
                     // Now echo the body  of the post
                    echo $p->body;
                    ?>
                </div>
                <!-- End article body -->
                
                <div style="margin-top:30px;position:relative;">
                    <span class="tags"><i class="fa fa-tags"></i> <?php echo $p->tag->value; ?></span> 

                    <span class="share pull-right">
                        <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-facebook"></i></a>

                        <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&text=<?php echo $p->title->value ?>"><i class="fa fa-twitter"></i></a>

                        <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-hacker-news"></i></a>

                        <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>"><i class="fa fa-linkedin"></i></a>

                        <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-reddit"></i></a>

                        <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-tumblr-square"></i></a>

                        <a target="_blank" href="http://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&appkey=&title=<?php echo $p->title->value ?>"><i class="fa fa-weibo"></i></a>

                    </span>
                    <div style="clear:both;"></div>
                </div>
                
                <!-- Comment section -->
                <?php
                // Call model/comments/view.php
                $posttitle1 = strtolower($p->title->value); // string in lowercase
                $posttitle = str_replace(' ', '-', $posttitle1);
                ?>

                <div class="commentContainer">
                    <iframe class="commentFrame" src="../../model/comments/view.php?post=<?php echo $posttitle; ?> " seamless="seamless" scrolling="no" frameborder="0" allowtransparency="true"></iframe>
                </div>
                <!-- End Comment section -->

                <div style="margin-top:30px;position:relative;">
                    <?php $tags = get_related($p->tag->related, true, config('related.count')); ?>
                    <?php
                    $char = 30;
                    $total = count($tags);
                    $i = 1;
                    if ($total >= 1) {
                        ?>
                        <div class="related related-posts" style="margin-top:30px;position:relative;">
                            <hr>
                            <h2 class="heading">Related Posts</h2>
                            <?php foreach ($tags as $t): ?>
                                <div class="item col-md-4">
                                    <?php
                                    if (strlen(strip_tags($t->title->value)) > $char) {
                                        $relatedTitle = shorten($t->title->value, $char) . '...';
                                    } else {
                                        $relatedTitle = $t->title->value;
                                    }
                                    ?>
                                    <h3 class="title"><a href="<?php echo $t->url; ?>"><?php echo $relatedTitle; ?></a></h3>
                                    <div class="content">
                                        <p><?php echo shorten($t->description->value, 60); ?>... <a class="more-link" href="<?php echo $t->url; ?>">more</a></p>
                                    </div><!--//content-->
                                </div>
                                <?php
                                if ($i++ >= config('related.count')) {
                                    break;
                                }
                                ?>
                            <?php endforeach; ?>
                            <div style="clear:both;"></div>
                            <?php
                        } else {
                            echo '<div>';
                        }
                        ?>  
                        <div id="line"><hr/></div>

                        <?php if (!empty($next)):
                            ?>
                            <span class="newer"><a href="<?php echo($next['url']); ?>" rel="next"><i class="fa fa-long-arrow-left"></i> Next Post</a></span>
                        <?php endif; ?>
                        <?php if (!empty($prev)): ?>
                            <span class="older pull-right"><a href="<?php echo($prev['url']); ?>" rel="prev">Previous Post <i class="fa fa-long-arrow-right"></i></a></span>
                        <?php endif; ?>

                        <div style="clear:both;"></div>
                    </div>

                </div>
            </div><!--//item-->                        
        </div><!--//content-->  
    </div><!--//section-inner-->                 
</section><!--//section-->

