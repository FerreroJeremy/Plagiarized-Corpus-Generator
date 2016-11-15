<?php

/**
 * Jérémy Ferrero<br/>
 * Compilatio <br/>
 * GETALP - Laboratory of Informatics of Grenoble <br/>
 *
 * This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.
 * For more information, see http://creativecommons.org/licenses/by-sa/4.0/
 */
/* * *****************************************************************************************
 *                                      INCLUDES
 * **************************************************************************************** */

require_once("DBNaryInterface.php");

/**
 * @class ObfuscationGenerator
 * 
 * Classe permettant de bruiter un fragment textuel afin de le rendre plus difficile à comparer ou rechercher.
 */
class ObfuscationGenerator {
    /*     * *****************************************************************************************
     *                                      VARIABLES
     * **************************************************************************************** */

    /**
     * Texte.
     * @var string  $text 
     */
    private $text = '';

    /**
     * Type d'obfuscation à opérer sur le texte.
     * <br/>Utilisation :
     * <br/><b>&nbsp;-1 :</b> Aucune obfuscation (copie exacte).
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre un certain pourcentage de mots au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @var int $type 
     */
    private $type = -1;

    /**
     * Complexité d'obfuscation à opérer sur le texte.<br/>
     * Comprise entre 0 et 1 ou 0 et 100.
     * @var float   $complexity 
     */
    private $complexity = 0.0;

    /**
     * Instance de l'interface avec DBNary.
     * @var DBNaryInterface $db
     */
    private $db = null;

    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe ObfuscationGenerator.
     */
    public function __construct() {
        $this->db = new DBNaryInterface();
        $this->db->connect();
    }

    /*     * *****************************************************************************************
     *                                      GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le texte.
     * @return  string  Texte
     */
    public function getText() {
        return $this->text;
    }

    /*     * *****************************************************************************************
     *                                       SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un texte.
     * @param   string  $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * Affecte un type d'obfuscation.
     * <br/>Utilisation :
     * <br/><b>&nbsp;-1 :</b> Aucune obfuscation (copie exacte).
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre des mots au sein d'un certain pourcentage de phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @param   int     $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Affecte une complexité.<br/>
     * Comprise entre 0 et 1 ou 0 et 100.
     * @param   float   $complexity
     */
    public function setComplexity($complexity) {
        $this->complexity = $complexity;
    }

    /*     * *****************************************************************************************
     *                                       METHODS
     * **************************************************************************************** */

    /**
     * Ajoute du bruit dans le texte en fonction du type d'obfuscation choisi afin de rendre la détection de similitudes plus complexe.
     */
    public function run() {
        $this->normalizeComplexity();

        switch ($this->type) {
            case 0:
                $this->synonymsSubstitution();
                break;
            case 1:
                $this->splitWordOrder();
                break;
            case 2:
                $this->addNoise();
                break;
            case 3:
                $this->deleteWords();
                break;
            case 4:
                $this->deleteLastCharacterOfWords();
                break;
            default:
                // ne rien faire.
                break;
        }
    }

    /**
     * Substitue un certain pourcentage de mots par leur synonyme.
     */
    private function synonymsSubstitution() {
        // On découpe le texte en mots.
        $words = explode(" ", $this->text);
        $wordsNumber = count($words);
        // Un mot tous les X mots (avec X définit grâce au pourcentage).
        $step = $wordsNumber / ($this->complexity * $wordsNumber);
        for ($i = 0; $i < $wordsNumber; $i+=$step) {
            // On le remplace par un synonyme.
            $synonym = $this->db->getNyms($words[$i], 'french');
            if (!empty($synonym) && $this->wordCountAccordingMicrosoftWordApproach($synonym) < 2) {
                $words[$i] = $synonym;
            }
        }
        // Reconstruction du texte.
        $this->text = implode(' ', $words);
    }

    /**
     * Inverse l'ordre des mots au sein d'un certain pourcentage de phrases.
     */
    private function splitWordOrder() {
        // On découpe le texte en phrases.
        $sentences = $this->splitText($this->text, 15);
        $sentenceNumber = count($sentences);
        // Une phrase toutes les X phrases (X définit grâce au pourcentage).
        $step = $sentenceNumber / ($this->complexity * $sentenceNumber);
        // On mélange l'ordre des mots de la phrase.
        for ($i = 0; $i < $sentenceNumber; $i+=$step) {
            // Découpe de la phrase en mots.
            $words = explode(" ", $sentences[$i]);
            // Mélange.
            shuffle($words);
            // Reconstruction de la phrase.
            $sentences[$i] = implode(' ', $words);
        }
        // Reconstruction du texte.
        $this->text = implode(' ', $sentences);
    }

    /**
     * Ajoute un certain pourcentage de bruit au sein des phrases.
     */
    private function addNoise() {
        // On créé un tableau de mots quelconques.
        $randomWords = array('v', 'vn', 'vna', 'vi', 'bruit', 'noise', 'quelconque');
        $randomWordNumber = count($randomWords);
        // On découpe le texte en mots.
        $words = explode(" ", $this->text);
        $wordsNumber = count($words);
        // Un mot tous les X mots (avec X définit grâce au pourcentage).
        $step = $wordsNumber / ($this->complexity * $wordsNumber);
        for ($i = 0; $i < $wordsNumber; $i+=$step) {
            // On tire une variable aléatoire.
            $k = rand(0, $randomWordNumber - 1);
            // On rajoute un autre mot après.
            $words[$i] = $words[$i] . ' ' . $randomWords[$k];
        }
        // Reconstruction du texte.
        $this->text = implode(' ', $words);
    }

