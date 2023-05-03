<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 *  Registering a new user
 */

session_start();

$failure = false;

if (isset($_SESSION['failure'])) {
    $failure = htmlentities($_SESSION['failure']);
    unset($_SESSION['failure']);
}

// Check to see if we have some POST data, if we do process it
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role']) && isset($_POST['researchfield'])) {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $pass = htmlentities(md5($_POST['password']));
    $role = htmlentities($_POST['role']);
    $research_field = htmlentities($_POST['researchfield']);

    // Checking if email already exists 

    
    //////////////////
    // Register the user
    create_user($name, $pass, $email, $role, $researchfield);

    //if the registration is succesful
    $_SESSION['message'] = 'Registration succesful! You can now log in.';

    header('Location: authenticate.php');
    return;
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">


        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>


        <title>Signing Biology writing assistant</title>

        <!-- Custom styles for this template -->
        <link href="css/custom.css" rel="stylesheet">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:100,300,400">

        <style>
            .ui-autocomplete {
                max-height: 100px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
            }
            /* IE 6 doesn't support max-height
             * we use height instead, but this forces the menu to always be this tall
             */
            * html .ui-autocomplete {
                height: 100px;
            }
        </style>

    </head>

    <body>

        <!-- Begin navbar -->
        <header class="primary-header container group">

            <h1 class="logo">
                <a href="index.php">Biology writing assistant</a>
            </h1>
            <h3 class="tagline">
                <a class="btn btn-alt" href="authenticate.php">Log in</a>
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
                <form class="register-form" action="#" method="post">

                    <fieldset class="register-group">
<?php
if ($failure !== false) {
    // Look closely at the use of single and double quotes
    $status_color = 'red';
    echo(
    '<p style="color: red;" class="col-sm-10 col-sm-offset-2">' .
    htmlentities($failure) .
    "</p>\n"
    );
}
?>
                        <label>
                            Name
                            <input type="text" name="name" placeholder="Full name" required>
                        </label>
                        <!--  fake fields are a workaround for chrome/opera autofill getting the wrong fields -->
                        <label>
                            <input id="mail" style="display:none" type="text" name="fakeumailmbered">
                        </label>
                        <label>
                            <input id="password" style="display:none" type="password" name="fakepasswordremembered">
                        </label>   
                        <!--
                        <input autocomplete="nope"> turns off autocomplete on many other browsers that don't respect
                        the form's "off", but not for "password" inputs.
                        -->
                        <label>
                            Email
                            <input id="real-mail" type="email" name="email" autocomplete="nope" placeholder="Email address" required>
                        </label>

                        <label>
                            Password
                            <input type="password" name="password" placeholder="Password" autocomplete="new-password" />
                        </label>

                        <label>
                            Research field
                            <input type="text" name="researchfield" placeholder="Research field" required>
                        </label>


                    </fieldset>
                    <div class= sign-in>
                        <input class="btn btn-default" type="submit" name="submit" value="Sign in">
                        <p> Do you have an account? <a href=authenticate.php> Log in.</a></p>
                    </div>
                </form>

            </div>
        </section>

        <!-- Footer -->

        <footer class="primary-footer container group">

            <small>&copy; Biology writing assistant</small>

            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="elaboratedocument.php">Generate template article</a>
                <a href="Hstory.php">History</a>
            </nav>

        </footer>
        <script>
            $(document).ready(function () {
                window.console && console.log('Document ready called');

            });
        </script>
    </body>
</html>
