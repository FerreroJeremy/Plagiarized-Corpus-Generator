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

require_once("Encoding.php");

/**
 * @class FragmentExtractor
 * 
 * Classe permettant d'extraire un fragment.
 */
class FragmentExtractor {
    /*     * *****************************************************************************************
     *                                       VARIABLES
     * **************************************************************************************** */

    /**
     * Chemin d'accès à la source du fragment.
     * @var string  $path
     */
    private $path = '';

    /**
     * Chemin d'accès aux documents parallèles à la source du fragment à extraire.
     * @var string  $parallelRessourcePath
     */
    private $parallelRessourcePath = '';

    /**
     * Nombre de mots que doit contenir le fragment.
     * @var int $wordNumber
     */
    private $wordNumber = 0;

    /**
     * Type de fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue
     * @var int $fragmentType
     */
    private $fragmentType = 0;

    /**
     * Contenu (texte) du fragment.
     * @var string  $content
     */
    private $content = '';

    /**
     * Position de départ du fragment dans la source.
     * @var int $offset
     */
    private $offset = 0;

    /**
     * Contenu (texte) du fragment dans une autre langue pris dans un texte parallèle au texte source du fragment.
     * @var string  $contentEquivalent
     */
    private $parallelContent = '';

    /**
     * Position de départ du fragment parallèle dans le texte parallèle au texte source du fragment.
     * @var int $offsetEquivalent
     */
    private $parallelOffset = 0;

    /**
     * Instance de la classe Encoding.
     * @var Encoding    $encoder
     */
    private $encoder = null;

    /**
     * Variable déterminant si l'on conserve ou non les espaces et retours à la ligne dans le contenu des sources extraites.
     * @var boolean  $spacesPreservation
     */
    private $spacesPreservation = FALSE;

    /*     * *****************************************************************************************
     *                                       CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe FragmentExtractor.
     */
    public function __construct() {
        $this->encoder = new Encoding();
    }

    /*     * *****************************************************************************************
     *                                        GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le contenu du fragment.
     * @return  string  Texte
     */
    public function getContentOfFragment() {
        return $this->content;
    }

    /**
     * Retourne la position de départ du fragment extrait dans la source.
     * @return  int Position de départ
     */
    public function getOffsetOfFragment() {
        return $this->offset;
    }

    /**
     * Retourne le contenu du fragment dans une autre langue pris dans un texte parallèle au texte source du fragment.
     * @return  string  Texte
     */
    public function getContentOfParallelFragment() {
        return $this->parallelContent;
    }

    /**
     * Retourne la position de départ du fragment parallèle dans le texte parallèle au texte source du fragment.
     * @return  int Position de départ
     */
    public function getOffsetOfParallelFragment() {
        return $this->parallelOffset;
    }

    /*     * *****************************************************************************************
     *                                        SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un chemin d'accès à la source du fragment à extraire.
     * @param   string  $path   Chemin
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Affecte un chemin d'accès au document parallèle à la source du fragment à extraire.
     * @param   string  $path   Chemin
     */
    public function setParallelRessourcePath($path) {
        $this->parallelRessourcePath = $path;
    }

    /**
     * Affecte un nombre de mots à extraire dans la source pour constituer le fragment.
     * @param   int $wordNumber Nombre de mots
     */
    public function setWordNumber($wordNumber) {
        $this->wordNumber = $wordNumber;
    }

    /**
     * Affecte un type de fragment à extraire.
     * @param   int $fragmentType   Type
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue
     */
    public function setFragmentType($fragmentType) {
        $this->fragmentType = $fragmentType;
    }

    /**
     * Détermine si l'on conserve ou non, lors de l'extraction des ressources, les espaces et retours à la ligne.
     * @param   boolean $spacesPreservation
     */
    public function setSpacesPreservation($spacesPreservation) {
        $this->spacesPreservation = $spacesPreservation;
    }

    /*     * *****************************************************************************************
     *                                        METHODS
     * **************************************************************************************** */

