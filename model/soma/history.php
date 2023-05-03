<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['name'])) {

    $_SESSION['failure'] = "You must log in.";
    header('Location: authenticate.php');
    return;
}

// Check to see if we have some POST data, if we do process it
$id = $row['user_id'];

while ($row) {
    $searches[] = $row;
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Search History of Biology writing assistant</title>

        <!-- Custom styles for this template -->
        <link href="css/custom.css" rel="stylesheet">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:100,300,400">

        <meta charset="UTF-8">	
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

        <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    </head>

    <body>

        <!-- Begin navbar -->
        <header class="primary-header container group">

            <h1 class="logo">
                <a href="index.php">Biology writing assistant</a>
            </h1>
            <h3 class="tagline">
                <a class="btn btn-alt" href="helpers/logout.php">Log out</a>
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
                <table class="center" border="0" cellspacing="2" cellpadding="4" id="myTable">
                    <thead>
                        <tr><!-- headers of table -->
                            <th>Analysis Type </th>
                            <th>Mesh Query</th>
                            <th>Publication Date Range </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($searches)) {
                            foreach ($searches as $search) {
                                ?>
                                <tr>
                                    <td ><?php echo $search->analysis; ?></td>
                                    <td style="text-align:center"><?php echo $search->meshquery; ?></td>
                                    <td style="text-align:center"><?php echo $search->daterange; ?></td>
                                </tr>
                            <?php }
                        }
                        ?>
                    </tbody>
                </table>

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

        <!-- Script -->

        <script>
            $(document).ready(function () {
                $('#myTable').DataTable();
            });
        </script>

    </body>
</html>
