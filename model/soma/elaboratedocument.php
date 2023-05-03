<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * To generate a document:
 * We ask for keywords to the user, of for a research question
 * With those keywords we query Pubmed, which gives us a number of articles.
 * Loop1:
 * In each these articles we identify proper nouns.
 * For each of these proper nouns we query Wikipedia, so we get couples (ProperName, WPabstract)
 *  We could possibly query a local version of Wikipedia or use a HTTPXMLRequest to make the request from user's browser.
 * We summarize each WPabstract with enforcing sentences that have the original keywords.
 * Each summary of a WPabstract is inserted after the sentence where there was a ProperName. 
 * Each summary of a WPabstract is also put in a cache.
 * This aggregation of Pubmed articles and summaries of WPabstracts is sent to the next step.
 * We then ask the user to select a few articles that are sent to next step.
 * In the next step we search for "similar articles" and "articles which cited" the selected articles.
 * 
 * Depending of the size that was required by the user, we iterate this procedure a number of times by returning at Loop1.
 */

session_start();

if (!isset($_SESSION['name'])) {

    $_SESSION['failure'] = "You must log in.";
    header('Location: authenticate.php');
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="img/PI_logo_square.avif">
        <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/starter-template/">

        <title>Biology writing assistant</title>

        <link rel="stylesheet" type="text/css" href="css/pubmed.css"/>
        <script type="text/javascript" src="js/pubmed.js" defer></script>
        <!-- Bootstrap core CSS -->
        <link href="https://getbootstrap.com/docs/3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <style>
            body {
                padding-top: 50px;
            }
            .starter-template {
                padding: 40px 15px;
                text-align: center;
            }
            /* Style the navbar */
            #navbar {
                overflow: hidden;
                background-color: #333;
            }

            /* Navbar links */
            #navbar a {
                float: left;
                display: block;
                color: #f2f2f2;
                text-align: center;
                padding: 14px;
                text-decoration: none;
            }

            /* Page content */
            .content {
                padding: 16px;
            }

            /* The sticky class is added to the navbar with JS when it reaches its scroll position */
            .sticky {
                position: fixed;
                top: 0;
                width: 100%;
            }

            /* Add some top padding to the page content to prevent sudden quick movement (as the navigation bar gets a new position at the top of the page (position:fixed and top:0) */
            .sticky + .content {
                padding-top: 60px;
            }
        </style>

    </head>

    <body>

        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Biology writing assistant</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="elaboratedocument.php">Generate template article</a></li>
                        <li><a href="about.php">About</a></li> // FIXME
                        <li><a href="history.php">History</a></li>
                        <li><a href="contact.php">Contact</a></li> // FIXME
                        <li><a href="logout.php">Log Out</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <!-- End navbar -->
        <div class="container">

            <div class="row">
                <h3>Search PubMed for inspiring articles:</h3>
                First you must select published articles that inspire you.<br>
                Their textual content (but not their images) will be used in the process of creating a custom template.
                <br><br>
                Type here keywords that must be found in articles<br>
                <input id="inputIn" size="75" placeholder="Type your keywords here."><br><br>
                Type here keywords that should NOT be found in articles<br>
                <input id="inputEx" size="75" placeholder="Type keywords you want to exclude."><br><br>
                <button onClick="changeP()">New search</button>
                <br><br>
                <div  id="found" style="visibility: hidden;">
                    Found articles:<br>
                    We present you for each found article, its title, authors, journal and abstract.<br>
                    To consult an item, click on the cross to unfold it.<br>
                    In case you find the article interesting, please tick the box.<br>
                    <div id="recentArt">
                    </div>
                    <br>You have selected those articles<br>
                    <input type="text" id="NbArtcl" size="75">
                    <br>We will use those abstracts<br>
                    <input type="hidden" type="text" id="usedabstracts" size="75">
                    <br><br>Would you be interested in designing a free abstract template inspired by these articles?
                    <br>
                    <input type="text" id="WPArtcls" size="600">

                    <form action="TextAnalysis.php" method="post" target="_blank">
                        <input type="hidden" type = "text" id = "keywords" name="keywords">
                        <input type="hidden" type = "text" id = "PMIDs" name="PMIDs">
                        <input type="hidden" type = "text" id = "abstracts" name="abstracts">
                        <input type = "submit" value="Design a free abstract">
                    </form>
                    <form action="elaboratedocument.php" method="post" target="_blank">
                        <input type = "submit" value="Clear and start again">
                    </form>
                </div>
                <script>


                    function changeBT(pmid, abstractpar) {
                        //                   alert(abstractpar);
                        // Test if an article was deselected
                        var newNbArtcl = '';
                        var oldNbArtcl = document.getElementById("NbArtcl").value;
                        var oldabstracts = document.getElementById("usedabstracts").value;

                        /*
                         * Manage additions and removals of PMIDs
                         */
                        var indx = oldNbArtcl.indexOf(pmid);
                        if (indx !== -1) {
                            // So the article with this PMID was deselected
                            newNbArtcl = oldNbArtcl.replace(', ' + pmid, "");
                            document.getElementById("NbArtcl").value = newNbArtcl;
                        } else {
                            // So a new article was selected
                            document.getElementById("NbArtcl").value += ', ' + pmid;
                        }

                        // Copy the result also in form's hidden field that will be used 
                        // to transfert the list of selected PMIDs to the server
                        document.getElementById("PMIDs").value = document.getElementById("NbArtcl").value;

                        /*
                         * Manage additions and removals of abstracts
                         */
                        var indx = oldabstracts.indexOf(abstractpar);
                        if (indx !== -1) {
                            // So the article with this PMID was deselected
                            newNbArtcl = oldabstracts.replace('| ' + abstractpar, "");
                            document.getElementById("usedabstracts").value = newNbArtcl;
                        } else {
                            // So a new article was selected
                            document.getElementById("usedabstracts").value += '| ' + abstractpar;
                        }

                        // Copy the result also in form's hidden field that will be used 
                        // to transfert the list of selected PMIDs to the server
                        document.getElementById("abstracts").value = document.getElementById("usedabstracts").value;

                    }

                    function changeP() {
                        var inputIn = document.getElementById("inputIn").value;
                        var inputEx = document.getElementById("inputEx").value;
                        var input = '(' + inputIn + ') NOT (' + inputEx + ')';

                        // Make the 'found' HTML section visible
                        document.getElementById("found").style.visibility = "visible";

                        // First store for future reference the list of keywords selected by the user
                        document.getElementById("keywords").value = input;

                        // Creates a PubMed list for the Author, this string should be the search term used by PubMed
                        pubmed.createList(input)
                                //Optional - Sets the max number of records to retrieve
                                .setMax(20) // default = 10

                                //Optional - Sets whether or not the abstract of an article should be retrieved
                                .retrieveAbstract(true) // default = true
                                //Optional - Sets whether or not the abstract of an article should be collapsible
                                .collapsibleAbstract(true) // default = true
                                //Optional - Sets whether or not the abstract of an article should be pre-expanded
                                // this only has an effect if collapsibleAbstract = true
                                .openedAbstract(false) // default = false

                                //Optional - Sets whether or not the details of an article should be retrieved
                                .retrieveDetails(true) // default = true
                                //Optional - Sets whether or not the details of an article should be collapsible
                                .collapsibleDetails(true) // default = false
                                //Optional - Sets whether or not the details of an article should be pre-expanded
                                // this only has an effect if collapsibleDetails = true
                                .openedDetails(false) // default = true

                                //Optional - Override the function used to expand an element.
                                .overrideExpand(function (domElement) {
                                    domElement.style.display = "inline"; //default implementation
                                })

                                //Optional - Override the function used to collapse an element.
                                .overrideCollapse(function (domElement) {
                                    domElement.style.display = "none"; //default implementation
                                })

                                //Required - Converts the data to HTML and inserts it into the element with the specified ID
                                .bind("recentArt");

                        // Now query Wikipedia with the key words

                        // explode inputIn in an array of keywords
                        var keywords = inputIn.split(' ');
                        var res = makeRequests(keywords);
 //                       for (i = 0; i < res.length; i++) {
                            alert('res length: ' + res.length)
 //                       }
                        //  document.getElementById("keywords").WPArtcls = res;
                    }

                    async function makeRequests(titles) {
                        const results = [];

                        for (let i = 0; i < titles.length; i++) {
                            const title = titles[i];
                            const url = `https://en.wikipedia.org/w/api.php?origin=*&action=query&format=json&prop=extracts&exsentences=2&titles=${title}`;

//                            const response = await fetch(url);
                            const response = await fetch(url, {
                                mode: 'cors'
                            });

                            const data = await response.json();

                            // Get the page ID of the first page in the query result
                            const pageId = Object.keys(data.query.pages)[0];

                            // Get the extract of the page
                            const extract = data.query.pages[pageId].extract;

                            results.push({title, extract});
                        }
                        return results;
                    }

                </script>
            </div>
        </div>

        <!-- Bootstrap core JavaScript
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="https://getbootstrap.com/docs/3.3/dist/js/bootstrap.min.js"></script>
      </body>
    </html>
    
