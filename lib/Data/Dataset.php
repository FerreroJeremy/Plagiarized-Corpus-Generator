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
 * @class Dataset
 * 
 * Classe représentant un corpus de documents.
 */
class Dataset {
    /*     * *****************************************************************************************
     *                                      VARIABLES
     * **************************************************************************************** */

    /**
     * Chemin vers le répertoire des documents représentant le corpus.
     * @var string   $directoryPath
     */
    protected $directoryPath = '';

    /**
     * Ensemble de documents représentant le corpus de documents.
     * @var array    $documents
     */
    protected $documents = array();

    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe Dataset.
     */
    public function __construct() {
        
    }

    /*     * *****************************************************************************************
     *                                      GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le chemin vers le répertoire des documents représentant le corpus.
     * @return  string  Chemin
     */
    public function getPath() {
        return $this->directoryPath;
    }

    /**
     * Retourne le nombre de documents actuels dans le corpus.
     * @return  int Nombre de documents
     */
    public function getDocumentNumber() {
        return count($this->documents);
    }

    /*     * *****************************************************************************************
     *                                       SETTERS
     * **************************************************************************************** */

    /**
     * Affecte le chemin vers le répertoire des documents représentant le corpus.
     * @param   string  $path   Chemin
     */
    public function setPath($path) {
        $this->directoryPath = $path;
    }

    /*     * *****************************************************************************************
     *                                        METHODS
     * **************************************************************************************** */

    /**
     * Affiche les documents du corpus.
     */
    public function display() {
        echo '<pre>';
        print_r($this->documents);
        echo '</pre>';
    }

    /*
     * Supprime les documents du corpus (physiquement et conceptuellement parlant).
     */
    public function clear() {
        // Ouvre le dossier.
        $folder = opendir($this->directoryPath);
        // Tant que le dossier n'est pas vide.
        while ($file = readdir($folder)) {
            // Pour chaque fichier.
            if ($file != "." && $file != "..") {
                // Suppression du fichier courant.
                unlink($this->directoryPath . '/' . $file);
            }
        }
        // Fermeture du répertoire.
        closedir($folder);
        // Vide le tableau représentant les documents de l'instance.
        $this->documents = array();
    }

}
