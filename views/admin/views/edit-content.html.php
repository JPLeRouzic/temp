<!-- edit-content.html -->
<?php
if (isset($p->file)) {
    $url = $p->file;
} else {
    $url = $oldfile;
}

$content = file_get_contents($url);
$oldtitle = get_content_tag('t', $content, 'Untitled');
$olddescription = get_content_tag('d', $content);
$oldtag = get_content_tag('tag', $content);
$oldcontent = remove_html_comments($content);

$dir = substr($url, 0, strrpos($url, '/'));
$isdraft = explode('/', $dir);
$oldurl = explode('_', $url);

if (empty($oldtag)) {
    $oldtag = $oldurl[1];
}

$oldmd = str_replace('.md', '', $oldurl[2]);

if (isset($_GET['destination'])) {
    $destination = $_GET['destination'];
} else {
    $destination = 'admin';
}
$replaced = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/';

// Category string
$cat = explode('/', $replaced);
$category = $cat[count($cat) - 3];

$dt = str_replace($replaced, '', $oldurl[0]);
$t = str_replace('-', '', $dt);

// string(45) "content/users/admin/blog/中文/post/20210816184920"
$oldtimearray = explode('/', $t);
if (is_array($oldtimearray)) {
    $t = $oldtimearray[count($oldtimearray) - 1];
}

$time = new DateTime($t);
$timestamp = $time->format("Y-m-d H:i:s");
// The post date
$postdate = strtotime($timestamp);
// The post URL
if (config('permalink.type') == 'post') {
    $delete = site_url() . 'post/' . $oldmd . '/delete?destination=' . $destination;
} else {
    // The post URL
    $delete = site_url() . date('Y/m', $postdate) . '/' . $oldmd . '/delete?destination=' . $destination;
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>views/admin/editor/css/editor.css"/>
<script src="<?php echo site_url() ?>assets/resources/js/jquery.min.js"></script> 
<script src="<?php echo site_url() ?>assets/resources/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Extra.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>assets/resources/css/jquery-ui.css">
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/jquery.ajaxfileupload.js"></script>

    <?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
    <?php } ?>

<div class="wmd-panel">
    <form method="POST">
        Title <span class="required">*</span>
        <br>
        <input type="text" name="title" class="text <?php
        if (isset($postTitle)) {
            if (empty($postTitle)) {
                echo 'error';
            }
        }
        ?>" value="<?php echo $oldtitle ?>"/>
        <br><br>
        Category <span class="required">*</span>
        <br>
        <select name="category">
            <?php
            $catgries = category_list();
            ?>
            <?php
            foreach ($catgries as $catgry):
                if (($category === $catgry[0]) || ($category === $catgry[1])) {
                    echo 'selected="selected"';
                    ?>
                    <option value="<?php echo $catgry[1]; ?>"><?php echo $catgry[1]; ?></option>
            <?php } ?>
        <?php endforeach; ?>        
        </select> 
        <br><br>
        Tag <span class="required">*</span><br>
        <input type="text" name="tag" class="text <?php
        if (isset($postTag)) {
            if (empty($postTag)) {
                echo 'error';
            }
        }
        ?>" value="<?php echo $oldtag ?>"/><br><br>
        Url (optional)<br>
        <input type="text" name="url" class="text" value="<?php echo $oldmd ?>"/>
        <br>
        <span class="help">If the url is left empty we will use the post title.</span>
        <br><br>
        Year, Month, Day<br>
        <input type="date" name="date" class="text" value="<?php echo date('Y-m-d', $postdate); ?>">
        <br>
        Hour, Minute, Second<br>
        <input type="time" name="time" class="text" value="<?php echo $time->format('H:i:s'); ?>">
        <br><br>
        Meta Description (optional)<br>
        <textarea name="description" rows="3" cols="20"><?php
        if (isset($p->description)) {
            echo $p->description->value;
        } else {
            echo $olddescription;
        }
        ?></textarea>
        <br><br>

            <?php if ($type == 'is_science_ed'): ?>
            Featured Image <span class="required">*</span>
            <br>
            <textarea rows="3" cols="20" class="text <?php
            if (isset($postImage)) {
                if (empty($postImage)) {
                    echo 'error';
                }
            }
            ?>" name="image"><?php echo $oldimage; ?></textarea>
            <input type="hidden" name="is_image" value="is_image">
            <?php endif; ?>

            <?php if ($type == 'is_post'): ?>
            <input type="hidden" name="is_post" value="is_post">
        <?php endif; ?>
        <br>
        <div id="wmd-button-bar" class="wmd-button-bar"></div>
        <textarea id="wmd-input" class="wmd-input <?php
        if (isset($postContent)) {
            if (empty($postContent)) {
                echo 'error edit-content 134';
            }
        }
        ?>" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea>
        <br>
        <input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
            <?php if ($isdraft[4] == 'draft') { ?>
            <input type="submit" name="publishdraft" class="submit" value="Publish draft"/> 
            <input type="submit" name="updatedraft" class="draft" value="Update draft"/> 
            <a href="<?php echo $delete ?>">Delete</a>
                <?php } else { ?>
            <input type="submit" name="updatepost" class="submit" value="Update post"/> 
            <input type="submit" name="revertpost" class="revert" value="Revert to draft"/> 
            <a href="<?php echo $delete ?>">Delete</a>
            <?php } ?>
    </form>
</div>

<style>
    #insertImageDialog {
        display:none;
        padding: 10px;
        font-size:12px;
    }
    .wmd-prompt-background {
        z-index:10!important;
    }
</style>

<div id="insertImageDialog" title="Insert Image">
    <h4>URL</h4>
    <input type="text" placeholder="Enter image URL" />
    <h4>Upload</h4>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="file" name="file" id="file" />
    </form>
</div>

<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/editor.js"></script>
