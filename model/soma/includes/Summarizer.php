<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/**
 * @author W-Shadow
 * @url https://w-shadow.com/
 * @copyright 2008
 * 
 * Here’s the summarization algorithm in a nutshell :
 * - Split the input text into sentences, and split the sentences into words.
 * - For each word, stem it and keep count of how many times it occurs in the text. 
 * - Skip words that are very common, like “you”, “me”, “is” and so on.
 * - Sort the words by their “popularity” in the text and keep only the top 'n' most popular words. 
 *      + The idea is that the most common words reflect the main topics of the input text.
 *      + the “top 20” threshold is a mostly arbitrary choice.
 * - Rate each sentence by the words it contains. 
 *      + In this case I simply added together the popularity ratings of every “important” word in the sentence. 
 *      + For example, if the word “Linux” occurs 4 times overall, and the word “Windows” occurs 
 *          3 times, then the sentence “Windows bad, Linux – Linux good!” will get a rating of 11 
 *          (assuming “bad” and “good” didn’t make it into the Top 20 word list).
 * - Take the X highest rated sentences. That’s the summary.
 * 
 * JPLR
 * - Comparison to stop words is done independently of the case
 * - List of words that hint at ignoring the sentence they are in
 * 
 * TODO
 * - Base rating inversely to sentence length
 * - Rating is downgraded if infrequent words are used in a sentence
 * - Most important words are reported
 * - At similar count, words that appear in many documents are more important 
 *      than words in a few documents
 * 
 */
require_once 'porter_stemmer.php';
require_once __DIR__ . '/../brill/BrillTagger.php';
require_once 'linkwords.php'; // Words linking two propositions in the same sentence

class Summarizer {

    var $word_cnt_in_document;
    var $freq_words, $rare_words;
    var $tagger;
    var $linkwords;

    //Constructor
    function __construct() {
        global $default_linkwords;

        $this->word_cnt_in_document = array();  // Count for each word  in document
        $this->freq_words = array();            // Words with greatest count in document
        $this->rare_words = array();            // Words with lowest count in document
        $this->tagger = new BrillTagger();
        $this->linkwords = $default_linkwords;  // Words linking two propositions (and, or, etc..)
    }