    /**
     * Extrait un fragment d'une source en fonction du type de fragment à extraire.
     */
    public function extract() {
        if ($this->fragmentType == 0) {
            // Cas de l'extraction d'un passage monolingue.
            $this->extractMonolingualFragment();
        } else {
            // Cas de l'extraction d'un passage cross-lingue.
            $this->extractCrossLanguageFragment();
        }
    }

    /**
     * Extrait un fragment monolingue.
     */
    private function extractMonolingualFragment() {
        $punctuation = array('.', '?', '!');
        $length = 0;
        $excerpt = '';
        $isSentence = false;

        // Récupère le contenu du fichier ou de l'url.
        $text = $this->loadContent($this->path);

        if ($text != '') {
            // Segmente ce contenu en mots.
            $words = explode(" ", $text);
            $wordNumber = count($words);
            $max = $wordNumber - $this->wordNumber;
            // Détermine un offset aléatoire.
            $rand = mt_rand(0, $max);
            // On parcourt chaque mot à partir de la position tirée aléatoirement.
            for ($i = $rand, $size = count($words); $i < $size; $i++) {
                // On part à partir d'une nouvelle phrase.
                if ($isSentence) {
                    // On concatène les mots un à un pour construire un fragment.
                    $excerpt .= trim($words[$i]) . ' ';
                    // Si on arrive à la fin d'une phrase et si le fragment en cours de construction dépasse la taille souhaitée.
                    if ($length++ >= $this->wordNumber && in_array($this->getLastLetter(trim($words[$i])), $punctuation)) {
                        // On sort, le fragment est construit.
                        break;
                    }
                }
                // Si on passe un mot avec un point à la fin, le tour suivant, on attaque une nouvelle phrase.
                if (in_array($this->getLastLetter(trim($words[$i])), $punctuation)) {
                    $isSentence = true;
                }
            }
            $this->content = trim($excerpt);
            $this->offset = $this->getOffsetInSource($this->content, $text);
        } else {
            $this->content = '';
            $this->offset = -1;
        }
    }

    /**
     * Extrait un fragment cross-lingue.
     */
    private function extractCrossLanguageFragment() {
        $excerpt = '';
        $parallelExcerpt = '';

        // On extrait le contenu des deux textes (la source et son document parallèle).
        $text = $this->loadContent($this->path);
        $parallelText = $this->loadContent($this->parallelRessourcePath);

        if (($text != '') && ($parallelText != '')) {
            // On extrait les lignes des deux textes (la source et son document parallèle).
            $srcText = $this->extractLinesFromFile($this->path);
            $equivalentSrcText = $this->extractLinesFromFile($this->parallelRessourcePath);
            // On détermine une position de départ à l'extraction aléatoirement.
            $max = count($srcText) - 2;
            // Si il n'y a pas assez de lignes dans le fichier, on part du début.
            if ($max <= 0) {
                $rand = 0;
            } else {
                $rand = mt_rand(0, count($srcText));
            }

            // On parcourt chaque ligne de la source à partir de la position tirée aléatoirement.
            for ($i = $rand, $size = count($srcText); $i < $size; $i++) {
                // On construit en même temps les fragments (celui de la source et celui de son document parallèle) en concaténant les lignes.
                // Cela est possible car les documents sont parallèles, ils possèdent le même nombre de lignes qui sont parallèles deux à deux dans leur ordre d'apparition.
                $excerpt .= trim($srcText[$i]) . ' ';
                $parallelExcerpt .= trim($equivalentSrcText[$i]) . ' ';
                // Si la taille (en nombre de mots) du fragment de la source est supérieur ou égale à la taille souhaitée.
                if ($this->wordCountAccordingMicrosoftWordApproach(trim($excerpt)) >= $this->wordNumber) {
                    // On sort !
                    break;
                }
            }
            $this->content = trim($excerpt);
            $this->parallelContent = trim($parallelExcerpt);
            $this->offset = $this->getOffsetInSource($this->content, $text);
            $this->parallelOffset = $this->getOffsetInSource($this->parallelContent, $parallelText);
        } else {
            $this->content = '';
            $this->parallelContent = '';
            $this->offset = -1;
            $this->parallelOffset = -1;
        }
    }

