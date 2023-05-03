<?php

include_once 'BrillTagger.php' ;
include_once 'lexicon.php' ;

/*
$input = "The quick brown fox jumps over the lazy dog.";
$tagger = new BrillTagger();
var_dump($tagger->tag($input));

array(9) { 
        [0]=> array(2) { 
                ["token"]=> string(3) "The" 
                ["tag"]=> string(2) "AT" 
                } 
        [1]=> array(2) { 
                ["token"]=> string(5) "quick" 
                ["tag"]=> string(2) "JJ" 
                } 
        [2]=> array(2) { 
                ["token"]=> string(5) "brown" 
                ["tag"]=> string(2) "JJ" 
                } 
        [3]=> array(2) { 
                ["token"]=> string(3) "fox" 
                ["tag"]=> string(2) "NN" 
                } 
        [4]=> array(2) { 
                ["token"]=> string(5) "jumps" 
                ["tag"]=> string(3) "VBZ" 
                } 
       [5]=> array(2) { 
                ["token"]=> string(4) "over" 
                ["tag"]=> string(2) "IN" 
                } 
       [6]=> array(2) { 
                ["token"]=> string(3) "the" 
                ["tag"]=> string(2) "AT" 
                } 
       [7]=> array(2) { 
                ["token"]=> string(4) "lazy" 
                ["tag"]=> string(2) "JJ" 
                } 
       [8]=> array(2) { 
                ["token"]=> string(4) "dog." 
                ["tag"]=> string(2) "NN" 
                } 
       }
       
Part-of-speech tags meaning:
       
ABV abbreviation
// adjective //
AJ0	adjective (unmarked) (e.g. GOOD, OLD)
AJC	comparative adjective (e.g. BETTER, OLDER)
AJQ qualifier (well less very most so real as highly fundamentally)
AJS	superlative adjective (e.g. BEST, OLDEST)
// //
AT0	article (e.g. THE, A, AN)
AV0	adverb (unmarked) (e.g. OFTEN, WELL, LONGER, FURTHEST)
AVN adverbial (e.g. home Monday yesterday west )
AVP	adverb particle (e.g. UP, OFF, OUT)
AVQ	wh-adverb (e.g. WHEN, HOW, WHY)
// //
CDN string which represents a numerical information (date/# of degrees/etc.) but not a number
CJC	coordinating conjunction (e.g. AND, OR)
CJS	subordinating conjunction (e.g. ALTHOUGH, WHEN)
CJT	the conjunction THAT
CRD	cardinal numeral (e.g. 3, FIFTY-FIVE, 6609) (excl ONE)
// //
DPS	possessive determiner form (e.g. YOUR, THEIR)
DT0	general determiner (e.g. THAT, THESE, SOME)
DTQ	wh-determiner (e.g. WHOSE, WHICH)
EX0	existential THERE
FRW	Foreign Words
ITJ	interjection or other isolate (e.g. OH, YES, MHM)
MRK markers in text, such abstract, conclusion
// Nouns //
NN0	noun (neutral for number) (e.g. AIRCRAFT, DATA)
NN1	singular noun (e.g. PENCIL, GOOSE)
NN2	plural noun (e.g. PENCILS, GEESE)
# NP0	proper noun (e.g. LONDON, MICHAEL, MARS)
// //
NULL	the null tag (for items not to be tagged)
ORD	ordinal (e.g. SIXTH, 77TH, LAST)
// Pronoun //
PNI	indefinite pronoun (e.g. all, any, anyone, anything, each, everybody, everyone, everything, few, many, nobody, none, one, several, some, somebody, and someone.)
PN$	genitive case of indefinite pronoun (e. g. one's someone's anybody's nobody's everybody's anyone's everyone's)
PNP	personal pronoun (e.g. I, me, they, we, us, them, you, he, him, she, her, it)
PNQ	wh-pronoun (e.g. WHO, WHOEVER)
PNX	reflexive pronoun (e.g. ITSELF, OURSELVES)
POS	the possessive (or genitive morpheme) 'S or '
PP$	possessive pronoun
// //
PRF	the preposition 'OF'
PRP	preposition (except for OF) (e.g. FOR, ABOVE, TO)
PUL	punctuation - left bracket (i.e. ( or [ )
PUN	punctuation - general mark (i.e. . ! , : ; - ? ... )
PUQ	punctuation - quotation mark (i.e. ` ' " )
PUR	punctuation - right bracket (i.e. ) or ] )
SYM	Symbols
TO0	infinitive marker TO
UH0 exclamation
UNC	"unclassified" items which are not words of the English lexicon
URL an Internet URL
// Be verb //
VBB	the "base forms" of the verb "BE" (except the infinitive), i.e. AM, ARE
VBD	past form of the verb "BE", i.e. WAS, WERE
VBG	-ing form of the verb "BE", i.e. BEING
VBI	infinitive of the verb "BE"
VBN	past participle of the verb "BE", i.e. BEEN
VBZ	-s form of the verb "BE", i.e. IS, 'S
// Do verb //
VDB	base form of the verb "DO" (except the infinitive), i.e.
VDD	past form of the verb "DO", i.e. DID
VDG	-ing form of the verb "DO", i.e. DOING
VDI	infinitive of the verb "DO"
VDN	past participle of the verb "DO", i.e. DONE
VDZ	-s form of the verb "DO", i.e. DOES
// Have verb //
VHB	base form of the verb "HAVE" (except the infinitive), i.e. HAVE
VHD	past tense form of the verb "HAVE", i.e. HAD, 'D
VHG	-ing form of the verb "HAVE", i.e. HAVING
VHI	infinitive of the verb "HAVE"
VHN	past participle of the verb "HAVE", i.e. HAD
VHZ	-s form of the verb "HAVE", i.e. HAS, 'S
// Other verbs //
VM0	modal auxiliary verb (e.g. CAN, COULD, WILL, 'LL)
VM1	modal auxiliary verb negated (e.g. CANNOT, COULD'NT, WON'T)
VVB	base form of lexical verb (except the infinitive)(e.g. TAKE, LIVE)
VVD	past tense form of lexical verb (e.g. TOOK, LIVED)
VVG	-ing form of lexical verb (e.g. TAKING, LIVING)
VVI	infinitive of lexical verb
VVN	past participle form of lex. verb (e.g. TAKEN, LIVED)
VVZ	-s form of lexical verb (e.g. TAKES, LIVES)
// //
XX0	the negative NOT or N'T
ZZ0	alphabetical symbol (e.g. A, B, c, d)
*/

if (isset($_POST['submit'])) {
    $input = $_REQUEST['input'];

    if (isset($input)) {
        $tagger = new BrillTagger();
        foreach($tagger->tag($input) as $tag) {
            echo '<br>' ;
            echo($tag["token"] . " ______ " . $tag["tag"]) ;
        }
    }
}
?>
<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
        <title>Sentence tagger</title>
    </head>
    <body>
        <h1>Sentence tagger</h1>

        <h2>Input text</h2>
        <form method="post" action="" name="markov">
            <textarea rows="2" cols="80" name="input"></textarea>
            <br/>
            <br/>
            <input type="submit" name="submit" value="GO" />
        </form>

    </body>
</html>
       
