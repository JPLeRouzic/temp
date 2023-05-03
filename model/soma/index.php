<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['name'])) {

    $_SESSION['failure'] = "You must log in.";
    header('Location: authenticate.php');
    die();
}

$status = false;

$failure = false;

if (isset($_SESSION['failure'])) {
    $failure = htmlentities($_SESSION['failure']);

    unset($_SESSION['failure']);
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="">
        <title>Biology writing assistant</title>
        <!-- Custom styles for this template -->
        <link href="css/custom.css" rel="stylesheet">
       <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:100,300,400">
        <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <!-- Bootstrap core 
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">  
        -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>

        <!-- Begin navbar -->
        <header class="primary-header container group">

            <h2 class="logo">
                <a href="index.php">Biology <br> writing assistant</a>
            </h2>
            <h3 class="tagline">
                <a class="btn btn-alt" href="helpers/logout.php">Log Out</a>
            </h3>

            <nav class="nav primary-nav">
                <a href="index.php">Home</a>
                <a href="elaboratedocument.php">Generate template article</a>
                <a href="history.php">History</a>
            </nav>

        </header>
        <!-- End navbar -->

        <section class="row">
            <div class="grid">

                <!-- Articles -->
                <section class="teaser col-1-3">
                    <a href="elaboratedocument.php">
                        <h5>Articles</h5>
                        <img src="img/researcher.jpg" alt="Generating an article">
                        <h3>Do you need to write a paper soon?</h3>
                    </a>
                    <p>If biology is your field of choice, and you want to publish an article but you don't have the time,
                        we can provide custom document templates that are based on your requirements.</p>
                </section>
                <!-- Researchers -->
                <section class="teaser col-1-3">
                    <a href="elaboratedocument.php">
                        <h5>Custom Text Finalization</h5>
                        <img src="img/article.jpeg" alt="Text Finalization">
                        <h3>Personalized text finalization</h3>
                    </a>
                    <p>We can help you finalized your article with peer-reviewers and help publish it. </p>
                </section>
                <!-- History -->
                <section class="teaser col-1-3">
                    <a href="history.php">
                        <h5>History</h5>
                        <img src="img/map.jpeg" alt="History">
                        <h3>Analysis History</h3>
                    </a>
                    <p>The history of your previous text analysis on PubMed articles, you will have the necessary information to reproduce them again.</p>
                </section>
            </div>
        </section>


        <!-- Footer -->

        <footer class="primary-footer container group">

            <small>&copy; Biology writing assistant</small>

            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="elaboratedocument.php">Generate template article</a>
                <a href="history.php">History</a>
            </nav>

        </footer>

    </body>
</html>
