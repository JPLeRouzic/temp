<?php

declare(strict_types=1);

include_once 'lexicon.php';

/**
 * Part Of Speech Tagging
 * Brill Tagger
 *
 * @category   BrillTagger
 * @author     Ekin H. Bayar <me@ekins.space>
 * @version    0.2.0
 *
 * Usage:
 * $input = "The quick brown fox jumps over the lazy dog.";
 * $tagger = new BrillTagger();
 * $tagger->tag($input);
 */
class BrillTagger {

    private $dictionary = LEXICON;

    /**
     * This is the entry point. It takes a string representing a text and 
     * restitute an array of arrays. Each consisting of a word and a tag.
     * $input = "The quick brown fox jumps over the lazy dog.";
     *
     * array(9) { 
     *        [0]=> array(2) { 
     *                ["token"]=> string(3) "The" 
     *                ["tag"]=> string(2) "AT" 
     *                } 
     *        [1]=> array(2) { 
     *                ["token"]=> string(5) "quick" 
     *                ["tag"]=> string(2) "JJ" 
     *                } 
     *
     * @param $text
     * @return array
     */
    public function tag(string $text): array {
        // FIXME this could be improved
        $arrayAlpha = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S',
        'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '_', '-'
        ) ;
        $arrayPunct = array('.', ',', ';') ;
        $matches = array() ; // The array where are stored words
        $word = '' ; // The place for one word
        
        $maxTxt = strlen($text);
        for ($indx = 0; $indx < $maxTxt; $indx++) {
            if ( ($text[$indx] === ' ') && ($text[$indx-1] !== '.') ) {
                $matches[] = $word;
                $word = '' ;
            } elseif (array_search($text[$indx], $arrayAlpha) !== false) {
                // The letter is alphanumeric, add it to current word
                $word .= $text[$indx] ;
            } elseif (array_search($text[$indx], $arrayPunct) !== false) {
                // It's a punctuation
                $matches[] = $word; // Current word is stored in $matches
                $matches[] = $text[$indx]; // The punctuation is also stored in $matches
                $word = '' ; // Current word is reinitialized
                if( (isset($text[$indx+1])) && ($text[$indx+1] === ' ') ) {
                    // We have a space after the punctuation, so ignore it
                    $indx++ ;
                }
            } else {
                // (, ), /, %, =, 
//                 echo '<br>Error tagging' ;
//                 var_dump($text[$indx]) ;
            }
        }
        $matches[] = $word; // do not forget last word

        // This is the result array
        $tagsArray = [];
        $cnt = count($matches);

