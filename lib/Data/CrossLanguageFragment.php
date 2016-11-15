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

require_once("Fragment.php");

/**
 * @class CrossLanguageFragment
 * 
 * Classe représentant un fragment dans un contexte cross-lingue, le cas où la source et l'extrait suspect sont écrit dans deux langues différentes.
 */
class CrossLanguageFragment extends Fragment {
    /*     * *****************************************************************************************
     *                                      VARIABLES
     * **************************************************************************************** */

    /**
     * Texte, contenu du fragment dans la source d'origine.
     * @var string  $parallelText
     */
    private $parallelText = '';

    /**
     * Langue dans laquelle est écrite le fragment dans la source d'origine.
     * @var string  $parallelLanguage
     */
    private $parallelLanguage = '';

    /**
     * Position de départ du fragment dans la source d'origine.
     * @var int $parallelSrcOffset
     */
    private $parallelSrcOffset = 0;

    /**
     * Longueur en nombre de caractères du fragment dans la source d'origine.
     * @var int $parallelSrcLength
     */
    private $parallelSrcLength = 0;

    /**
     * Longueur en nombre de mots du fragment dans la source d'origine.
     * @var int $parallelSrcWordNumber
     */
    private $parallelSrcWordNumber = 0;

    /**
     * Source d'origine du fragment.
     * @var string  $parallelSource
     */
    private $parallelSource = '';

    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe CrossLanguageFragment.
     */
    public function __construct() {
        parent::__construct();
    }

    /*     * *****************************************************************************************
     *                                      GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le texte, le contenu du fragment dans la source d'origine.
     * @return  string  Texte
     */
    public function getParallelText() {
        return $this->parallelText;
    }

    /**
     * Retourne la langue dans laquelle est écrite le fragment dans la source d'origine.
     * @return  string  Langue
     */
    public function getParallelLanguage() {
        return $this->parallelLanguage;
    }

    /**
     * Retourne la position de départ du fragment dans la source d'origine.
     * @return  int Position de départ
     */
    public function getParallelSrcOffset() {
        return $this->parallelSrcOffset;
    }

    /**
     * Retourne la longueur en nombre de caractères du fragment dans la source d'origine.
     * @return  int Taille
     */
    public function getParallelSrcLength() {
        return $this->parallelSrcLength;
    }

    /**
     * Retourne la longueur en nombre de mots du fragment dans la source d'origine.
     * @return  int Nombre de mots
     */
    public function getParallelSrcWordNumber() {
        return $this->parallelSrcWordNumber;
    }

    /**
     * Retourne la source d'origine du fragment.
     * @return  string  Source
     */
    public function getParallelSource() {
        return $this->parallelSource;
    }

    /*     * *****************************************************************************************
     *                                       SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un contenu au fragment dans la source originale.
     * @param   string  $text   Texte
     */
    public function setParallelText($text) {
        $this->parallelText = $text;
    }

    /**
     * Affecte une langue au fragment dans la source originale.
     * @param   string  $language   Langue
     */
    public function setParallelLanguage($language) {
        $this->parallelLanguage = $language;
    }

    /**
     * Affecte une position de départ dans la source originale au fragment.
     * @param   int $offset Position de départ
     */
    public function setParallelSrcOffset($offset) {
        $this->parallelSrcOffset = $offset;
    }

    /**
     * Affecte une longueur en nombre de caractères au fragment dans la source originale.
     * @param   int $length Taille
     */
    public function setParallelSrcLength($length) {
        $this->parallelSrcLength = $length;
    }

    /**
     * Affecte une longueur en nombre de mots au fragment dans la source originale.
     * @param   int $length Nombre de mots
     */
    public function setParallelSrcWordNumber($length) {
        $this->parallelSrcWordNumber = $length;
    }

    /**
     * Affecte une source d'origine au fragment.
     * @param   string  $src    Source
     */
    public function setParallelSource($src) {
        $this->parallelSource = $src;
    }

}
