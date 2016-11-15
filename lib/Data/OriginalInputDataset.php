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

require_once("InputDataset.php");

/**
 * @class OriginalInputDataset
 * 
 * Classe représentant le corpus de documents représentant les sources originales.
 */
class OriginalInputDataset extends InputDataset {
    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe OriginalInputDataset.
     */
    public function __construct() {
        parent::__construct();
    }

    /*     * *****************************************************************************************
     *                                       METHODS
     * **************************************************************************************** */

    /**
     * Retourne un fichier aléatoirement.
     * @return  int Identifiant du fichier
     */
    public function getRandomFile() {
        return $this->documents[rand(0, count($this->documents) - 1)]['path'];
    }

}
