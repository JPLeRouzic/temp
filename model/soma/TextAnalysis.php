<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/*
 * On recherche les noms et verbes dans l'amorce. (les verbes sont peut-être transformés en noms)
 * Pour chacun d'eux on fait une recherche dans WP pour obtenir un texte long.
 * On résume ce texte qui est très long (notamment en tenant compte des mots clés fournis par le client)
 * On trouve des sources!
 * On aide le client avec un écran pour éditer son texte qui représente un abstract.
 *
 * As improvements:
 * - Mix more than two texts (using similar and cited by from Pubmed). (done)
 * - Improve summary with prioritized keywords from user
 * - Beautify the text:
 *       (note and refactor references)
 *       (replace biological names with higher level entities)
 *       (paraphrase)
 *       (reformulate sentences by pivoting around verb)
 * 
  On lui propose cinq choix payants:
  1. essai 3,000 mots $15
  2. Article scientifique 15,000 mots $59
  3. Thèse bachelor: 25,000 mots $100
  4. Thèse master: 50,000 mots $150
  5. Thèse doctorat: 100,000 mots $250
 *
 */

require_once 'includes/Summarizer.php';
// require_once 'includes/sentenceToken.php'; // Tokenizing sentences
require_once 'includes/rewriting.php';
require_once 'includes/html_functions.php';

session_start();

if (!isset($_SESSION['name'])) {

    $_SESSION['failure'] = "You must log in.";
    header('Location: authenticate.php');
    return;
}

if (isset($_POST['abstracts'])) {
    $summarizer = new Summarizer();
    $outrez = '';
    $result = '';
    $sentences = array();

    $keyw = $_POST['keywords'];
    // echo '<b>Keywords: ' . $keyw ;

    $PMIDs = $_POST['PMIDs'];
    // echo '<b>PMIDs: ' . $PMIDs ;

    $abstracts1 = $_POST['abstracts'];

    /*
     *  We rewrite abstracts, to make the prose not at first person and a bit less pedant
     */
    $abstracts = rewritings($abstracts1);

    /*
     * Select sentences of interest by checking keywords
     *
     * reduction factor = 1/2
     * min # of lines = 5
     */
    $summary_bag = $summarizer->summary($abstracts, $keyw, 0, 0, 16, 22);

    /*
     * Output result to browser
     */
    foreach ($summary_bag as $sentence) {
        $result .= mb_ucfirst($sentence['sentence']);
    }
} else {
    echo '<br>Not $POST';
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit abstract template</title>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/custom.css">
        <link rel="stylesheet" href="css/rules.css">
        <style>
            .textarea-wink {
                min-width: 100%;
            }
        </style>
    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand fs-4">Generate template article</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                        aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>

        <div class="container mt-5 mb-5" id="text-container">
            <div class="row">

                <div class="col-md-7 col-lg-8 col-xl-9">
                    <div class="container-wink">
                        <div class="backdrop-wink">
                            <div class="highlights-wink"></div>
                        </div>
                        <br><br>
                        <textarea class="textarea-wink"  rows="12">
                            <?php echo $result; ?></textarea>
                        <!-- <button type="Toggle" class="fs-5 btn btn-primary mt-5 mb-5 pt-2 pb-2" id="toggle-button">Toggle Perspective</button> -->
                    </div>
                </div>

                <div class="col-md-5 col-lg-4 col-xl-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="fs-4" scope="col">Legends</th>
                            </tr>
                        </thead>
                        <tbody id="legend-body">
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand navbar-dark bg-dark text-white">
        <div class="container-fluid">
            <div class="fs-6 navbar-brand">
                <a class="text-white" >Biology writing assistant</a>
            </div>        
        </div>
    </nav>
    <script src="js/bundle.js"></script>
</body>

</html>