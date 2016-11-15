<?php

/**
 * Jérémy Ferrero<br/>
 * Compilatio <br/>
 * GETALP - Laboratory of Informatics of Grenoble <br/>
 *
 * This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.
 * For more information, see http://creativecommons.org/licenses/by-sa/4.0/
 */

/**
 * @class SPARQLInterface
 * 
 * Classe permettant de requêter une base de données SPARQL.
 */
class SPARQLInterface {
    /*     * *****************************************************************************************
     *                                        VARIABLES
     * **************************************************************************************** */

    /**
     * PREFIX.
     * @var string   $prefix
     */
    private $prefix = '';

    /**
     * Uri de la base de données SPARQL à interroger.
     * @var string   $db
     */
    private $db = '';

    /**
     * Résultat de la requête.
     * @var array    $result
     */
    private $result = array();

    /*     * *****************************************************************************************
     *                                        CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe SPARQLInterface.
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
        return $this->result;
    }

    /**
     * Retourne le résultat sous la forme d'une liste (un tableau).
     * @return  array   Résultat
     */
    public function getResultInList() {
        return $this->parseXMLString('literal', $this->result);
    }

    /**
     * Retourne le nombre de résultats.
     * @return  int Nombre de résultats
     */
    public function getResultNumber() {
        return count(getResultInList($this->result));
    }

    /*     * *****************************************************************************************
     *                                          METHODS
     * **************************************************************************************** */

    /**
     * Ajoute un PREFIX à l'instance en cours.
     * @param   string  $prefix     Raccourci du PREFIX
     * @param   string  $uri        URI (adresse) du PREFIX
     */
    public function addPrefixe($prefix, $uri) {
        $this->prefix .= "PREFIX " . $prefix . ": <" . $uri . ">\n";
    }

    /**
     * Etablie la connexion avec la base SPARQL.
     * @param   string  $uri    URI
     */
    public function connect($uri) {
        $this->db = $uri;
    }

    /**
     * Lance une requête.
     * @param   string  $request    Requête à éxecuter
     */
    public function query($request) {
        try {
            $url = $this->db . '?' . 'query=' . urlencode($this->prefix . $request);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $this->result = $response;

            if (!$this->result) {
                throw new Exception('ERREUR SPARQL : Requête SPARQL non valide ou erreur lors de la connexion.');
            }
        } catch (Exception $e) {
            die($e);
        }
    }

    /**
     * Extrait le contenu d'une balise spécifique d'une chaîne XML.
     * @param   string  $xmlTag         Balise XML dont on veut extraire le contenu
     * @param   string  $xmlString      Chaîne XML à parser
     * @param   boolean $conserveXMLTag Le mode d'extraction
     * <br/><br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;true</b>&nbsp;&nbsp;&nbsp;Conserve les balises autour des matches. 
     * <br/><b>&nbsp;&nbsp;&nbsp;false</b>&nbsp;&nbsp;&nbsp;Ignore les balises XML et retourne seulement le contenu des matches. <br/>
     * Si rien n'est précisé, la valeur <b>false</b> sera appliquée par défaut.
     * @return  mixed   Retourne le tableau des matches
     */
    private function parseXMLString($xmlTag, $xmlString, $conserveXMLTag = false) {
        $matches = array();
        $found = preg_match_all('#<' . $xmlTag . '(?:\s+[^>]+)?>(.*?)' . '</' . $xmlTag . '>#s', $xmlString, $matches);

        if ($xmlString == false) {
            // Si la chaîne est vide.
            unset($matches);
            $result = false;
        } else if ($found != false) {
            if (!$conserveXMLTag) {
                // Ignore les balises XML et retourne seulement le contenu de celles-ci.
                $result = $matches[1];
            } else {
                // Retourne le contenu dans les balises XML identifiées.
                $result = $matches[0];
            }
        } else {
            // Si aucun pattern n'a été trouvé.
            unset($matches);
            $result = false;
        }
        return $result;
    }

}
