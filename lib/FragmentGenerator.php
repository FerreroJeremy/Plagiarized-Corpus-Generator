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

require_once("LanguageDetection/LanguageDetect.php");
require_once("ObfuscationGenerator.php");
require_once("FragmentExtractor.php");

require_once("Data/MonolingualFragment.php");
require_once("Data/CrossLanguageFragment.php");

/**
 * @class FragmentGenerator
 * 
 * Classe permettant de créer un fragment.
 */
class FragmentGenerator {
    /*     * *****************************************************************************************
     *                                       VARIABLES
     * **************************************************************************************** */

    /**
     * Chemin d'accès à la source du fragment.
     * @var string  $ressourcePath
     */
    private $ressourcePath = '';

    /**
     * Chemin d'accès aux documents parallèles aux documents de plagiat.
     * @var string  $parallelRessourcePath
     */
    private $parallelRessourcesPath = '';

    /**
     * Nombre de mots que doit contenir le fragment.
     * @var int $length
     */
    private $wordNumber = 0;

    /**
     * Type de fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue.
     * @var int $fragmentType
     */
    private $fragmentType = 0;

    /**
     * Type de fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment original.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment plagié.
     * @var int $ressourceType
     */
    private $ressourceType = 0;

    /**
     * Instance de l'extracteur du fragment depuis la ressource.
     * @var FragmentExtractor   $extractor
     */
    private $extractor = null;

    /**
     * Instance du prédicteur de langue.
     * @var LanguageDetect  $languageIdentifier
     */
    private $languageIdentifier = null;

    /**
     * Instance de l'outil d'ajout de bruit dans les textes.
     * @var ObfuscationGenerator    $obfuscationGenerator
     */
    private $obfuscationGenerator = null;

    /**
     * Type d'obfuscation du fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;-1 :</b> Aucune obfuscation (copie exacte).
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre un certain pourcentage de mots au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @var string  $obfuscationType
     */
    private $obfuscationType = -1;

    /**
     * Complexité d'obfuscation à opérer sur le texte.<br/>
     * Comprise entre 0 et 1 ou 0 et 100.
     * @var float   $obfuscationComplexity 
     */
    private $obfuscationComplexity = 0.0;

    /**
     * Instance du fragment créé.
     * @var Fragment    $fragment
     */
    private $fragment = null;

    /*     * *****************************************************************************************
     *                                       CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe FragmentGenerator.
     */
    public function __construct() {
        $this->languageIdentifier = new Text_LanguageDetect();
        $this->languageIdentifier->setNameMode(0);
        $this->extractor = new FragmentExtractor();
        $this->obfuscationGenerator = new ObfuscationGenerator();
    }

    /*     * *****************************************************************************************
     *                                        GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le fragment créé.
     * @return  Fragment    Fragment
     */
    public function getCreatedFragment() {
        return $this->fragment;
    }

    /**
     * Retourne la taille (en nombre de mots) du fragment créé.
     * @return  int Nombre de mots
     */
    public function getWordNumberOfCreatedFragment() {
        return $this->fragment->getWordNumber();
    }

    /*     * *****************************************************************************************
     *                                        SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un chemin d'accès à la source du fragment à créer.
     * @param   string  $path   Chemin
     */
    public function setRessourcePath($path) {
        $this->ressourcePath = $path;
    }

    /**
     * Affecte un chemin d'accès aux documents parallèles aux documents de plagiat.
     * @param   string  $path   Chemin
     */
    public function setParallelRessourcesPath($path) {
        $this->parallelRessourcesPath = $path;
    }

    /**
     * Affecte un nombre de mots à extraire dans la source pour créer le fragment.
     * @param   int $wordNumber Nombre de mots
     */
    public function setWordNumber($wordNumber) {
        $this->wordNumber = $wordNumber;
    }

    /**
     * Affecte un type de fragment à créer.
     * @param   int $type   Type
     * <br/><br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue.
     */
    public function setFragmentType($type) {
        $this->fragmentType = $type;
    }

