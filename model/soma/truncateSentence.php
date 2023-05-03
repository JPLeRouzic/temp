<?php

class truncateSentence {
    var $summarizer;
    var $parser ;
    var $arrayPRN = array("he", "she", "it", "they", "them", "his", "her", "its", "their");
    var $arrayAND = array(['and', 'or', 'then']);

    public function __construct() {
    $this->summarizer = new Summarizer();
            $this->parser = new BrillTagger();
}

function truncate_complex_sentence($text, $max_length) {
    # Tokenize the text into sentences
    $sentences = $this->summarizer->sentenceTokenize($text);
            echo '<br>truncate_1<br>' ;

    # Initialize variables for anaphora resolution
    $prev_sentence = "";
    $prev_last_word = "";
    $prev_subj = "";
    $prev_obj = "";

    # Truncate each sentence
    $truncated_sentences = array();
    foreach (truncateSentence::list($sentences) as $sentence) {
        # If the sentence is already short enough, just add it to the output list
        if (strlen($sentence) <= $max_length) {
            $truncated_sentences[] = $sentence;
            $prev_sentence = $sentence;
            continue;
        }

        # Otherwise, split the sentence
        $split_sentence = truncate_sentences($sentence);

        # Add the truncated sentences to the output list
        foreach (truncateSentence::list($split_sentence) as $truncated_sentence) {
            $truncated_sentences[] = $truncated_sentence;
            $prev_sentence = $truncated_sentence;
            $uno = explode(' ', $truncated_sentence);
            $unocount = count($uno);
            $prev_last_word = $uno[$unocount - 1];
            list($prev_subj, $prev_obj) = resolve_anaphora($truncated_sentences, $prev_sentence, $prev_last_word, $prev_subj, $prev_obj);
        }

        # Return the truncated sentences as a single string
        $stringrsult = implode(' ', $truncated_sentences);
        return $stringrsult;
    }
}

    /*
     * The truncate_sentences function takes a string of text as input and returns a list of truncated sentences. 
     * It first split the text into individual sentences. 
     * It then iterates through the sentences and handles anaphora by looking for pronouns in each sentence 
     * and replacing them with their antecedents from the previous sentence, if they exist.
     * Finally, the program appends each sentence (whether truncated or not) to a list of truncated sentences 
     * and returns it at the end.
     */

    function truncate_sentences($text) {
        $sentences = $this->summarizer->sentenceTokenize($text);
            echo '<br>truncate_2<br>' ;
        $truncated_sentences = array();
        $i = 0;
        foreach (truncateSentence::list(enumerate($sentences)) as $sentence) {
            if ($i > 0) {
                /*
                  To handle anaphora, the program first splits each sentence into words. 
                 * It then looks for pronouns by checking if each word is in a list of common pronouns. 
                 * If a pronoun is found, the program searches the previous sentence for an antecedent by 
                 * splitting it into words and checking if any of the words match the pronoun. 
                 * - If an antecedent is found, the program constructs a new sentence by replacing the pronoun 
                 * with the antecedent and adding it to the end of the previous sentence. 
                 * - If no antecedent is found, the program leaves the pronoun unchanged.
                 */
                $prev_sentence = $sentences[$i - 1];
                $prev_words = explode(' ', $prev_sentence);
                $sentence_words = explode(' ', $sentence);
                $duo = truncateSentence::range(len($sentence_words));
                foreach (truncateSentence::list($duo) as $j) {
                    if (array_search(sentence_words[j], $this->trioPRN) !== false) {
                        $antecedent = null;
                        $is_break = do_trunc(&$sentence_words, $truncated_sentences, &$prev_words, &$antecedent, &$j);
                        if ($is_break) {
                            break;
                        }
                    }
                }
            }
            $truncated_sentences->append($sentence);
            $i++;
        }
        return truncated_sentences;
    }

    /*
     * This function do implements anaphora
     * it returns true if a break should occur in the enclosing loop
     */

    function do_trunc(&$sentence_words, $truncated_sentences, &$prev_words, &$antecedent, &$j): bool {
        // for k in range(j-1, -1, -1):
        foreach (truncateSentence::list(truncateSentence::range(($j - 1), -1, -1)) as $k) {
            if (array_search($sentence_words[$k], $prev_words) !== false) {
                $antecedent = $sentence_words[$k];
                break;
            }
        }
        if ($antecedent) {
            // $quarto = implode(' ', sentence_words[:j]) ;
            $quarto = implode(' ', sentence_words[j]);
            // $quint = implode(' ', sentence_words[j+1:]) ;
            $quint = implode(' ', sentence_words[j + 1]);
            $new_sentence = $quarto . " " . $antecedent . " " . $quint;
            $countTS = count($truncated_sentences);
            $truncated_sentences[$countTS - 1] .= $new_sentence;
            return true;
        }
        return false;
    }

    function resolve_anaphora($truncated_sentences, $sentence, $prev_words, $prev_subj, $prev_obj) {
        # Tokenize the sentence into words
        $words = $this->summarizer->sentenceTokenize($sentence);
            echo '<br>truncate_3<br>' ;

        # Identify the subject and object nouns in the sentence
        # for i, word in enumerate(words):
        $i = 0;
        foreach ($words as $word) {
            $uno = array_search($word, $this->arrayPRN);
            $duo = !(array_search($words[$i - 1], $this->arrayAND) !== false) ;
            if (($uno !== false) && $i > 0 && $duo) {

                if (array_search(sentence_words[$i], $this->arrayPRN) !== false) {
                    $antecedent = null;

                    foreach (truncateSentence::list(truncateSentence::range(($i - 1), -1, -1)) as $k) {
                        if (array_search($words[$k], $prev_words) !== false) {
                            $antecedent = $words[$k];
                            break;
                        }
                    }

                    if ($antecedent) {
                        // $quarto = implode(' ', sentence_words[:j]) ;
                        $quarto = implode(' ', sentence_words[j]);
                        // $quint = implode(' ', sentence_words[j+1:]) ;
                        $quint = implode(' ', sentence_words[j + 1]);
                        $new_sentence = $quarto . " " . $antecedent . " " . $quint;
                        $countTS = count($truncated_sentences);
                        $truncated_sentences[$countTS - 1] .= $new_sentence;
                        break;
                    }
                }
            }
            $i++;
        }
    }

    function range($start, $stop = null, $step = null) {
        if ($stop === null) {
            $stop = $start;
            $start = 0;
        }
        if ($step == null) {
            $step = 1;
        }
        $arr = array();
        while ($start < $stop) {
            $arr[] = $start;
            $start += $step;
        }
        return $arr;
        }

        public static function list($item = null) {
            // In python, chars in a string can be iterated eg for x in "abc"
            if ($item === null) {
                return [];
            }
            if (is_string($item)) {
                return str_split($item);
            }
            if (is_array($item)) {
                return $item;
            }
            if ($item instanceof Traversable) {
                $list = [];
                foreach ($item as $k => $v) {
                    $list[$k] = $v;
                }
                return $list;
            }

            throw new Exception("Invalid arg passed to list()");
        }
}
