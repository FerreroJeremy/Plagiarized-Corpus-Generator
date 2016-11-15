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

require_once("Dataset.php");

/**
 * @class InputDataset
 * 
 * Classe représentant un corpus de documents en entrée.
 */
class InputDataset extends Dataset {
    /*     * *****************************************************************************************
     *                                      VARIABLES
     * **************************************************************************************** */

    /**
     * Type de documents en entrée.
     * <br/><br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Documents par pointeur sur répertoire.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Urls contenues dans un fichier par pointeur sur le fichier (une par ligne).
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Urls contenues dans un flux de texte (une par ligne).
     * @var int  $inputDocumentType
     */
    protected $inputDocumentType = 0;

    /**
     * Zone de texte contenant les Urls (une par ligne).
     * @var string  $textArea
     */
    protected $textArea = '';

    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe InputDataset.
     */
    public function __construct() {
        parent::__construct();
    }

    /*     * *****************************************************************************************
     *                                       SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un type de documents en entrée.
     * @param   int $type   Type
     * <br/><br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Documents par pointeur sur répertoire.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Urls contenues dans un fichier par pointeur sur le fichier (une par ligne).
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Urls contenues dans un flux de texte (une par ligne).
     */
    public function setInputDocumentType($type) {
        $this->inputDocumentType = $type;
    }

    /**
     * Affecte un texte dans la zone de texte destinée à recevoir les Urls (une par ligne).
     * @param   string  $textArea   Texte
     */
    public function setTextArea($textArea) {
        $this->textArea = $textArea;
    }

    /*     * *****************************************************************************************
     *                                       METHODS
     * **************************************************************************************** */

    /**
     * Ajoute un fichier ou une url au corpus.
     * @param   string  $file   Chemin du fichier ou Url.
     * @param   string  $type   "URL" ou extension si c'est un document/fichier.
     */
    public function add($file, $type) {
        array_push($this->documents, array('path' => $file, 'type' => $type, 'usage' => 0));
    }

    /**
     * Charge les documents formant le corpus en fonction de leur type.
     */
    public function loadDataset() {
        switch ($this->inputDocumentType) {
            case 1:
                $this->loadUrlsFromFilePath();
                break;
            case 2:
                $this->loadUrlsFromTextArea();
                break;
            default:
                $this->loadFilesFromDirectoryPath();
                break;
        }
    }

    /**
     * Charge les fichiers à partir du chemin pointant sur un repertoire.
     */
    private function loadFilesFromDirectoryPath() {
        $files = glob($this->directoryPath . '/*');
        asort($files);

        foreach ($files as $file) {
            $extension = mb_strtoupper(pathinfo($file, PATHINFO_EXTENSION), 'UTF-8');
            if ($extension == 'TXT') {
                array_push($this->documents, array('path' => $file, 'type' => $extension, 'usage' => 0));
            }
        }
    }

    /**
     * Charge les urls à partir du chemin pointant sur un fichier.
     */
    private function loadUrlsFromFilePath() {
        $files = glob($this->directoryPath . '/*');
        foreach ($files as $file) {
            $urls = $this->extractLinesFromFile($file);
            foreach ($urls as $url) {
                array_push($this->documents, array('path' => trim($url), 'type' => 'URL', 'usage' => 0));
            }
        }
    }

    /**
     * Charge les urls à partir de la zone de texte.
     */
    private function loadUrlsFromTextArea() {
        $urls = explode(chr(10), $this->textArea);

        foreach ($urls as $url) {
            array_push($this->documents, array('path' => trim($url), 'type' => 'URL', 'usage' => 0));
        }
    }

    /**
     * Charge un fichier dans un tableau de strings (ses lignes).
     * @param   string  $filename   Nom du fichier dont le texte est à extraire.
     * @return  array   Tableau des lignes du fichier.
     */
    private function extractLinesFromFile($filename) {
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
            echo($e);
            $content = null;
        }
        return $content;
    }

}
