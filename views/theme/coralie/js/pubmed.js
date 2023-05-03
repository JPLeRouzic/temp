window.pubmed = (function () {

    function createList(term) {

        var max = 10,
                showAbstract = true,
                collapsibleAbstract = true,
                openedAbstract = false,
                showDetails = true,
                collapsibleDetails = false,
                openedDetails = true;

        function ajaXML(location, callback) {
            var xmlhttp;
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
                {
                    var xml = xmlhttp.responseXML.documentElement;
                    callback(xml);
                }
            }

            xmlhttp.open("GET", location, true);
            xmlhttp.send();
        }

        var expand = function (domElement) {
            domElement.style.display = "inline";
        }
        var expander = function (link, type) {
            var domElement;
            if (type == "details") {
                link.innerHTML = "details [-]";
                domElement = link.parentNode.getElementsByClassName("pubmed_details")[0];
            } else if (type == "abstract") {
                link.innerHTML = "abstract [-]";
                domElement = link.parentNode.getElementsByClassName("pubmed_abstract")[0];
            }

            link.onclick = function () {
                collapser(link, type)
            };
            expand(domElement);
        }
        var collapse = function (domElement) {
            domElement.style.display = "none";
        }
        var collapser = function (link, type) {
            var domElement;
            if (type == "details") {
                link.innerHTML = "Publisher [+]";
                domElement = link.parentNode.getElementsByClassName("pubmed_details")[0];
            } else if (type == "abstract") {
                link.innerHTML = "abstract [+]";
                domElement = link.parentNode.getElementsByClassName("pubmed_abstract")[0];
            }

            link.onclick = function () {
                expander(link, type)
            };
            collapse(domElement);
        }

        var li = function (domElement) {
            ajaXML("https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=" + term + "&usehistory=y&rettype=xml&retmax=" + max, function (xml) {
                var WebEnv = xml.getElementsByTagName("WebEnv")[0].firstChild.nodeValue;
                var QueryKey = xml.getElementsByTagName("QueryKey")[0].firstChild.nodeValue;

                ajaXML("https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&query_key=" + QueryKey + "&WebEnv=" + WebEnv + "&rettype=xml&retmax=" + max, function (xml) {
                    var articles = xml.getElementsByTagName("PubmedArticle");
//                    alert(articles);
                    var html = "";
                    for (var i = 0; i < (articles.length); i++) {
                        html += '<hr>' ;
                        html += '<div class="pubmed_article"><div class="pubmed_title"><a class="pubmed_link" href="https://www.ncbi.nlm.nih.gov/pubmed/' + articles[i].getElementsByTagName("PMID")[0].firstChild.nodeValue + ' target="_blank">';
                        articleTitle = articles[i].getElementsByTagName("ArticleTitle")[0].firstChild.nodeValue;
                        html += articleTitle + '</a></div>';
                        if (showDetails) {
                            if (collapsibleDetails)
                                html += '<a class="pubmed_detailsExpander" href="javascript:void(0)"">Journal [+] </a><br>';
                            html += '<div class="pubmed_details">';
                            /*
                             var authors = articles[i].getElementsByTagName("Author");
                             for (var k = 0; k < authors.length; k++) {
                             var lastName = authors[k].getElementsByTagName("LastName")[0].firstChild.nodeValue;
                             var initials = authors[k].getElementsByTagName("Initials")[0].firstChild.nodeValue;
                             html += '<div class="pubmed_author">' + lastName + ' ' + initials + (k == authors.length - 1 ? '</div>' : ', </div>');
                             }
                             */
                            var journalName = articles[i].getElementsByTagName("Journal")[0].getElementsByTagName("Title")[0].firstChild.nodeValue;
//                            var pubDate = articles[i].getElementsByTagName("PubDate")[0].getElementsByTagName("Year")[0].firstChild.nodeValue;
                            var pubDate = "";
                            var doi = "";
                            if (articles[i].getElementsByTagName("ELocationID")[0])
                                var doi = "doi: " + articles[i].getElementsByTagName("ELocationID")[0].firstChild.nodeValue;
                            html += '<div class="pubmed_publicationDetails">' + journalName + ' ' + pubDate + '. ' + doi + '</div>';
                            html += '</div>';
                        }
                        if (showAbstract) {
                            if (collapsibleAbstract)
                                html += '<a class="pubmed_abstractExpander" href="javascript:void(0)">abstract [+] </a>';
                            html += '<div class="pubmed_abstract">';
                            if (articles[i].getElementsByTagName("AbstractText")[0]) {
                                var abstract = "No abstract found.";
                                if (typeof articles[i].getElementsByTagName("AbstractText")[0].firstChild.nodeValue !== "undefined")
                                {
                                    // the variable is defined and not null
                                    abstract = articles[i].getElementsByTagName("AbstractText")[0].firstChild.nodeValue;
                                }
                                html += abstract;
                            }
                            // Now send the social networks icons and links
                            html += '<br></div>';
//                            alert(html);
                        }

                        html += '<div class="pubmed_clear"></div>'
                        html += '</div>';

                    }
                    document.getElementById(domElement).innerHTML = html;
                    if (collapsibleAbstract) {
                        var abstractExpanders = document.getElementsByClassName("pubmed_abstractExpander");
                        for (var i = 0; i < abstractExpanders.length; i++) {
                            if (openedAbstract) {
                                expander(abstractExpanders[i], "abstract");
                            } else {
                                collapser(abstractExpanders[i], "abstract");
                            }
                        }
                    }
                    if (collapsibleDetails) {
                        var detailsExpanders = document.getElementsByClassName("pubmed_detailsExpander");
                        for (var i = 0; i < detailsExpanders.length; i++) {
                            if (openedDetails) {
                                expander(detailsExpanders[i], "details");
                            } else {
                                collapser(detailsExpanders[i], "details");
                            }
                        }
                    }
                });
            });
        }

        li.setMax = function (max_) {
            max = max_;
            return li;
        }

        li.setDetails = function (showDetails_, collapsibleDetails_, openedDetails_) {
            showDetails = showDetails_;
            if (collapsibleDetails_)
                collapsibleDetails = collapsibleDetails_;
            if (openedDetails_)
                openedDetails = openedDetails_;
            return li;
        }

        li.openedDetails = function (openedDetails_) {
            openedDetails = openedDetails_;
            return li;
        }
        li.collapsibleDetails = function (collapsibleDetails_) {
            collapsibleDetails = collapsibleDetails_;
            return li;
        }
        li.retrieveDetails = function (showDetails_) {
            showDetails = showDetails_;
            return li;
        }

        li.openedAbstract = function (openedAbstract_) {
            openedAbstract = openedAbstract_;
            return li;
        }
        li.collapsibleAbstract = function (collapsibleAbstract_) {
            collapsibleAbstract = collapsibleAbstract_;
            return li;
        }
        li.retrieveAbstract = function (showAbstract_) {
            showAbstract = showAbstract_;
            return li;
        }

        li.overrideExpand = function (expand_) {
            expand = expand_;
            return li;
        }

        li.overrideCollapse = function (collapse_) {
            collapse = collapse_;
            return li;
        }

        li.bind = function (domElement) {
            return li(domElement);
        }

        return li;
    }

    return {createList: createList}
})();