        /*
         * Loop through all words
         */
        for ($i = 0; $i < $cnt; $i++) {
            $token = $matches[$i];

            /*
             * A dictionary and some morphological rules then provide an initial tag for each word token. 
             * Default the current entry in tag's results to:
             * - a common noun
             * - a subject
             */
            $tagsArray[$i] = ['token' => $token, 'tag' => 'NN1', 'SVO' => 'S'];

            # remove trailing full stops
            if (substr(trim($token), -1) === '.') {
                $token = preg_replace('/\.+$/', '', $token);
            }

            // This does a Part Of Speech analysis
            $this->POS_analysis($token, $tagsArray, $i, $matches);

            // This does a Subject/Verb/Object analysis
            $this->SVO_analysis($token, $tagsArray, $i, $matches);
        }
        return $tagsArray;
    }

    public function POS_analysis(string &$tokenOrg, array &$tagsArray, int &$i, array &$matches) {
        // We lowercase the first character of the token, yet save the original token for future analysis
        $token = $tokenOrg;

        /*
         * Get the tag corresponding to the token from dictionary if it exists
         * The most frequent tag is chosen, and rules will later modify this choice
         */
        if ($this->tokenExists($token)) {
            $tagsArray[$i]['tag'] = $this->dictionary[$token][0];
        }

        //  rule 1: DT0, {VBD | VBP} --> DT0, NN
        // "Some rain" verb base form
        // "Some rain" verb infinitive form
        // "Some have rain"
        if ($i > 0 && ($tagsArray[$i - 1]['tag'] === "DT0")) {
            if (($tagsArray[$i]['tag'] === "VVB") || ($tagsArray[$i]['tag'] === "VVI") || ($tagsArray[$i]['tag'] === "VHI")) {
                $tagsArray[$i]['tag'] = "NN";
            }
        }

        // rule 2: convert a noun to a number (CRD) if "." appears in the word
        // and if first character of $token is 'N'
        if (substr($tagsArray[$i]['tag'], 0, 1) === 'N') {
            if (str_contains($token, '.') > -1 || is_numeric($token)) {
                $tagsArray[$i]['tag'] = "CRD";
            }
        }

        // rule 3: convert a noun to a past participle if words.get(i) ends with "ed"
        if ((substr($tagsArray[$i]['tag'], 0, 1) === 'N') && (substr($tagsArray[$i]['tag'], -2) === 'ed')) {
            $tagsArray[$i]['tag'] = "VBN";
        }

        // rule 4: convert any type to adverb if it ends in "ly";
        if ($this->isAdverb($token)) {
            $tagsArray[$i]['tag'] = 'AV0';
        }

        // rule 5: convert a common noun (NN0, NN1, NN2) to a adjective if it ends with "al"
        if ((substr($tagsArray[$i]['tag'], 0, 2) === 'NN') && (substr($tagsArray[$i]['tag'], -2) === 'al')) {
            $tagsArray[$i]['tag'] = "AJ0";
        }

        // rule 6: convert a noun to a verb if the preceeding work is "would"
        if ($i > 0 && (substr($tagsArray[$i]['tag'], 0, 2) === 'NN') && ($matches[$i - 1] === "would")) {
            $tagsArray[$i]['tag'] = "VVI";
        }

        // rule 7: if a word has been categorized as a common noun and it ends with "s",
        //         then set its type to plural common noun (NN2)
        // FIXME Convert NN1 to NN2 if plural as some plural are not terminated by 's' like goose => geese
        if (($tagsArray[$i]['tag'] === "NN1") && (substr($tagsArray[$i]['tag'], -1) === 's') && (substr($tagsArray[$i]['tag'], -2) !== 'ss')) {
            $tagsArray[$i]['tag'] = "NN2";
        }

        // rule 8: convert a common noun to a present participle verb (i.e., a gerund)
        if ((substr($tagsArray[$i]['tag'], 0, 2) === 'NN') && (substr($tagsArray[$i]['tag'], -3) === 'ing')) {
            $tagsArray[$i]['tag'] = "VVG";
        }

        /*
         * This is to infer if it's something numeric
         */
        $tagsArray[$i]['tag'] = $this->transformNumerics($token, $tagsArray[$i]['tag']);

        /*
         * This is to infer if it's something else than a common noun
         * for ex. Common noun to adj. if it ends with 'al'
         */
        if ($this->isNoun($tagsArray[$i]['tag']) && !$this->isProperNoun($tagsArray[$i]['tag'])) {
            $tagsArray[$i]['tag'] = $this->transformNoun($tagsArray[$i]['tag'], $token);
        }

        /*
         * For undetected articles
         */
        if ($i > 0 && ($tagsArray[$i - 1]['tag'] === 'ZZ0') && (substr($tagsArray[$i]['tag'], -3, 2) === 'NN')) {
            $tagsArray[$i - 1]['tag'] = 'AT0';
        }

        /*
         * Converts verbs after 'the' to nouns
         */
        if ($i > 0 && $tagsArray[$i - 1]['tag'] === 'DT0' && $this->isVerb($tagsArray[$i]['tag'])) {
            $tagsArray[$i]['tag'] = 'NN1';
        }

        /*
         * Rectifies 'I' as a pronoun if before a verb
         */
        if ($i > 0 && $tagsArray[$i - 1]['tag'] === 'ZZ0' && $this->isVerb($tagsArray[$i]['tag'])) {
            $tagsArray[$i - 1]['tag'] = 'PNP';
        }

        /*
         * Rectifies 'one' as a pronoun if it is after 'the'
         */
        if ($i > 0 && $tagsArray[$i - 1]['tag'] === 'AT0' && $tagsArray[$i]['tag'] === 'CRD') {
            $tagsArray[$i]['tag'] = 'PNI';
        }

        // Convert NN1 to NP0 if first (and only the first) character is upper case, except if first word of sentence (except if all characters are uppercase)
        if ($i > 0 && ($tagsArray[$i]['tag'] === 'NN1') && ($this->starts_with_upper($tokenOrg))) {
            $tagsArray[$i]['tag'] = 'NP0';
        }

        // Discover negative abreviation like ('weren't')
        if (($tagsArray[$i]['tag'] !== 'VM1') && (substr($token, -2) === '\'t')) {
            $tagsArray[$i]['tag'] = 'VM1';
        }

        /*
         * FIXME Discover if it is a date or time
         * The token could be complete (date and time) or incomplete with only date or only time 
         *
          // punctuation should be like in 2013-05-01 12:30:45.5
          // $token2 = str_replace('/', '-', $token) ;
          if (date_parse($token) !== false) {
          $tagsArray[$i]['tag'] = 'CDN';
          }
         */

        // Discover if it is a fraction
        // Discover ordinal form when in a string ('eighteen', ''61st'', '172nd', '78th', '83rd')
        // Discover cardinal form when in a string ('twenty-eighth', 'twenty-fifth', 'twenty-first') 
        // Discover ordinal numbers like ('eighty-five')
        // Detect roman numbers like ('II', 'ii')
        // Detect units such as ('g/kg', 'g/mL')
        // Detect combine pronoun and verb like in ('you\'re', 'you\'s', '\'tain\'t', 'You\'ve', 'I\'ve', 'They\'ve')
        // Differentiate between VVB and VVI
        if ($i > 0) {
            $previousTag = $tagsArray[$i - 1]['tag'];
            if ($i > 0 && (substr($previousTag, 0, 1) === 'V') && ($tagsArray[$i]['tag'] === 'VVB')) {
                $tagsArray[$i]['tag'] = 'VVI';
            }
        }

        /*
         * Rectifies 'like' as a verb if after 'do'
         * verb => (*PNP, (VBD), (XX0), (AV0), Vxx* )
         */
        if (
                ($i > 0 && $tagsArray[$i - 1]['tag'] === 'VBD' && $tagsArray[$i]['tag'] === 'PRP') || // 'do like'
                ($i > 1 && $tagsArray[$i - 2]['tag'] === 'VBD' && $tagsArray[$i - 1]['tag'] === 'XX0' && $tagsArray[$i]['tag'] === 'PRP') || // 'do not like'
                ($i > 2 && $tagsArray[$i - 2]['tag'] === 'VBD' && $tagsArray[$i - 1]['tag'] === 'AV0' && $tagsArray[$i]['tag'] === 'PRP') || // 'do really like'
                ($i > 3 && $tagsArray[$i - 3]['tag'] === 'VDB' && $tagsArray[$i - 2]['tag'] === 'XX0' &&
                $tagsArray[$i - 1]['tag'] === 'AV0' && $tagsArray[$i]['tag'] === 'PRP') // 'do not really like'
        ) {
            $tagsArray[$i]['tag'] = 'VMI';
        }

        /*
         * This is to infer if it's something numeric
         * Anything that ends 'ly' is an adverb
         */
        if ($i > 0) {
            $tagsArray[$i]['tag'] = $this->transformBetweenNounAndVerb($tagsArray, $i, $token);
        }
    }

    /*
     * Subjetc/Verb/Object analysis
     */

    public function SVO_analysis(string &$token, array &$tagsArray, int &$i, array &$matches) {
        // To be done
    }

    /*
     * Get the tag corresponding to the token from dictionary if it exists
     * 
     * @param string $token
     * @return bool
     */

    public function tokenExists($token): bool {
        return isset($this->dictionary[$token]);
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function isNoun($tag): bool {
        return strpos(trim($tag), 'N') === 0;
    }

    /*
     *  check if first (and only first) letter of this string is upper case or lower case.
     */

    public function starts_with_upper($str) {
        $chr = mb_substr($str, 0, 1, "UTF-8");
        if (mb_strtolower($chr, "UTF-8") != $chr) {
            // First character is upper case, now look at last character
            $chr = mb_substr($str, -1, 1, "UTF-8"); // FIXME only one character at end of string is tested
            return mb_strtolower($chr, "UTF-8") == $chr;
        } else {
            return false;
        }
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function isProperNoun($tag): bool {
        return $tag === 'NP0';
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function isSingularNoun($tag): bool {
        return $tag === 'NN1';
    }

    /**
     * @param string $tag
     * @param string $token
     * @return bool
     */
    public function isPluralNoun($tag, $token): bool {
        return ($this->isNoun($tag) && substr($token, -1) === 's');
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function isVerb($tag): bool {
        return strpos(trim($tag), 'V') === 0;
    }

    /**
     * @param string $tag
     * FIXME not used
     * @return bool
     */
    public function isPronoun($tag): bool {
        return strpos(trim($tag), 'P') === 0;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isPastTenseVerb($token): bool {
        return in_array('VBN', $this->dictionary[$token], true);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isPresentTenseVerb($token): bool {
        return in_array('VBZ', $this->dictionary[$token], true);
    }

    /** it him me us you 'em thee we'uns
     * FIXME not used
     *
     * @param string $tag
     * @return bool
     */
    public function isAccusativePronoun($tag): bool {
        return strpos(trim($tag), 'PPO') === 0;
    }

    /** it he she thee
     * FIXME not used
     *
     * @param string $tag
     * @return bool
     */
    public function isThirdPersonPronoun($tag): bool {
        return strpos(trim($tag), 'PPS') === 0;
    }

    /** they we I you ye thou you'uns
     * FIXME not used
     *
     * @param string $tag
     * @return bool
     */
    public function isSingularPersonalPronoun($tag): bool {
        return strpos(trim($tag), 'PPSS') === 0;
    }

    /** itself himself myself yourself herself oneself ownself
     * FIXME not used
     *
     * @param string $tag
     * @return bool
     */
    public function isSingularReflexivePronoun($tag): bool {
        return strpos(trim($tag), 'PPL') === 0;
    }

    /** themselves ourselves yourselves
     * FIXME not used
     *
     * @param string $tag
     * @return bool
     */
    public function isPluralReflexivePronoun($tag): bool {
        return strpos(trim($tag), 'PPLS') === 0;
    }

    /** ours mine his her/hers their/theirs our its my your/yours out thy thine
     * FIXME not used
     *
     * @param string $tag
     * @return bool
     */
    public function isPossessivePronoun($tag): bool {
        return in_array($tag, ['PP$$', 'PP$'], true);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isAdjective($token): bool {
        if (isset($this->dictionary[$token])) {
            return (substr($token, -2) === 'al' || in_array('JJ', $this->dictionary[$token], true));
        } else {
            return false;
        }
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isGerund($token): bool {
        return substr($token, -3) === 'ing';
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isPastParticiple($token): bool {
        return substr($token, -2) === 'ed';
    }

    /*
     * This is to infer if it's something numeric
     * Anything that ends 'ly' is an adverb
     */

    public function isAdverb($token): bool {
        return substr($token, -2) === 'ly';
    }

    /**
     * - Common noun to adj. if it ends with 'al',
     * - to gerund if 'ing', to past tense if 'ed'
     * - to number if . appears
     *
     * @param string $tag
     * @param string $token
     * @return string
     */
    public function transformNoun($tag, $token): string {

        if ($this->isAdjective($token)) {
            $tag = 'AJ0'; // OK
        } elseif ($this->isGerund($token)) {
            $tag = 'VVG'; // OK
        } elseif ($this->isPastParticiple($token)) {
            $tag = 'VBN'; // OK
        } elseif ($token === 'I') {
            $tag = 'PPSS'; // OK
        } elseif ($this->isPluralNoun($tag, $token)) {
            $tag = 'NN2'; // OK
        }

        # Convert noun to number if . appears
        if (strpos($token, '.') !== false) {
            $tag = 'CDN'; // Not used?
        }

        return $tag;
    }

    /**
     * @param array  $tags
     * @param int    $i
     * @param string $token
     * @return mixed
     */
    public function transformBetweenNounAndVerb($tags, $i, $token) {
        # If we get noun noun, and the 2nd can be a verb, convert to verb
        if ($this->tokenExists($token) && $this->isNoun($tags[$i]['tag']) && $this->isNoun($tags[$i - 1]['tag'])
        ) {
            if ($this->isPastTenseVerb($token)) {
                $tags[$i]['tag'] = 'VVD';
            } elseif ($this->isPresentTenseVerb($token)) {
                $tags[$i]['tag'] = 'VVB';
            }
        }

        return $tags[$i]['tag'];
    }

    /*
     * This is to infer if it's something numeric
     */

    public function transformNumerics(string $token, string $tag): string {
        # tag numerals, cardinals, money (NNS)
        if (preg_match(NUMERAL, $token)) {
            $tag = 'CRD';
        }

        # tag years
        $matches = '';
        if (preg_match(YEAR, $token, $matches)) {
            $tag = isset($matches['CDN']) ? 'CDN' : 'CRD';
        }

        # tag percentages
        if (preg_match(PERCENTAGE, $token)) {
            $tag = 'CDN';
        }

        return $tag;
    }

}
