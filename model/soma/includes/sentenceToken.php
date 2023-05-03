<?php

/*
 * It takes a string and output an array of sentences
 */
    function sentenceTokenize(string $text):array {
        /*
         * Tokenize by link words
         * Link words are replaced with a dot and when all link words are processed
         * the text is splitted by dots
         */
        foreach ($this->linkwords as $lword) {
            $word = strtolower($lword);
            $text = str_replace($word, '.', $text);
        }

        $result1 = explode('.', $text);

        // Capitalize first character and add a dot at end
        foreach ($result1 as $newsentence) {
            $rez = mb_ucfirst($newsentence);
            if (substr($rez, -1) !== '.') {
                $result[] = $rez . '.';
            } else {
                $result[] = $rez ;
            }
        }
        // Return result
        return $result;
    }

