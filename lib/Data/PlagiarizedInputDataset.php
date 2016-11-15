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
 * @class PlagiarizedInputDataset
 * 
 * Classe représentant le corpus de documents représentant les documents contenant du plagiat.
 */
class PlagiarizedInputDataset extends InputDataset {
    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe PlagiarizedInputDataset.
     */
    public function __construct() {
        parent::__construct();
    }

    /*     * *****************************************************************************************
     *                                       METHODS
     * **************************************************************************************** */

    /**
     * Retourne un fichier peu utilisé en fonction d'un certain seuil en cherchant de façon aléatoire jusqu'à en trouver un.
     * @param   int $limit  Nombre d'utilisations maximal déterminant ce qu'est un fichier peu utilisé
     * @return  int Identifiant du fichier
     */
    public function getRarelyUsedFileUntilFind($limit = 0) {
        if ($limit <= 0) {
            $randomNumber = rand(0, count($this->documents) - 1);
        } else {
            $test = true;
            while ($test) {
                $randomNumber = rand(0, count($this->documents) - 1);
                if ($this->documents[$randomNumber]['usage'] == $limit) {
                    $test = false;
                }
            }
        }
        return $this->documents[$randomNumber]['path'];
    }

    /**
     * Retourne un fichier peu utilisé en fonction d'un certain seuil en cherchant de façon ordonnée et s'arrêtant au premier trouvé.
     * @param   int $limit  Nombre d'utilisations maximal déterminant ce qu'est un fichier peu utilisé
     * @return  int Identifiant du fichier
     */
    public function getCertainlyRarelyUsedFile($limit = 0) {
        if ($limit <= 0) {
            $i = rand(0, count($this->documents) - 1);
        } else {
            $test = true;
            while ($test) {
                $randomNumber = rand(0, count($this->documents) - 1);
                $i = $this->getNextRarelyUsedFile($randomNumber, $limit);
                if ($this->documents[$i]['usage'] <= $limit) {
                    $test = false;
                }
            }
        }
        return $this->documents[$i]['path'];
    }

    /**
     * Retourne un fichier peu utilisé en fonction d'un certain seuil 
     * à partir d'une certaine position dans le jeu de documents et en cherchant de façon ordonnée.
     * @param   int $current    Position courrante
     * @param   int $limit      Nombre d'utilisations maximal déterminant ce qu'est un fichier peu utilisé
     * @return  int Identifiant du fichier
     */
    public function getNextRarelyUsedFile($current, $limit = 0) {
        if ($limit <= 0) {
            $i = rand(0, count($this->documents) - 1);
        } else {
            for ($i = $current, $size = count($this->documents); $i < $size; $i++) {
                if ($this->documents[$i]['usage'] <= $limit) {
                    break;
                }
            }
            if ($i == count($this->documents) && $this->documents[$i]['usage'] >= $limit) {
                $i = rand(0, count($this->documents) - 1);
            }
        }
        return $i;
    }

}
