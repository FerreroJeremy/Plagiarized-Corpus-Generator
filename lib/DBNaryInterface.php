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

require_once("SPARQLInterface.php");

/**
 * @class DBNaryInterface
 * 
 * Classe permettant de gérer les requêtes DBNary.
 */
class DBNaryInterface {
    /*     * *****************************************************************************************
     *                                        VARIABLES
     * **************************************************************************************** */

    /**
     * Instance d'accès à la base DBNary.
     * @var string  $db
     */
    private $db = "";

    /**
     * Résultat.
     * @var string  $result
     */
    private $result = "";

    /**
     * Langue source.
     * @var string  $langFrom
     */
    private $langFrom;

    /**
     * Langue cible.
     * @var string  $langTo
     */
    private $langTo;

    /*     * *****************************************************************************************
     *                                        CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe DBNaryInterface.
     */
    public function __construct() {
        
    }

    /*     * *****************************************************************************************
     *                                          GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le résultat sous forme d'objet SPARQL. <br/>
     * Retourne le tableau tel qu'il est renvoyé par SPARQL lors de l'éxecution d'une requête.
     * @return  array   Résultat
     */
    public function getResultInSparqlObject() {
        return $this->db->getResultInSparqlObject();
    }

    /**
     * Retourne le résultat sous la forme d'une liste (un tableau).
     * @return  array   Résultat
     */
    public function getResultInList() {
        return $this->db->getResultInList();
    }

    /**
     * Retourne le nombre de résultats. <br/>
     * Plus vulgairement, retourne le nombre de lignes renvoyées lors de l'éxecution de la requête SPARQL.
     * @return  int Nombre de résultats
     */
    public function getResultNumber() {
        return $this->db->getResultNumber();
    }

    /*     * *****************************************************************************************
     *                                          SETTERS
     * **************************************************************************************** */

    /**
     * Affecte une langue source à l'instance.
     * @param   string  $langFrom   Langue
     */
    public function setLanguageFrom($langFrom) {
        $this->langFrom = $langFrom;
    }

    /**
     * Affecte une langue cible à l'instance.
     * @param   string  $langTo     Langue
     */
    public function setLanguageTo($langTo) {
        $this->langTo = $langTo;
    }

    /*     * *****************************************************************************************
     *                                          METHODS
     * **************************************************************************************** */

    /**
     * Ajoute un PREFIX à l'instance en cours.
     * @param   string  $prefix Code du PREFIX
     * @param   string  $uri    URI (adresse) du PREFIX
     */
    public function addPrefixe($prefix, $uri) {
        $this->db->addPrefixe($prefix, $uri);
    }

    /**
     * Etablie la connexion avec la base DBNary.<br/>
     * <b>Remarque : Cette méthode initialise également les PREFIX les plus couremment utilisés.</b>
     * @param   string  $uri    Uri de la base DBNary <br/>
     * Si non précisée, égale à : <b>http://kaiko.getalp.org/sparql</b>
     */
    public function connect($uri = "http://kaiko.getalp.org/sparql") {
        try {
            $this->db = new SPARQLInterface();
            $this->db->connect($uri);
            if (!$this->db) {
                throw new Exception('ERREUR SPARQL : Impossible de se connecter à DBNary : ' . $uri);
            }
            $this->addPrefixe("dbnary", "http://kaiko.getalp.org/dbnary#");
            $this->addPrefixe("lexvo", "http://lexvo.org/id/iso639-3/");
            $this->addPrefixe("fra", "http://kaiko.getalp.org/dbnary/fra/");
            $this->addPrefixe("eng", "http://kaiko.getalp.org/dbnary/eng/");
            $this->addPrefixe("deu", "http://kaiko.getalp.org/dbnary/deu/");
            $this->addPrefixe("ita", "http://kaiko.getalp.org/dbnary/ita/");
            $this->addPrefixe("spa", "http://kaiko.getalp.org/dbnary/spa/");
            $this->addPrefixe("lemon", "http://www.lemon-model.net/lemon#");
        } catch (Exception $e) {
            die($e);
        }
    }