    /**
     * $min_sentences - the minimum length of the summary.
     * $max_sentences - the maximum length of the summary.
     * $start- where we start summary
     * $end - where we end summary
     */
    function summary(string $text, $keyw, $start, $end, $min_sentences, $max_sentences): array {
        // * - Split the input text into sentencess.
        $sentences = $this->sentenceTokenize($text);
        /*
         * $sentences:
         * array(683) { 
         *      [0]=> string(1) "." 
         *      [1]=> string(11) "Epithelial." 
         *      [2]=> string(8) "tissues." 
         *      [3]=> string(8) "provide."
         *  ...
         */

        $sentence_bag = array();

        $endsum = count($sentences) - $end;

        $omitflag = false;

        // Browse each sentence
        for ($i = $start; $i < $endsum; $i++) {

            $word_stats = array();
            // * - Split the input sentence into words.
            $words = $this->wordTokenize($sentences[$i]);

            // If the sentence is too short to be meaningful
            // (it could be improved by detecting a subject/verb/object structure)
            if (count($words) < 5) {
                continue;
            }

            // Browse each word in this sentence
            foreach ($words as $word) {

                // * - For each word, stem it. 
                $wordStemmed = PorterStemmer::Stem($word);
//                $wordStemmed = $word;

                /*
                 * - For each word, count of how many times it occurs in the sentence. 
                 * If there is no count for this word, create it
                 */
//                echo '<br>found this word: ' . $wordStemmed . '<br>';
                if (!isset($word_stats[$wordStemmed])) {
                    $word_stats[$wordStemmed] = 1;
                } else {
                    $word_stats[$wordStemmed]++;
                }

                /*
                 * - For each word, count of how many times it occurs in the whole document. 
                 * If there is no count for this word, create it
                 * Build an array of word counts [word => count]
                 */
                if (!isset($this->word_cnt_in_document[$wordStemmed])) {
                    $this->word_cnt_in_document[$wordStemmed] = 1;
                } else {
                    $this->word_cnt_in_document[$wordStemmed]++;
                }
            } // End of loop through words of this sentence
            //
            // If we went here because of a omit word, we need to jump to the next sentence
            if ($omitflag == true) {
                $omitflag = false;
                continue;
            }

            /*
             *  Per sentence statistics
              sentence_bag: array(404) {
              .    [0]=> array(4) {
              .        ["sentence"]=> string(106) " Haemochromatosis is characterised by elevated transferrin saturation (TSAT) and progressive iron loading "
              .        ["word_cnt_in_sentence"]=> array(9) {
              .        ["haemochromatosi"]=> int(4)
              .        ["characteris"]=> int(3)
              .        ["elev"]=> int(2)
              .        ["transferrin"]=> int(1)
              .        ["satur"]=> int(1)
              .        ["tsat"]=> int(1)
              .        ["progress"]=> int(1)
              .        ["iron"]=> int(1)
              .        ["load"]=> int(1)
              .        }
              .    ["ord"]=> int(3)
              .    ["rating"]=> float(0.64285714285714268) (added later in code)
              .    ["references"]=> array('reference_1', 'ref_2', 'ref_3')
              .    }
              .    [1]=> array(4) {
             */
            if (!empty($word_stats)) {
                $sentence_bag[] = array(
                    'sentence' => $sentences[$i], // The sentence content
                    'word_cnt_in_sentence' => $word_stats, // For each word in sentence: The # of times it appears in it
                    'ord' => $i                     // Number associated with this sentence
                );
            }
        } // End of loop through each sentence


        /*
         * sort words by their frequency in the whole document (not just in one sentence)
         */
        arsort($this->word_cnt_in_document);

        // Keep a list of the 'n' most common words. 
        $this->freq_words = array_slice($this->word_cnt_in_document, 0, 10);

        // Keep a list of the 'n' least common words. 
        $this->rare_words = array_slice($this->word_cnt_in_document, 40);

        /*
         *  * - Rate each sentence by the words it contains. 
         *      + In this case I simply added together the popularity ratings of every “important” word in the sentence. 
         *      + For example, if the word “Linux” occurs 4 times overall, and the word “Windows” occurs 
         *          3 times, then the sentence “Windows bad, Linux – Linux good!” will get a rating of 11 
         *          (assuming “bad” and “good” didn’t make it into the Top 20 word list).
         */
        for ($i = 0; $i < count($sentence_bag); $i++) {
            $thisSentenceRating = $this->calculateRating($keyw, $sentence_bag[$i]);

            /*
             *  Store sentence's rating in bag of per sentence statistics
             */
            $sentence_bag[$i]['rating'] = $thisSentenceRating;
        }

        //Sort sentences by importance rating
        $keys = array_column($sentence_bag, 'rating');
        array_multisort($keys, SORT_ASC, $sentence_bag);
        /*
          echo '<hr>sentence_bag: ';
          foreach($sentence_bag as $sentence) {
          echo '<br>Sentence: ' . $sentence["sentence"] ;
          echo '<br>Sentence: ' . $sentence["rating"] ;
          }
          echo '<hr>';
         */
        //How many sentences do we need?
        if ($max_sentences == 0) {
            $max_sentences = count($sentence_bag);
        }

        $summary_count = min(
                $max_sentences,
                min($min_sentences, count($sentence_bag))
        );
        if ($summary_count < 1) {
            $summary_count = 1;
        }

        //echo "Total sentences : ".count($sentence_bag).", summary : $summary_count\n";
        //Take the X highest rated sentences (from the end of the array)
        $summary_bag = array_slice($sentence_bag, -$summary_count);

        /*
         * Restore the original sentence order
         * Sorts $summary_bag by values using a user-supplied comparison function to determine the order.
         */
        usort($summary_bag, array(&$this, 'cmpArraysOrd'));

        return $summary_bag;
    }