    /**
     * Supprime du texte un certain pourcentage de mots.
     */
    private function deleteWords() {
        // On découpe le texte en mots.
        $words = explode(" ", $this->text);
        $wordsNumber = count($words);
        // Un mot tous les X mots (avec X définit grâce au pourcentage).
        $step = $wordsNumber / ($this->complexity * $wordsNumber);
        for ($i = 0; $i < $wordsNumber; $i+=$step) {
            // On le supprime.
            unset($words[$i]);
        }
        // Reconstruction du texte.
        $this->text = implode(' ', $words);
    }

    /**
     * Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     */
    private function deleteLastCharacterOfWords() {
        // On découpe le texte en mots.
        $words = explode(" ", $this->text);
        $wordsNumber = count($words);
        // Un mot tous les X mots (avec X définit grâce au pourcentage).
        $step = $wordsNumber / ($this->complexity * $wordsNumber);
        for ($i = 0; $i < $wordsNumber; $i+=$step) {
            // Si le mot est un mot alphanumérique et plus long que 2 caractères.
            if ($this->isAlphanumericWord($words[$i]) && strlen($words[$i]) > 2) {
                // On supprime la dernière lettre de ce mot.
                $words[$i] = mb_substr($words[$i], 0, -1);
            }
        }
        // Reconstruction du texte.
        $this->text = implode(' ', $words);
    }

    /**
     * Retourne le nombre de mots d'une chaîne de caractère selon la méthode employé par Microsoft Word. <br/>
     * Les séparateurs entre les mots sont uniquement des espaces ou caractères blancs.
     * @param   string  $text   Chaîne de caractère
     * @return  int     Nombre de mots
     */
    private function wordCountAccordingMicrosoftWordApproach($text) {
        return count(explode(" ", $text));
    }

    /**
     * Normalise la complexité lorsque celle-ci est non comprise entre 0 et 1.
     */
    private function normalizeComplexity() {
        if ($this->complexity < 0) {
            $this->complexity = 0;
        }
        if ($this->complexity > 1) {
            $this->complexity = $this->complexity / 100;
        }
    }

    /**
     * Méthode segmentant en sous parties textuelles individuelles. <br/>
     * Conserve les phrases.
     * @return  array   Tableau des segments
     */
    private function splitText($text) {
        $temporarySegments = array();
        $textOfSegment = '';
        $segments = $this->multiexplode(array('. ', '! ', '? '), $this->removeUselessSpaces($text));
        $wordNumber = 0;
        foreach ($segments as $segment) {
            $wordNumber += $this->wordCountAccordingMicrosoftWordApproach($segment);
            $textOfSegment .= ' ' . $segment . '.';
            if ($wordNumber >= 2) {
                array_push($temporarySegments, trim($textOfSegment));
                $textOfSegment = '';
                $wordNumber = 0;
            }
        }
        return $temporarySegments;
    }

    /**
     * Enlève les espaces et blancs inutiles en surplus dans un texte.
     * @param   string  $string     Texte auquel on veut ôter les espaces et blancs inutiles
     * @return  string  Nouveau texte
     */
    private function removeUselessSpaces($string) {
        // Enlève les caractères "espace", équivalent à[ \t\n\r\f]
        // resp. l'espace standard, la tabulation, le saut de ligne, le retour chariot et le saut de page.
        // Enlève en plus les retours chariot.
        $string2 = str_replace(CHR(13) . CHR(10), "", $string);
        return preg_replace('~\s+~', ' ', $string2);
    }

    /**
     * Coupe une chaîne en plusieurs sous-chaînes en se servant de délimiteurs. <br/> 
     * Agit comme <b>explode</b> mais permet plusieurs délimiteurs. <br/> Ne renvoie pas les sous-chaînes vide.
     * @param   array   $delimiters     Tableau des délimiteurs pour splitter la chaîne.
     * @param   string  $string         Chaîne à splitter.
     * @return  array   Tableau des sous-chaînes de <b>$string</b> extraites avec les délimiteurs <b>$delimiters</b>.
     */
    private function multiexplode($delimiters, $string) {
        // Dans la chaîne on remplace tout les délimiteurs du tableau par le premier délimiteurs du tableau de délimiteurs.
        $newString = str_replace($delimiters, $delimiters[0], $string);
        // On split ensuite sur ce seul et même délimiteur.
        $textExploded = preg_split("~" . preg_quote($delimiters[0], '/') . "~", $newString, null, PREG_SPLIT_NO_EMPTY);
        return $textExploded;
    }

    /**
     * Vérifie si la chaîne en entrée est seulement composée de caractères alpha-numériques.
     * @param   string  $string     Chaîne à vérifier.
     * @return  boolean <b>true</b> si la chaîne est alpha-numérique, <b>false</b> sinon.
     */
    private function isAlphanumericWord($string) {
        $test = true;
        $result = "";
        // On cherche tous les caractères autre que alphabétique.
        preg_match("~([^A-Za-z\-0123456789'])~", $string, $result);
        // Si on en trouve, le mot n'est pas alphanumérique.
        if (!empty($result)) {
            $test = false;
        }
        return $test;
    }

}
