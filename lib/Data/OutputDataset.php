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
 * @class OutputDataset
 * 
 * Classe représentant le corpus de documents de sortie de l'application.
 */
class OutputDataset extends Dataset {
    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe OutputDataset.
     */
    public function __construct() {
        parent::__construct();
    }

    /*     * *****************************************************************************************
     *                                       GETTER
     * **************************************************************************************** */

    /**
     * Retourne la taille moyenne (en nombre de mots) des documents générés dans le corpus de sortie.
     * @return  float   Nombre de mots moyen
     */
    public function getAverageWordNumber() {
        $totalWordNumber = 0;
        foreach ($this->documents as $document) {
            $totalWordNumber += $document->getWordNumber();
        }
        return number_format($totalWordNumber / $this->getDocumentNumber(), 2, '.', '');
    }

    /**
     * Retourne le nombre moyen de fragments contenus dans les documents générés dans le corpus de sortie.
     * @return  float   Nombre de fragments moyen
     */
    public function getAverageFragmentNumber() {
        $totalFragmentNumber = 0;
        foreach ($this->documents as $document) {
            $totalFragmentNumber += $document->getFragmentNumber();
        }
        return number_format($totalFragmentNumber / $this->getDocumentNumber(), 2, '.', '');
    }

    /**
     * Retourne le pourcentage de plagiat moyen des documents générés dans le corpus de sortie.
     * @return  float   Pourcentage de plagiat moyen
     */
    public function getAveragePlagiarizedPercentage() {
        $total = 0;
        foreach ($this->documents as $document) {
            $total += $document->getPlagiarizedPercentage();
        }
        return number_format($total / $this->getDocumentNumber(), 2, '.', '');
    }

    /*     * *****************************************************************************************
     *                                       METHODS
     * **************************************************************************************** */

    /**
     * Ajoute un document dans le corpus.
     * @param   Document    $document   Document
     */
    public function addDocument($document) {
        array_push($this->documents, $document);
    }

    /**
     * Calcule les méta-informations de chaque document nécessaires à leur écriture. <br/>
     * Calcule le nombre de mots et le pourcentage de plagiat contenu dans chaque document.
     */
    public function calculateData() {
        foreach ($this->documents as $document) {
            $document->calculateData();
        }
    }

    /**
     * Ecrit les documents du corpus au format plain texte.
     */
    public function generatePlainTextDocuments() {
        foreach ($this->documents as $document) {
            $document->writeAsPlainText($this->directoryPath);
        }
    }

    /**
     * Ecrit les documents du corpus en encadrant le texte des fragments avec les metadata au format XML.
     */
    public function generatePlainTextDocumentsWithXmlMetaData() {
        foreach ($this->documents as $document) {
            $document->writePlainTextWithXmlMetaData($this->directoryPath);
        }
    }

    /**
     * Ecrit les documents du corpus au format employé lors de la PAN (seulement les metadata au format XML).
     */
    public function generateXmlMetaData() {
        foreach ($this->documents as $document) {
            $document->writeOnlyXmlMetaData($this->directoryPath);
        }
    }

}