    /**
     * Affecte un type de fragment à créer.
     * @param   int $type   Type
     * <br/><br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment original.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment plagié.
     */
    public function setRessourceType($type) {
        $this->ressourceType = $type;
    }

    /**
     * Affecte un type d'obfuscation.
     * <br/>Utilisation :
     * <br/><b>&nbsp;-1 :</b> Aucune obfuscation (copie exacte).
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre des mots au sein d'un certain pourcentage de phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @param   int     $type   Type
     */
    public function setObfuscationType($type) {
        $this->obfuscationType = $type;
    }

    /**
     * Affecte une complexité d'obfuscation.<br/> Comprise entre 0 et 1 ou 0 et 100.
     * @param   float   $complexity Complexité
     */
    public function setObfuscationComplexity($complexity) {
        $this->obfuscationComplexity = $complexity;
    }

    /*     * *****************************************************************************************
     *                                        METHODS
     * **************************************************************************************** */

    /**
     * Créer le fragment.
     */
    public function run() {
        // Créer un fragment en fonction du type de fragment demandé.
        if ($this->fragmentType == 0) {
            $this->fragment = new MonolingualFragment();
        } else {
            $this->fragment = new CrossLanguageFragment();
            // On récupère le nom du document parallèle à la source choisie.
            $path_parts = pathinfo($this->ressourcePath);
            // On reconstruit le chemin complet jusqu'à ce document parallèle.
            $parallelRessourcePath = $this->parallelRessourcesPath . '/' . $path_parts['filename'] . '.txt';
            $this->extractor->setParallelRessourcePath($parallelRessourcePath);
        }

        // Extraction du contenu du fragment.
        $this->extractor->setFragmentType($this->fragmentType);
        $this->extractor->setPath($this->ressourcePath);
        $this->extractor->setWordNumber($this->wordNumber);
        $this->extractor->extract();

        $excerpt = $this->extractor->getContentOfFragment();
        $offset = $this->extractor->getOffsetOfFragment();

        // Si une obfuscation est demandée, on la fait.
        if ($this->obfuscationType != -1) {
            $this->fragment->setObfuscationType($this->obfuscationType);
            $this->fragment->setObfuscationComplexity($this->obfuscationComplexity);

            $this->obfuscationGenerator->setText($excerpt);
            $this->obfuscationGenerator->setType($this->obfuscationType);
            $this->obfuscationGenerator->setComplexity($this->obfuscationComplexity);
            $this->obfuscationGenerator->run();
            $excerpt = $this->obfuscationGenerator->getText();
        }

        // Affecte les informations au fragment créé.
        $this->fragment->setText($excerpt);
        $this->fragment->setType($this->ressourceType);
        $this->fragment->setLanguage($this->languageIdentifier->detectSimple($excerpt));
        $this->fragment->setThisLength(strlen($excerpt));
        $this->fragment->setThisWordNumber($this->wordCountAccordingMicrosoftWordApproach($excerpt));
        $this->fragment->setSource($this->ressourcePath);
        $this->fragment->setSrcOffset($offset);
        $this->fragment->setSrcLength($this->fragment->getThisLength());
        $this->fragment->setSrcWordNumber($this->fragment->getThisWordNumber());

        // Si le fragment est un fragment cross-lingue, il requiert quelques informations supplémentaires.
        if ($this->fragmentType == 1) {
            $parallelExcerpt = $this->extractor->getContentOfParallelFragment();
            $this->fragment->setParallelText($parallelExcerpt);
            $this->fragment->setParallelLanguage($this->languageIdentifier->detectSimple($parallelExcerpt));
            $this->fragment->setParallelSrcWordNumber($this->wordCountAccordingMicrosoftWordApproach($parallelExcerpt));
            $this->fragment->setParallelSrcLength(strlen($parallelExcerpt));
            $this->fragment->setParallelSrcOffset($this->extractor->getOffsetOfParallelFragment());
            $this->fragment->setParallelSource($parallelRessourcePath);
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