    /**
     * Retourne le contenu textuel d'un fichier ou d'une url.
     * @param   string  $filename   Nom du fichier dont le texte est à extraire
     * @return  string  Contenu textuel du fichier
     */
    private function loadContent($filename) {
        $content = '';
        try {
            if ($this->spacesPreservation) {
                $content = $this->encoder->toUTF8($this->removeHTML($this->removeScript(file_get_contents($filename))));
            } else {
                $content = $this->encoder->toUTF8($this->removeUselessSpaces($this->removeHTML($this->removeScript(file_get_contents($filename)))));
            }
        } catch (Exception $e) {
            // echo($e);
            $content = '';
        }
        return $content;
    }

    /**
     * Charge un fichier dans un tableau de strings (ses lignes).
     * @param   string  $filename   Nom du fichier dont le texte est à extraire.
     * @return  array   Tableau des lignes du fichier.
     */
    private function extractLinesFromFile($filename) {
        $content = '';
        try {
            // Si le fichier n'existe pas.
            if (!file_exists($filename)) {
                throw new Exception('Fichier "' . $filename . '" introuvable.');
            }
            $file = fopen($filename, "r");
            // Renvoie null si l'ouverture échoue.
            if (!$file) {
                throw new Exception('Fichier "' . $filename . '" existant mais illisible.');
            } else {
                // Tant que l'on est pas à la fin du fichier.
                $p = 0;
                while (!feof($file)) {
                    $line = trim(fgets($file));
                    // Si la ligne n'est pas vide.
                    if ($line != '') {
                        // On la stoque dans une case du tableau.
                        $content[$p++] = $line;
                    }
                }
            }
            // On ferme le fichier en lecture.
            fclose($file);
        } catch (Exception $e) {
            // echo($e);
            $content = '';
        }
        return $content;
    }

    /**
     * Retourne un texte privé du code JavaScript qu'il contenait.
     * @param   string  $html   Texte HTML
     * @return  string  Texte sans JavaScript
     */
    private function removeScript($html) {
        $search = array("'<script[^>]*?>.*?</script>'si",
            "'<style[^>]*?>.*?</style>'si",
            "'<head[^>]*?>.*?</head>'si",
            "'<link[^>]*?>.*?</link>'si",
            "'<object[^>]*?>.*?</object>'si");
        $replace = array("",
            "",
            "",
            "",
            "");
        return preg_replace($search, $replace, $html);
    }

    /**
     * Retourne un texte privé du code HTML qu'il contenait.
     * @param   string  $html   Texte HTML
     * @return  string  Texte sans HTML
     */
    private function removeHTML($html) {
        $content = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $html);
        return strip_tags($content);
    }

    /**
     * Enlève les espaces et blancs inutiles en surplus dans un texte.
     * @param   string  $string     Texte auquel on veut ôter les espaces et blancs inutiles.
     * @return  string  Nouveau texte.
     */
    private function removeUselessSpaces($string) {
        // Enlève les caractères "espace", équivalent à[ \t\n\r\f] 
        // resp. l'espace standard, la tabulation, le saut de ligne, le retour chariot et le saut de page.
        // Enlève en plus les retours chariot.
        $string1 = str_replace(CHR(13) . CHR(10), ' ', $string);
        return preg_replace('~\s+~', ' ', $string1);
    }

    /**
     * Retourne le dernier caractère d'une chaîne.
     * @param   string  $string   Mot dont on veut avoir le dernier caractère
     * @return  string  Dernier caractère de la chaîne <b>$string</b>
     */
    private function getLastLetter($string) {
        mb_internal_encoding("UTF-8");
        return mb_substr($string, mb_strlen($string) - 1, 1);
    }

    /**
     * Retourne la position d'une sous-chaîne de caractères dans une chaîne de caractères.
     * @param   string  $excerpt    Sous-chaîne à rechercher
     * @param   string  $source     Chaîne dans laquelle rechercher
     * @return  int     Position
     */
    private function getOffsetInSource($excerpt, $source) {
        $matches = array();
        if (preg_match_all('~' . preg_quote($excerpt, '~') . '~ui', $source, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $occurrence) {
                return $occurrence[1];
            }
        }
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

}
