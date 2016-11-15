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
 * @class Fragment
 * 
 * Classe représentant un fragment.
 */
class Fragment {
    /*     * *****************************************************************************************
     *                                      VARIABLES
     * **************************************************************************************** */

    /**
     * Texte, contenu du fragment.
     * @var string  $text
     */
    protected $text = '';

    /**
     * Type de fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Original.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Plagié.
     * @var string  $type
     */
    protected $type = '';

    /**
     * Langue dans laquelle est écrite le fragment.
     * @var string  $language
     */
    protected $language = '';

    /**
     * Position de départ dans le document de sortie.
     * @var int $offset
     */
    protected $thisOffset = 0;

    /**
     * Longueur en nombre de caractères du fragment dans le document de sortie.
     * @var int $length
     */
    protected $thisLength = 0;

    /**
     * Longueur en nombre de mots du fragment dans le document de sortie.
     * @var int $wordNumber
     */
    protected $thisWordNumber = 0;

    /**
     * Source d'origine du fragment.
     * @var string  $source
     */
    protected $source = '';

    /**
     * Position de départ du fragment dans le document source.
     * @var int $offset
     */
    protected $srcOffset = 0;

    /**
     * Longueur en nombre de caractères du fragment dans le document source.
     * @var int $length
     */
    protected $srcLength = 0;

    /**
     * Longueur en nombre de mots du fragment dans le document source.
     * @var int $wordNumber
     */
    protected $srcWordNumber = 0;

    /**
     * Type d'obfuscation du fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;-1 :</b> Aucune obfuscation (copie exacte).
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre un certain pourcentage de mots au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @var string  $obfuscationType
     */
    protected $obfuscationType = -1;

    /**
     * Niveau d'obfuscation du fragment.
     * Comprise entre 0 et 1 ou 0 et 100.
     * @var float   $obfuscationComplexity
     */
    protected $obfuscationComplexity = 0.0;

    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe Fragment.
     */
    public function __construct() {
        
    }

    /*     * *****************************************************************************************
     *                                      GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le texte, le contenu du fragment.
     * @return  string  Texte
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Retourne le type du fragment.
     * @return  string  Type
     * <br/>Interprétation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Original.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Plagié.
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Retourne la langue dans laquelle est écrite le fragment.
     * @return  string  Langue
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Retourne la position de départ du fragment dans le document source.
     * @return  int Position de départ
     */
    public function getThisOffset() {
        return $this->thisOffset;
    }

    /**
     * Retourne la longueur en nombre de caractères du fragment dans le document source.
     * @return  int Taille
     */
    public function getThisLength() {
        return $this->thisLength;
    }

    /**
     * Retourne la longueur en nombre de mots du fragment dans le document source.
     * @return  int Nombre de mots
     */
    public function getThisWordNumber() {
        return $this->thisWordNumber;
    }

    /**
     * Retourne la source d'origine du fragment.
     * @return  string  Source
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * Retourne la position de départ du fragment dans le document source.
     * @return  int Position de départ
     */
    public function getSrcOffset() {
        return $this->srcOffset;
    }

    /**
     * Retourne la longueur en nombre de caractères du fragment dans le document source.
     * @return  int Taille
     */
    public function getSrcLength() {
        return $this->srcLength;
    }

    /**
     * Retourne la longueur en nombre de mots du fragment dans le document source.
     * @return  int Nombre de mots
     */
    public function getSrcWordNumber() {
        return $this->srcWordNumber;
    }

    /**
     * Retourne le type d'obfuscation du fragment.
     * @return  int Type d'obfuscation
     * <br/>Interprétation :
     * <br/><b>&nbsp;&nbsp;-1 :</b> Aucune obfuscation (copie exacte).
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre un certain pourcentage de mots au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     */
    public function getObfuscationType() {
        return $this->obfuscationType;
    }

    /**
     * Retourne le niveau d'obfuscation du fragment.
     * @return  int Niveau d'obfuscation
     */
    public function getObfuscationComplexity() {
        return $this->obfuscationComplexity;
    }

    /*     * *****************************************************************************************
     *                                       SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un contenu au fragment.
     * @param   string  $text   Texte
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * Affecte un type au fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Original.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Plagié.
     * @param   string  $type   Type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Affecte une langue au fragment.
     * @param   string  $language   Langue
     */
    public function setLanguage($language) {
        $this->language = $language;
    }

    /**
     * Affecte une position de départ au fragment dans le document.
     * @param   int $offset Position de départ
     */
    public function setThisOffset($offset) {
        $this->thisOffset = $offset;
    }

    /**
     * Affecte une longueur en nombre de caractères au fragment.
     * @param   int $length Taille
     */
    public function setThisLength($length) {
        $this->thisLength = $length;
    }

    /**
     * Affecte une longueur en nombre de mots au fragment.
     * @param   int $wordNumber Nombre de mots
     */
    public function setThisWordNumber($wordNumber) {
        $this->thisWordNumber = $wordNumber;
    }

    /**
     * Affecte une source au fragment.
     * @param   string  $src    Source
     */
    public function setSource($src) {
        $this->source = $src;
    }

    /**
     * Affecte une position de départ au fragment dans le document.
     * @param   int $offset Position de départ
     */
    public function setSrcOffset($offset) {
        $this->srcOffset = $offset;
    }

    /**
     * Affecte une longueur en nombre de caractères au fragment.
     * @param   int $length Taille
     */
    public function setSrcLength($length) {
        $this->srcLength = $length;
    }

    /**
     * Affecte une longueur en nombre de mots au fragment.
     * @param   int $wordNumber Nombre de mots
     */
    public function setSrcWordNumber($wordNumber) {
        $this->srcWordNumber = $wordNumber;
    }

    /**
     * Affecte un type d'obfuscation.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;-1 :</b> Aucune obfuscation (copie exacte).
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre un certain pourcentage de mots au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @param   string  $type   Type d'obfuscation
     */
    public function setObfuscationType($type) {
        $this->obfuscationType = $type;
    }

    /**
     * Affecte un niveau d'obfuscation.
     * @param   float   $complexity Niveau d'obfuscation
     */
    public function setObfuscationComplexity($complexity) {
        $this->obfuscationComplexity = $complexity;
    }

    /**
     * Retourne la valeur par défaut représentant un Fragment, son texte.
     * @return  string  Texte
     */
    public function __toString() {
        return $this->text;
    }

}
