<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once 'csrf.php';
include_once 'misc.php';
include_once 'login.php';

session_start();

// If the user is already identified
if (isset($_SESSION['name'])) {
    header('Location: index.php');
    return;
}

$failure = false;

// If the user has failed to be identified?
if (isset($_SESSION['failure'])) {
    $failure = htmlentities($_SESSION['failure']);

    unset($_SESSION['failure']);
}

$success = false;

if (isset($_SESSION['message'])) {
    $success = htmlentities($_SESSION['message']);

    unset($_SESSION['message']);
}

/*
 * Check to see if we have some POST data, if we do process it
 */
if (isset($_POST['email']) && isset($_POST['password'])) {
    $properCSRF = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $usermail = from($_REQUEST, 'email');
    $userid = from($_REQUEST, 'userid');
    $password = from($_REQUEST, 'password');

    $email = htmlentities($usermail);
    $pass = htmlentities($password);

    $row = authentication($email, $pass);

    if ($row !== false) {
        error_log("Login success " . $email);
        $_SESSION['name'] = $usermail;
        $_SESSION['user_id'] = $userid;

        header("Location: index.php");
        return;
    }

    error_log("Login failed");
    $_SESSION['failure'] = "Incorrect mail or password";
    header("Location: index.php");
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

        <title>Log in Biology writing assistant</title>

        <!-- Custom styles for this template -->
        <link href="css/custom.css" rel="stylesheet">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:100,300,400">

    </head>

    <body>

        <!-- Begin navbar -->
        <header class="primary-header container group">

            <h1 class="logo">
                <a href="index.php">Biology writing assistant</a>
            </h1>
            <h3 class="tagline">
                <a class="btn btn-alt" href="Register.php">Sign in</a>
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

                <form class="register-form" action="#" method="post" >

                    <fieldset class="register-group">
                        <?php
                        // Note triple not equals and think how badly double
                        // not equals would work here...
                        if ($failure !== false) {
                            // Look closely at the use of single and double quotes
                            echo(
                            '<p style="color: red;" class="col-sm-10 col-sm-offset-2">' .
                            htmlentities($failure) .
                            "</p>\n"
                            );
                        }

                        if ($success !== false) {
                            // Look closely at the use of single and double quotes
                            echo(
                            '<p style="color: green;" class="col-sm-10 col-sm-offset-2">' .
                            htmlentities($success) .
                            "</p>\n"
                            );
                        }
                        ?>
                        <!-- CSRF a value to detect tampering attempts-->
                        <label>
                            <input type="hidden" name="csrf_token"  value="<?php echo get_csrf() ?>">
                        </label>
                        <!--  fake fields are a workaround for chrome/opera autofill getting the wrong fields -->
                        <label>
                            <input id="mail" style="display:none" type="text" name="fakeumailmbered">
                        </label>
                        <label>
                            <input id="password" style="display:none" type="password" name="fakepasswordremembered">
                        </label>   
                        <!--
                        <input autocomplete="off"> turns off autocomplete on many other browsers that don't respect
                        the form's "off", but not for "password" inputs.
                        -->
                        <label>
                            User pseudonym

                            <input type="text" name="userid" placeholder="User pseudonym" required>
                            <!--<input type="email" name="email" placeholder="Email address" required>-->

                        </label>

                        <label>
                            Email

                            <input id="real-mail" type="email" name="email" autocomplete="off" placeholder="Email address" required>
                            <!--<input type="email" name="email" placeholder="Email address" required>-->

                        </label>

                        <label>
                            Password
                            <input type="password" name="password" placeholder="Password" autocomplete="new-password" />
                        </label>

                    </fieldset>
                    <div class= sign-in>
                        <input class="btn btn-default" type="submit" name="submit" value="Log in">
                        <p> Don't you have an account? <a href=Register.php> Sign in.</a></p>
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
                <a href="history.php">History</a>
            </nav>

        </footer>

    </body>
</html>