    /**
     * Lance une requête.
     * @param   string  $request    Requête
     */
    public function query($request) {
        try {
            $this->db->query($request);
            $this->result = $this->db->getResultInSparqlObject();
            
            if (!$this->result) {
                throw new Exception('ERREUR SPARQL : Requête DBNary non valide ou erreur lors de la connexion.');
            }
        } catch (Exception $e) {
            die($e);
        }
    }

    /**
     * Retourne toutes les traductions d'un mot d'un langage source dans un langage cible.
     * @param   string  $word       Mot
     * @param   string  $langFrom   Langue source <br/>
     * Si non précisée la langue source de l'instance est prise par défaut
     * @param   string  $langTo     Langue cible <br/>
     * Si non précisée la langue cible de l'instance est prise par défaut
     */
    public function getTranslations($word, $langFrom = -1, $langTo = -1) {
        if ($langFrom == -1) {
            $langFrom = $this->langFrom;
        }
        if ($langTo == -1) {
            $langTo = $this->langTo;
        }
        $languageFrom = $this->normalizeLanguage($langFrom, 3);
        $languageTo = $this->normalizeLanguage($langTo, 3);
        $request = "SELECT DISTINCT ?f "
                . "WHERE { "
                . $languageFrom . ":" . $word . " dbnary:refersTo ?le. "
                . "?t dbnary:isTranslationOf ?le. "
                . "?t dbnary:targetLanguage lexvo:" . $languageTo . ". "
                . "?t dbnary:writtenForm ?f . "
                . "OPTIONAL {?t dbnary:gloss ?o}"
                . "}";
        $this->query($request);
    }

    /**
     * Récupère toute les relations "nymes" d'un mot. <br/>
     * Les relations "nymes" sont les relations du types : homonyme, synonyme, antonyme, hyperonyme, etc. <br/>
     * <b>Remarque : Cette méthode nécessite de connaître la langue du mot. </b>
     * @param   string  $word   Mot
     * @param   string  $lang   Langue <br/>
     * Si non précisée la langue source de l'instance est prise par défaut
     */
    public function getNyms($word, $lang = -1) {
        if ($lang == -1) {
            $lang = $this->langFrom;
        }

        $language = $this->normalizeLanguage($lang, 3);
        $request = "SELECT distinct ?term "
                . "WHERE { "
                . $language . ":" . $word . " dbnary:refersTo ?lf. "
                . "{ "
                . "?lf ?relation ?term. "
                . "?term a dbnary:Vocable"
                . "}  "
                . "}";
        $this->query($request);
        return trim(basename(strip_tags($this->result)));
    }

    /**
     * Normalise les langues au format souhaité.
     * @param   string  $lang           Langue
     * @param   string  $caracterNumber Nombre de caractères sur lesquelles encoder la langue
     * @return  string  Langue encodée
     */
    private function normalizeLanguage($lang, $caracterNumber) {
        $language = $this->toLowerString($lang);
        switch ($caracterNumber) {
            case 2:
                if ($language == "fr" || $language == "french") {
                    $language = "fr";
                } else if ($language == "es" || $language == "sp" || $language == "spanish") {
                    $language = "sp";
                } else if ($language == "de" || $language == "deutsch" || $language == "german") {
                    $language = "de";
                } else if ($language == "it" || $language == "italian") {
                    $language = "it";
                } else { // english
                    $language = "en";
                }
                break;

            default: // 3
                if ($language == "fr" || $language == "french") {
                    $language = "fra";
                } else if ($language == "es" || $language == "sp" || $language == "spanish") {
                    $language = "spa";
                } else if ($language == "de" || $language == "deutsch" || $language == "german") {
                    $language = "deu";
                } else if ($language == "it" || $language == "italian") {
                    $language = "ita";
                } else { // english
                    $language = "eng";
                }
                break;
        }
        return $language;
    }

    /**
     * Passe une chaîne entièrement en minuscule.
     * @param   string  $string Chaîne
     * @return  Chaîne
     */
    private function toLowerString($string) {
        return mb_strtolower($string, 'UTF-8');
    }

}