    /*
     * This is the user-supplied comparison function to determine the original sentence order.
     */

    function cmpArraysOrd($a, $b) {
        return $this->cmpArrays($a, $b, 'ord');
    }

    function cmpArrays($a, $b, $key) {
        if (is_int($a[$key]) || is_float($a[$key])) {
            return floatval($a[$key]) - floatval($b[$key]);
        } else {
            return strcmp(strval($a[$key]), strval($b[$key]));
        }
    }

    /*
     * Splits text into sentences. 
     * Treats newlines as end-of-sentence markers.
     * Treat link words as end of sentences.
     */

    function wordTokenize($sentence) {
        //Splits text into words and also does some cleanup.
        $words = preg_split('/[\'\s\r\n\t$]+/', $sentence);
        $rez = array();
        foreach ($words as $word) {
            $word1 = preg_replace('/(^[^a-z0-9]+|[^a-z0-9]$)/i', '', $word);
            $word = strtolower($word1);
            if (strlen($word) > 0) {
                array_push($rez, $word);
            }
        }
        return $rez;
    }

    /*
     * Rate a sentence
     * A sentence is promoted if it contains the most frequent words
     *
     * - Rate each sentence by the words it contains. 
     *      + In this case I simply added together the popularity ratings of every “important” word in the sentence. 
     *      + For example, if the word “Linux” occurs 4 times overall, and the word “Windows” occurs 
     *          3 times in overall, then 
     *          the sentence “Windows bad, Linux – Linux good!” will get a rating of 11 
     *                           3   +      4    +   4
     *          (assuming “bad” and “good” didn’t make it into the Top 20 word list).
     * 
     * - On contrary it is downgraded if it contains rare words (removed)
     *  rare words should be more penalizing than frequent words
     * 
     * - Rating is divided by sentence length, as the lengther a sentence is, the 
     *      higher probability it will contains high rated words
     */

    function calculateRating(string $keyw, array $sentence_words) {
        $rating = 0;
        foreach ($sentence_words['word_cnt_in_sentence'] as $word => $count_sentence) {
            /*
             * If the word belongs in $keyw, then it is heavily promoted
             * Same if it's a name or a verb
             */
            $wordgram = $this->tagger->tag($word);
            if(empty($wordgram) ) {
                continue;
            }
            // if( ($wordgram !== 'Nxx') && ($wordgram !== 'Vxx') ) {
            if ((substr($wordgram[0]["token"], 0, 1) === 'N') || (substr($wordgram[0]["token"], 0, 1) === 'V')) {
                // Not a name or verb, so we do not promote this word
                continue;
            }

            // The sentence contains this word, so it gets promoted
            $count_whole = $sentence_words['word_cnt_in_sentence'][$word];
            $duo = $sentence_words['sentence'];
            $sentence_length = explode(' ', $duo);

            // As a long sentence may contain more key words, penalize lengthy sentences
            $word_rating = ($count_sentence * $count_whole) / count($sentence_length);

            // Is this word a key word?
            if (str_contains($keyw, $word)) {
                $word_rating = $word_rating * 2;
            }

            $rating += $word_rating;
        }
        return $rating;
    }

    /*
     * Splits text into sentences. 
     * Treats newlines as end-of-sentence markers.
     * Treat link words as end of sentences.
     */

    function sentenceTokenize(string $text) {
        /*
         * Tokenize by link words
         * Link words are replaced with a dot and when all link words are processed
         * the text is splitted by dots
         */
        foreach ($this->linkwords as $lword) {
            $word = mb_ucfirst($lword);
            $text = str_replace($word, '. ' . $word, $text);
        }

        $result1 = explode('.', $text);

        // Capitalize first character and add a dot at end
        foreach ($result1 as $newsentence) {
            $rez = mb_ucfirst($newsentence);
            
            if (substr($rez, -1) !== '.') {
                $result[] = $rez . '.';
            } else {
                $result[] = $rez;
            }
        }
        // Return result
        return $result;
    }

}
