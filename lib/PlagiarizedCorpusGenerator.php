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

require_once("Data/OriginalInputDataset.php");
require_once("Data/PlagiarizedInputDataset.php");
require_once("Data/OutputDataset.php");
require_once("Data/Document.php");

require_once("FragmentGenerator.php");

/**
 * @class PlagiarizedCorpusGenerator
 * 
 * Classe permettant de générer un corpus pour la tâche de détection du plagiat.
 */
class PlagiarizedCorpusGenerator {
    /*     * *****************************************************************************************
     *                                      VARIABLES
     * **************************************************************************************** */

    /**
     * Type de fragment.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue
     * @var int $fragmentType
     */
    private $fragmentType = 0;

    /*     * **************************************************************** */

    /**
     * Instance de l'objet courant représentant le corpus de documents originaux.
     * @var OriginalInputDataset    $originalInputDataset
     */
    private $originalInputDataset = null;

    /**
     * Chemin vers le répertoire des documents représentant les sources originales.
     * @var string  $originalDocumentPath
     */
    private $originalDocumentsPath = './upload/original_ressources';

    /**
     * Type de documents originaux en entrée.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Documents par pointeur sur répertoire.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Urls contenues dans un fichier par pointeur sur le fichier (une par ligne).
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Urls contenues dans un flux de texte (une par ligne).
     * @var int  $originalInputDocumentType
     */
    private $originalInputDocumentType = 0;

    /**
     * Instance de l'objet courant représentant le corpus de documents plagiés.
     * @var PlagiarizedInputDataset $plagiarizedInputDataset
     */
    private $plagiarizedInputDataset = null;

    /**
     * Chemin vers le répertoire des documents représentant les documents contenant du plagiat.
     * @var string  $plagiarizedDocumentPath 
     */
    private $plagiarizedDocumentsPath = './upload/plagiarized_ressources';

    /**
     * Chemin vers le répertoire des documents parallèles aux documents de plagiat.
     * @var string  $parallelDocumentPath 
     */
    private $parallelDocumentsPath = './upload/plagiarized_ressources';

    /**
     * Type de documents plagiés en entrée.
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Documents par pointeur sur répertoire.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Urls contenues dans un fichier par pointeur sur le fichier (une par ligne).
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Urls contenues dans un flux de texte (une par ligne).
     * @var int  $plagiarizedInputDocumentType
     */
    private $plagiarizedInputDocumentType = 0;

    /**
     * Zone de texte contenant les Urls (une par ligne).
     * @var string  $textArea
     */
    private $textArea = '';

    /**
     * Nombre maximal d'utilisation d'une source comme étant un document plagié.
     * @var int $maximumUsageOfPlagiarizedRessource 
     */
    private $maximumUsageOfPlagiarizedRessource = 0;

    /**
     * Instance du générateur de fragments.
     * @var FragmentGenerator   $fragmentGenerator
     */
    private $fragmentGenerator = null;

    /**
     * Taille minimale que peut faire un fragment original.
     * @var int Nombre de mots 
     */
    private $minimumWordNumberOfOriginalFragments = 30;

    /**
     * Taille maximale que peut faire un fragment original.
     * @var int Nombre de mots 
     */
    private $maximumWordNumberOfOriginalFragments = 300;

    /*     * **************************************************************** */

    /**
     * Instance de l'objet courant représentant le corpus de documents de sortie.
     * @var OutputDataset   $outputDataset 
     */
    private $outputDataset = null;

    /**
     * Chemin vers le répertoire des documents produits en sortie par l'application.
     * @var string  $outputDocumentPath 
     */
    private $outputDocumentPath = './output';

    /*     * **************************************************************** */

    /**
     * Nombre de documents à créer en sortie.
     * @var int $outputDocumentNumber
     */
    private $outputDocumentNumber = 0;

    /**
     * Longueur de chaque document à créer.
     * @var array   $lengthsOfDocuments 
     */
    private $lengthsOfDocuments = array();

    /**
     * Taille minimale que peut avoir un document (en nombre de mots).
     * @var int $minDocumentlength
     */
    private $minimumDocumentlength = 0;

    /**
     * Taille maximale que peut avoir un document (en nombre de mots).
     * @var int $maxDocumentlength
     */
    private $maximumDocumentlength = 0;

    /**
     * Taille moyenne que doivent avoir les documents créés (en nombre de mots).
     * @var int $averageDocumentlength
     */
    private $averageDocumentlength = 0;

    /*     * **************************************************************** */

    /**
     * Pourcentage de plagiat par document.
     * @var array   $percentagesOfPlagiarismByDocument
     */
    private $percentagesOfPlagiarismByDocument = array();

    /**
     * Pourcentage minimal de plagiat que peut contenir un document.
     * @var float   $minPercentageOfPlagiarismByDocument 
     */
    private $minimumPercentageOfPlagiarismByDocument = 0.0;

    /**
     * Pourcentage maximal de plagiat que peut contenir un document.
     * @var float   $maxPercentageOfPlagiarismByDocument 
     */
    private $maximumPercentageOfPlagiarismByDocument = 0.0;

    /**
     * Pourcentage moyen de plagiat que doivent contenir les documents créés.
     * @var float   $averagePercentageOfPlagiarismByDocument 
     */
    private $averagePercentageOfPlagiarismByDocument = 0.0;


    /*     * **************************************************************** */

    /**
     * Nombre de fragments par document.
     * @var array   $numberOfFragmentsByDocument
     */
    private $numberOfFragmentsByDocument = array();

    /**
     * Nombre minimal de fragments que peut contenir un document.
     * @var int  $minimumFragmentNumber
     */
    private $minimumFragmentNumber = 0;

    /**
     * Nombre maximal de fragments que peut contenir un document.
     * @var int  $maximumFragmentNumber
     */
    private $maximumFragmentNumber = 0;

    /**
     * Nombre moyen de fragments que doit contenir l'ensemble des document créés.
     * @var int  $averageFragmentNumber
     */
    private $averageFragmentNumber = 0;

    /**
     * Détermine si le nombre de fragments doit être utilisé pour les créer, ou si c'est leur taille qui sera utilisée.
     * @var boolean $numberOfFragmentUsage
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;false :</b> La taille des fragments est utilisée pour déterminer leur nombre.
     * <br/><b>&nbsp;&nbsp;&nbsp;true :</b> Le nombre de fragments est utilisé pour déterminer leur taille.
     */
    private $numberOfFragmentsUsage = false;

    /*     * **************************************************************** */

    /**
     * Pourcentage moyen de fragments de petite taille.
     * @var float   $averagePercentageOfShortLengthFragmentNumber 
     */
    private $averagePercentageOfShortPlagiarizedFragmentNumber = 0.0;

    /**
     * Taille minimale que peut faire un fragment de petite taille.
     * @var int $minimumWordNumberForShortPlagiarizedFragments 
     */
    private $minimumWordNumberForShortPlagiarizedFragments = 0;

    /**
     * Taille maximale que peut faire un fragment de petite taille.
     * @var int $maximumWordNumberForShortPlagiarizedFragments 
     */
    private $maximumWordNumberForShortPlagiarizedFragments = 0;

    /**
     * Pourcentage moyen de fragments de taille moyenne.
     * @var float   $averagePercentageOfMediumLengthFragmentNumber 
     */
    private $averagePercentageOfMediumPlagiarizedFragmentNumber = 0.0;

    /**
     * Taille minimale que peut faire un fragment de taille moyenne.
     * @var int $minimumWordNumberForMediumPlagiarizedFragments 
     */
    private $minimumWordNumberForMediumPlagiarizedFragments = 0;

    /**
     * Taille maximale que peut faire un fragment de taille moyenne.
     * @var int $maximumWordNumberForMediumPlagiarizedFragments 
     */
    private $maximumWordNumberForMediumPlagiarizedFragments = 0;

    /**
     * Pourcentage moyen de fragments de grande taille.
     * @var float   $averagePercentageOfLongLengthFragment 
     */
    private $averagePercentageOfLongPlagiarizedFragmentNumber = 0.0;

    /**
     * Taille minimale que peut faire un fragment de grande taille.
     * @var int $minimumWordNumberForLongPlagiarizedFragments 
     */
    private $minimumWordNumberForLongPlagiarizedFragments = 0;

    /**
     * Taille maximale que peut faire un fragment de grande taille.
     * @var int $maximumWordNumberForLongPlagiarizedFragments 
     */
    private $maximumWordNumberForLongPlagiarizedFragments = 0;

    /*     * **************************************************************** */

    /**
     * Pourcentage de plagiat sans obfuscation (copie exacte) que doivent contenir les documents créés.
     * @var float   $noneObfuscationPercentage
     */
    private $noneObfuscationPercentage = 100.0;

    /**
     * Pourcentage de plagiat avec substitution par synonymes que doivent contenir les documents créés.
     * @var float   $noneObfuscationPercentage
     */
    private $substitutionObfuscationPercentage = 0.0;

    /**
     * Pourcentage de plagiat avec inversion d'ordre des mots que doivent contenir les documents créés.
     * @var float   $noneObfuscationPercentage
     */
    private $splitObfuscationPercentage = 0.0;

    /**
     * Pourcentage de plagiat avec du bruit que doivent contenir les documents créés.
     * @var float   $noneObfuscationPercentage
     */
    private $noiseObfuscationPercentage = 0.0;

    /**
     * Pourcentage de plagiat avec perte d'informations que doivent contenir les documents créés.
     * @var float   $noneObfuscationPercentage
     */
    private $delationObfuscationPercentage = 0.0;

    /**
     * Pourcentage de plagiat avec troncage des terminaisons que doivent contenir les documents créés.
     * @var float   $noneObfuscationPercentage
     */
    private $truncationObfuscationPercentage = 0.0;

    /**
     * Type d'obfuscation de chaque document à créer.
     * @var array   $obfuscationTypeByDocument
     */
    private $obfuscationTypeByDocument = array();

    /*     * **************************************************************** */

    /**
     * Pourcentage de faible obfuscation que doivent contenir les documents créés.
     * @var float    $lowObfuscationPercentage
     */
    private $lowObfuscationPercentage = 0.0;

    /**
     * Pourcentage d'obfuscation moyenne que doivent contenir les documents créés.
     * @var float    $mediumObfuscationPercentage
     */
    private $mediumObfuscationPercentage = 0.0;

    /**
     * Pourcentage de forte obfuscation que doivent contenir les documents créés.
     * @var float   $strongObfuscationPercentage
     */
    private $strongObfuscationPercentage = 0.0;

    /**
     * Complexité d'obfuscation de chaque document à créer.
     * @var array   $obfuscationComplexityByDocument
     */
    private $obfuscationComplexityByDocument = array();

    /*     * *****************************************************************************************
     *                                      CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe Core.
     */
    public function __construct() {
        $this->originalInputDataset = new OriginalInputDataset();
        $this->plagiarizedInputDataset = new PlagiarizedInputDataset();
        $this->outputDataset = new OutputDataset();
        $this->fragmentGenerator = new FragmentGenerator();
    }

    /*     * *****************************************************************************************
     *                                        GETTERS
     * **************************************************************************************** */

    /**
     * Retourne le nombre de documents générés dans le corpus de sortie.
     * @return  int Nombre de documents
     */
    public function getDocumentNumberOfOutputDataset() {
        return $this->outputDataset->getDocumentNumber();
    }

    /**
     * Retourne la taille moyenne (en nombre de mots) des documents générés dans le corpus de sortie.
     * @return  float   Nombre de mots moyen
     */
    public function getAverageSizeOfDocuments() {
        return $this->outputDataset->getAverageWordNumber();
    }

    /**
     * Retourne le nombre moyen de fragments contenus dans les documents générés dans le corpus de sortie.
     * @return  float   Nombre de fragments moyen
     */
    public function getAverageFragmentNumberOfDocuments() {
        return $this->outputDataset->getAverageFragmentNumber();
    }

    /**
     * Retourne le pourcentage de plagiat moyen des documents générés dans le corpus de sortie.
     * @return  float   Pourcentage de plagiat moyen
     */
    public function getAveragePlagiarizedPercentageOfDocuments() {
        return $this->outputDataset->getAveragePlagiarizedPercentage();
    }

    /*     * *****************************************************************************************
     *                                        SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un type de fragment à extraire.
     * @param   int $fragmentType   Type
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue
     */
    public function setFragmentType($fragmentType) {
        $this->fragmentType = $fragmentType;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le chemin vers le répertoire des documents représentant les sources originales.
     * @param   string  $originalDocumentPath   Chemin
     */
    public function setOriginalDocumentsPath($originalDocumentPath) {
        $this->originalDocumentsPath = $originalDocumentPath;
    }

    /**
     * Affecte le type de documents originaux en entrée.
     * @param   int $originalInputDocumentType  Type
     */
    public function setOriginalInputDocumentType($originalInputDocumentType) {
        $this->originalInputDocumentType = $originalInputDocumentType;
    }

    /**
     * Affecte le chemin vers le répertoire des documents représentant les documents contenant du plagiat.
     * @param   string  $plagiarizedDocumentPath    Chemin
     */
    public function setPlagiarizedDocumentsPath($plagiarizedDocumentPath) {
        $this->plagiarizedDocumentsPath = $plagiarizedDocumentPath;
    }

    /**
     * Affecte le chemin vers le répertoire des documents parallèles aux documents de plagiat.
     * @param   string  $parallelDocumentPath    Chemin
     */
    public function setParallelDocumentsPath($parallelDocumentPath) {
        $this->parallelDocumentsPath = $parallelDocumentPath;
    }

    /**
     * Affecte le type de documents plagiés en entrée.
     * @param   int $plagiarizedInputDocumentType   Type
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Documents par pointeur sur répertoire.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Urls contenues dans un fichier par pointeur sur le fichier (une par ligne).
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Urls contenues dans un flux de texte (une par ligne).
     */
    public function setPlagiarizedInputDocumentType($plagiarizedInputDocumentType) {
        $this->plagiarizedInputDocumentType = $plagiarizedInputDocumentType;
    }

    /**
     * Affecte un texte dans la zone de texte.
     * @param   string  $textArea   Texte
     */
    public function setTextArea($textArea) {
        $this->textArea = $textArea;
    }

    /**
     * Affecte le nombre maximal d'utilisation d'une source comme étant un document plagié.
     * @param   int $limit    Nombre d'utilisation
     */
    public function setMaximumUsageOfPlagiarizedRessource($limit) {
        $this->maximumUsageOfPlagiarizedRessource = $limit;
    }

    /**
     * Affecte la taille minimale que peut faire un fragment original.
     * @param   int $length
     */
    public function setMinimumWordNumberOfOriginalFragments($length) {
        $this->minimumWordNumberOfOriginalFragments = $length;
    }

    /**
     * Affecte la taille maximale que peut faire un fragment original.
     * @param   int $length
     */
    public function setMaximumWordNumberOfOriginalFragments($length) {
        $this->maximumWordNumberOfOriginalFragments = $length;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le chemin vers le répertoire des documents produits en sortie par l'application.
     * @param   string  $outputDocumentPath Chemin
     */
    public function setOutputDocumentPath($outputDocumentPath) {
        $this->outputDocumentPath = $outputDocumentPath;
    }

    /**
     * Affecte le nombre de documents à produire en sortie de l'application.
     * @param   int $outputDocumentNumber   Nombre de documents
     */
    public function setOutputDocumentNumber($outputDocumentNumber) {
        $this->outputDocumentNumber = $outputDocumentNumber;
    }

    /*     * **************************************************************** */

    /**
     * Affecte la longueur minimale (en nombre de mots) que peut faire un document.
     * @param   int $minDocumentlength  Nombre de mots
     */
    public function setMinimumDocumentlength($minDocumentlength) {
        $this->minimumDocumentlength = $minDocumentlength;
    }

    /**
     * Affecte la longueur maximale (en nombre de mots) que peut faire un document.
     * @param   int $maxDocumentlength  Nombre de mots
     */
    public function setMaximumDocumentlength($maxDocumentlength) {
        $this->maximumDocumentlength = $maxDocumentlength;
    }

    /**
     * Affecte la longueur moyenne (en nombre de mots) que doivent faire les documents produits.
     * @param   int $averageDocumentlength  Nombre de mots
     */
    public function setAverageDocumentlength($averageDocumentlength) {
        $this->averageDocumentlength = $averageDocumentlength;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le nombre minimal de fragments que peut contenir un document.
     * @param   int $number
     */
    public function setMinimumFragmentNumber($number) {
        $this->minimumFragmentNumber = $number;
    }

    /**
     * Affecte le nombre maximal de fragments que peut contenir un document.
     * @param   int $number
     */
    public function setMaximumFragmentNumber($number) {
        $this->maximumFragmentNumber = $number;
    }

    /**
     * Affecte le nombre moyen de fragments que doit contenir l'ensemble des document créés.
     * @param   int $number
     */
    public function setAverageFragmentNumber($number) {
        $this->averageFragmentNumber = $number;
    }

    /**
     * Indique si le nombre de fragments doit être utilisé pour les créer, ou si c'est leur taille qui sera utilisée.
     * @param   boolean $active  Indicateur
     *  <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;false :</b> La taille des fragments est utilisée pour déterminer leur nombre.
     * <br/><b>&nbsp;&nbsp;&nbsp;true :</b> Le nombre de fragments est utilisé pour déterminer leur taille.
     */
    public function setNumberOfFragmentsUsage($active) {
        $this->numberOfFragmentsUsage = $active;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le pourcentage minimum de plagiat que peut contenir un document.
     * @param   float   $percentage Pourcentage
     */
    public function setMinimumPercentageOfPlagiarismByDocument($percentage) {
        $this->minimumPercentageOfPlagiarismByDocument = $percentage;
    }

    /**
     * Affecte le pourcentage maximum de plagiat que peut contenir un document.
     * @param   float   $percentage Pourcentage
     */
    public function setMaximumPercentageOfPlagiarismByDocument($percentage) {
        $this->maximumPercentageOfPlagiarismByDocument = $percentage;
    }

    /**
     * Affecte le pourcentage moyen de plagiat que doivent contenir les documents.
     * @param   float   $percentage Pourcentage
     */
    public function setAveragePercentageOfPlagiarismByDocument($percentage) {
        $this->averagePercentageOfPlagiarismByDocument = $percentage;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le pourcentage de fragments de petite taille que doit contenir un document.
     * @param   float   $percentage   Pourcentage
     */
    public function setAveragePercentageOfShortPlagiarizedFragmentNumber($percentage) {
        $this->averagePercentageOfShortPlagiarizedFragmentNumber = $percentage;
    }

    /**
     * Affecte la longueur minimale (en nombre de mots) que peut faire un fragment de petite taille.
     * @param   int $length  Nombre de mots
     */
    public function setMinimumWordNumberForShortPlagiarizedFragments($length) {
        $this->minimumWordNumberForShortPlagiarizedFragments = $length;
    }

    /**
     * Affecte la longueur maximale (en nombre de mots) que peut faire un fragment de petite taille.
     * @param   int $length  Nombre de mots
     */
    public function setMaximumWordNumberForShortPlagiarizedFragments($length) {
        $this->maximumWordNumberForShortPlagiarizedFragments = $length;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le pourcentage de fragments de taille moyenne que doit contenir un document.
     * @param   float   $percentage  Pourcentage
     */
    public function setAveragePercentageOfMediumPlagiarizedFragmentNumber($percentage) {
        $this->averagePercentageOfMediumPlagiarizedFragmentNumber = $percentage;
    }

    /**
     * Affecte la longueur minimale (en nombre de mots) que peut faire un fragment de taille moyenne.
     * @param   int $length Nombre de mots
     */
    public function setMinimumWordNumberForMediumPlagiarizedFragments($length) {
        $this->minimumWordNumberForMediumPlagiarizedFragments = $length;
    }

    /**
     * Affecte la longueur maximale (en nombre de mots) que peut faire un fragment de taille moyenne.
     * @param   int $length Nombre de mots
     */
    public function setMaximumWordNumberForMediumPlagiarizedFragments($length) {
        $this->maximumWordNumberForMediumPlagiarizedFragments = $length;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le pourcentage de fragments de grande taille que doit contenir un document.
     * @param   float   $percentage  Pourcentage
     */
    public function setAveragePercentageOfLongPlagiarizedFragmentNumber($percentage) {
        $this->averagePercentageOfLongPlagiarizedFragmentNumber = $percentage;
    }

    /**
     * Affecte la longueur minimale (en nombre de mots) que peut faire un fragment de grande taille.
     * @param   int $length   Nombre de mots
     */
    public function setMinimumWordNumberForLongPlagiarizedFragments($length) {
        $this->minimumWordNumberForLongPlagiarizedFragments = $length;
    }

    /**
     * Affecte la longueur maximale (en nombre de mots) que peut faire un fragment de grande taille.
     * @param   int $length   Nombre de mots
     */
    public function setMaximumWordNumberForLongPlagiarizedFragments($length) {
        $this->maximumWordNumberForLongPlagiarizedFragments = $length;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le pourcentage de plagiat sans obfuscation (copie exacte) que doivent contenir les documents créés.
     * @param   float   $percentage Pourcentage
     */
    public function setNoneObfuscationPercentage($percentage) {
        $this->noneObfuscationPercentage = $percentage;
    }

    /**
     * Affecte le pourcentage de plagiat avec substitution par synonymes que doivent contenir les documents créés.
     * @param   float   $percentage Pourcentage
     */
    public function setSubstitutionObfuscationPercentage($percentage) {
        $this->substitutionObfuscationPercentage = $percentage;
    }

    /**
     * Affecte le pourcentage de plagiat avec inversion d'ordre des mots que doivent contenir les documents créés.
     * @param   float   $percentage Pourcentage
     */
    public function setSplitObfuscationPercentage($percentage) {
        $this->splitObfuscationPercentage = $percentage;
    }

    /**
     * Affecte le pourcentage de plagiat avec du bruit que doivent contenir les documents créés.
     * @param   float   $percentage Pourcentage
     */
    public function setNoiseObfuscationPercentage($percentage) {
        $this->noiseObfuscationPercentage = $percentage;
    }

    /**
     * Affecte le pourcentage de plagiat avec perte d'informations que doivent contenir les documents créés.
     * @param   float   $percentage Pourcentage
     */
    public function setDelationObfuscationPercentage($percentage) {
        $this->delationObfuscationPercentage = $percentage;
    }

    /**
     * Affecte le pourcentage de plagiat avec troncage des terminaisons que doivent contenir les documents créés.
     * @param   float   $percentage Pourcentage
     */
    public function setTruncationObfuscationPercentage($percentage) {
        $this->truncationObfuscationPercentage = $percentage;
    }

    /*     * **************************************************************** */

    /**
     * Affecte le pourcentage de faible obfuscation que doivent contenir les documents créés.
     * @param   float   $percentage
     */
    function setLowObfuscationPercentage($percentage) {
        $this->lowObfuscationPercentage = $percentage;
    }

    /**
     * Affecte le pourcentage d'obfuscation moyenne que doivent contenir les documents créés.
     * @param   float   $percentage
     */
    function setMediumObfuscationPercentage($percentage) {
        $this->mediumObfuscationPercentage = $percentage;
    }

    /**
     * Affecte le pourcentage de forte obfuscation que doivent contenir les documents créés.
     * @param   float   $percentage
     */
    function setStrongObfuscationPercentage($percentage) {
        $this->strongObfuscationPercentage = $percentage;
    }

    /*     * *****************************************************************************************
     *                                        METHODS
     * **************************************************************************************** */

    /**
     * Initialise les corpus d'entrées.
     */
    public function initializeDataset() {
        // Chargement des ressources originales en entrée.
        $this->originalInputDataset->setInputDocumentType($this->originalInputDocumentType);
        $this->originalInputDataset->setPath($this->originalDocumentsPath);
        $this->originalInputDataset->loadDataset();
        // $this->originalInputDataset->display();
        // Chargement des ressources plagiées en entrée.
        $this->plagiarizedInputDataset->setInputDocumentType($this->plagiarizedInputDocumentType);
        $this->plagiarizedInputDataset->setPath($this->plagiarizedDocumentsPath);
        $this->plagiarizedInputDataset->setTextArea($this->textArea);
        $this->plagiarizedInputDataset->loadDataset();
        // $this->plagiarizedInputDataset->display();
        // Initialisation du répertoire de sortie.
        $this->outputDataset->setPath($this->outputDocumentPath);
        $this->outputDataset->clear();
        // $this->outputDataset->display();
    }

    /**
     * Vérifie la cohérence des paramètres.
     * @return  int Code d'erreur.
     * <br/>Interprétation :
     * <br/><b>&nbsp;&nbsp;&nbsp;-1 :</b> Certains nombres sont inférieurs à 0.
     * <br/><b>&nbsp;&nbsp;&nbsp;-2 :</b> Certains pourcentages ne sont pas compris entre 0 et 100.
     * <br/><b>&nbsp;&nbsp;&nbsp;-3 :</b> La somme des pourcentages de répartition des tailles des passages plagiés n'est pas égale à 100%.
     * <br/><b>&nbsp;&nbsp;&nbsp;-4 :</b> La valeur minimale d'un critère est supérieur à la valeur maximale de ce même critère.
     * <br/><b>&nbsp;&nbsp;&nbsp;-5 :</b> Une moyenne est supérieure à sa valeur maximale ou inférieure à sa valeur minimale.
     * <br/><b>&nbsp;&nbsp;&nbsp;-6 :</b> La somme des pourcentages de répartition de l'obfuscation des passages plagiés n'est pas égale à 100%.
     * <br/><b>&nbsp;&nbsp;&nbsp;-7 :</b> La somme des pourcentages de répartition de la compléxité d'obfuscation des passages plagiés n'est pas égale à 100%.
     */
    public function checkParameters() {
        $error = 1;
        if ($this->outputDocumentNumber < 0 || $this->maximumDocumentlength < 0 || $this->minimumDocumentlength < 0 || $this->averageDocumentlength < 0 || $this->maximumUsageOfPlagiarizedRessource < 0) {
            $error = -1; // Les nombres sont inférieurs à 0.
        } else if (!$this->isPercentageFormat($this->averagePercentageOfPlagiarismByDocument) || !$this->isPercentageFormat($this->maximumPercentageOfPlagiarismByDocument) || !$this->isPercentageFormat($this->minimumPercentageOfPlagiarismByDocument) || !$this->isPercentageFormat($this->averagePercentageOfLongPlagiarizedFragmentNumber) || !$this->isPercentageFormat($this->averagePercentageOfMediumPlagiarizedFragmentNumber) || !$this->isPercentageFormat($this->averagePercentageOfShortPlagiarizedFragmentNumber)) {
            $error = -2; // Les pourcentages ne sont pas compris entre 0 et 100.
        } else if (($this->averagePercentageOfLongPlagiarizedFragmentNumber + $this->averagePercentageOfMediumPlagiarizedFragmentNumber + $this->averagePercentageOfShortPlagiarizedFragmentNumber) != 100) {
            $error = -3; // La somme des pourcentages de répartition des tailles des passages plagiés n'est pas égale à 100%.
        } else if (($this->maximumPercentageOfPlagiarismByDocument < $this->minimumPercentageOfPlagiarismByDocument) || ($this->maximumDocumentlength < $this->minimumDocumentlength)) {
            $error = -4; // La valeur minimale d'un critère est supérieur à la valeur maximale de ce même critère.
        } else if (($this->averagePercentageOfPlagiarismByDocument > $this->maximumPercentageOfPlagiarismByDocument) || ($this->averagePercentageOfPlagiarismByDocument < $this->minimumPercentageOfPlagiarismByDocument) || ($this->averageDocumentlength > $this->maximumDocumentlength) || ($this->averageDocumentlength < $this->minimumDocumentlength)) {
            $error = -5; // Une moyenne est supérieure à sa valeur maximale ou inférieure à sa valeur minimale.
        } else if (($this->noneObfuscationPercentage + $this->substitutionObfuscationPercentage + $this->splitObfuscationPercentage + $this->noiseObfuscationPercentage + $this->delationObfuscationPercentage + $this->truncationObfuscationPercentage) != 100) {
            $error = -6; // La somme des pourcentages de répartition de l'obfuscation des passages plagiés n'est pas égale à 100%.
        } else if (($this->noneObfuscationPercentage != 100) && (($this->lowObfuscationPercentage + $this->mediumObfuscationPercentage + $this->strongObfuscationPercentage) != 100)) {
            $error = -7; // La somme des pourcentages de répartition de la compléxité d'obfuscation des passages plagiés n'est pas égale à 100%.
        }
        return $error;
    }

    /**
     * Initialise les paramètres d'entrées.
     * @throws Exception
     */
    public function initializeParameters() {
        try {
            $result = $this->checkParameters();
            if ($result > 0) {
                // Détermine les tailles des documents à créer en fonction des paramètres entrés.
                $this->lengthsOfDocuments = $this->meanRandom($this->averageDocumentlength, $this->outputDocumentNumber, $this->minimumDocumentlength, $this->maximumDocumentlength);

                if ($this->numberOfFragmentsUsage) {
                    // Détermine le nombre de fragments contenus dans les documents à créer en fonction des paramètres entrés.
                    $this->numberOfFragmentsByDocument = $this->meanRandom($this->averageFragmentNumber, $this->outputDocumentNumber, $this->minimumFragmentNumber, $this->maximumFragmentNumber);
                }

                // Détermine les pourcentages de plagiat des documents à créer en fonction des paramètres entrés.
                $this->percentagesOfPlagiarismByDocument = $this->meanRandom($this->averagePercentageOfPlagiarismByDocument, $this->outputDocumentNumber, $this->minimumPercentageOfPlagiarismByDocument, $this->maximumPercentageOfPlagiarismByDocument);
                // Détermine les obfuscations des passages plagiés à créer en fonction des paramètres entrés.
                $probabilityDistributionOfObfuscationType = array($this->noneObfuscationPercentage, $this->substitutionObfuscationPercentage, $this->splitObfuscationPercentage, $this->noiseObfuscationPercentage, $this->delationObfuscationPercentage, $this->truncationObfuscationPercentage);
                $this->obfuscationTypeByDocument = $this->applyProbabilityDistribution($probabilityDistributionOfObfuscationType, array(-1, 0, 1, 2, 3, 4), $this->outputDocumentNumber);
                // Détermine les compléxités d'obfuscation des passages plagiés à créer en fonction des paramètres entrés.
                $probabilityDistributionOfObfuscationComplexity = array($this->lowObfuscationPercentage, $this->mediumObfuscationPercentage, $this->strongObfuscationPercentage);
                $this->obfuscationComplexityByDocument = $this->applyProbabilityDistribution($probabilityDistributionOfObfuscationComplexity, array(0.20, 0.50, 0.70), $this->outputDocumentNumber);
            } else {
                throw new Exception('Wrong parameters! Error code: ' . $result);
            }
        } catch (Exception $e) {
            die($e);
        }
    }

    /**
     * Génère des fragments de plagiat.
     * @param   int     $fragmentType           Type de fragment à créer
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue.            
     * @param   int     $min                    Taille minimale que doit faire un fragment
     * @param   int     $max                    Taille maximale que doit faire un fragment
     * @param   int     $total                  Taille totale que doivent faire les fragments de ce type
     * @param   int     $obfuscationType        Type d'obfuscation. <br/> <i>Aucune est appliquée par défaut.</i>
     * <br/>Utilisation :
     * <br/><b>&nbsp;-1 :</b> Aucune obfuscation.
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre des mots au sein d'un certain pourcentage de phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @param   float   $obfuscationComplexity  Complexité d'obfuscation <br/>Comprise entre 0 et 1 ou 0 et 100.
     * @return  array   Fragments
     */
    private function generatePlagiarizedFragments($fragmentType, $min, $max, $total, $obfuscationType = -1, $obfuscationComplexity = 0.0) {
        $fragments = array();
        $fragmentsSize = 0;
        // Calcule les tailles des passages.
        $sizes = $this->factorialRandom($min, $max, $total);
        // Pour chacune des tailles, créer les objets Fragments.
        foreach ($sizes as $wordNumber) {
            // Génère le fragment plagié.
            $fragment = $this->generatePlagiarizedFragment($fragmentType, $wordNumber, $obfuscationType, $obfuscationComplexity);
            // Ajoute le fragment au tableau de fragments.
            array_push($fragments, $fragment);
            $fragmentsSize += $fragment->getThisWordNumber();
            // Si la taille des fragments en cours de création dépasse la taille limite des fragments à créer, on sort.
            if ($fragmentsSize >= $total) {
                break;
            }
        }
        return $fragments;
    }

    /**
     * Génère des fragments originaux.          
     * @param   int     $min                    Taille minimale que doit faire un fragment
     * @param   int     $max                    Taille maximale que doit faire un fragment
     * @param   int     $total                  Taille totale que doivent faire les fragments de ce type
     * @return  array   Fragments
     */
    private function generateOriginalFragments($min, $max, $total) {
        $fragments = array();
        $fragmentsSize = 0;
        // Calcule les tailles des passages.
        $sizes = $this->factorialRandom($min, $max, $total);
        // Pour chacune des tailles, créer les objets Fragments.
        foreach ($sizes as $wordNumber) {
            // Génère le fragment original.
            $fragment = $this->generateOriginalFragment($wordNumber);
            // Ajoute le fragment au tableau de fragments.
            array_push($fragments, $fragment);
            $fragmentsSize += $fragment->getThisWordNumber();
            // Si la taille des fragments en cours de création dépasse la taille limite des fragments à créer, on sort.
            if ($fragmentsSize >= $total) {
                break;
            }
        }
        return $fragments;
    }

    /**
     * Génère un fragment plagié.
     * @param   int     $fragmentType           Type de fragment à créer
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Fragment monolingue.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Fragment cross-lingue.            
     * @param   int     $size                   Taille du fragment (en nombre de mots)
     * @param   int     $obfuscationType        Type d'obfuscation. <br/> <i>Aucune est appliquée par défaut.</i>
     * <br/>Utilisation :
     * <br/><b>&nbsp;-1 :</b> Aucune obfuscation.
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre des mots au sein d'un certain pourcentage de phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @param   float   $obfuscationComplexity  Complexité d'obfuscation <br/>Comprise entre 0 et 1 ou 0 et 100.
     * @return  Fragment    Fragment
     */
    private function generatePlagiarizedFragment($fragmentType, $size, $obfuscationType = -1, $obfuscationComplexity = 0.0) {
        $contentOfFragment = '';
        $i = 0;

        // Tant que le fragment créé comporte du texte vide (du à une erreur d'extraction de la source par exemple).
        while (($contentOfFragment == '') && ($i < 10)) {
            $i++;
            // Retourne un fichier ou une url peu utilisé jusqu'à présent.
            $ressourcePath = $this->plagiarizedInputDataset->getCertainlyRarelyUsedFile($this->maximumUsageOfPlagiarizedRessource);
            // Génère le fragment en fonction des paramètres.
            $this->fragmentGenerator->setFragmentType($fragmentType);
            $this->fragmentGenerator->setRessourcePath($ressourcePath);
            $this->fragmentGenerator->setParallelRessourcesPath($this->parallelDocumentsPath);
            $this->fragmentGenerator->setRessourceType(1);
            $this->fragmentGenerator->setWordNumber($size);
            $this->fragmentGenerator->setObfuscationType($obfuscationType);
            $this->fragmentGenerator->setObfuscationComplexity($obfuscationComplexity);
            // Génère le passage.
            $this->fragmentGenerator->run();
            // Récupère le fragment créé.
            $fragment = $this->fragmentGenerator->getCreatedFragment();
            // Récupère son contenu pour savoir si on ré-itère la boucle ou non.
            $contentOfFragment = $fragment->getText();
        }
        return $fragment;
    }

    /**
     * Génère un fragment original.
     * @param   int $size   Taille du fragment (en nombre de mots)
     * @return  Fragment    Fragment
     */
    private function generateOriginalFragment($size) {
        $contentOfFragment = '';
        $i = 0;

        // Tant que le fragment créé comporte du texte vide (du à une erreur d'extraction de la source par exemple).
        while (($contentOfFragment == '') && ($i < 10)) {
            $i++;
            // Retourne un fichier ou une url peu utilisé jusqu'à présent.
            $ressourcePath = $this->originalInputDataset->getRandomFile();
            // Génère le fragment en fonction des paramètres.
            $this->fragmentGenerator->setFragmentType(0);
            $this->fragmentGenerator->setRessourcePath($ressourcePath);
            $this->fragmentGenerator->setRessourceType(0);
            $this->fragmentGenerator->setWordNumber($size);
            $this->fragmentGenerator->setObfuscationType(-1);
            $this->fragmentGenerator->setObfuscationComplexity(0.0);
            // Génère le passage.
            $this->fragmentGenerator->run();
            // Récupère le fragment créé.
            $fragment = $this->fragmentGenerator->getCreatedFragment();
            // Récupère son contenu pour savoir si on ré-itère la boucle ou non.
            $contentOfFragment = $fragment->getText();
        }
        return $fragment;
    }

    /**
     * Génère le corpus de sortie dans un contexte monolingue. <br/>
     * Génère les documents de sortie et les stoque dans un objet OutputDataset.
     */
    public function run() {
        // Pour chaque document.
        for ($i = 0; $i < $this->outputDocumentNumber; $i++) {

            // Récupère la longueur du document en cours de création.
            $desiredWordNumberOfDocument = $this->lengthsOfDocuments[$i];
            // Récupère le pourcentage de plagiat du document en cours de création.
            $plagiarizedPercentageOfDocument = $this->percentagesOfPlagiarismByDocument[$i];
            // Récupère le type d'obfuscation du plagiat du document en cours de création.
            $obfuscationType = $this->obfuscationTypeByDocument[$i];
            // Récupère la compléxité d'obfuscation du plagiat du document en cours de création.
            $obfuscationComplexity = $this->obfuscationComplexityByDocument[$i];
            // Calcule le nombre de mots plagiés du document en cours de création.
            $plagiarizedWordNumberOfDocument = $this->crossMultiplication($plagiarizedPercentageOfDocument, 100, $desiredWordNumberOfDocument);

            // Création des fragment en utilisant leur nombre.
            if ($this->numberOfFragmentsUsage) {
                // Récupère le nombre de fragments du documents en cours de création.
                $fragmentNumberOfDocument = $this->numberOfFragmentsByDocument[$i];
                // Calcule la taille des fragments du document.
                $sizesOfFragmentsOfDocument = $this->sumRandom($desiredWordNumberOfDocument, $fragmentNumberOfDocument, 12);

                // Distribue les fragments entre plagiés ou originaux afin de bien répartir les tailles.
                $length = 0;
                $fragments = array();
                foreach ($sizesOfFragmentsOfDocument as $size) {
                    $length += $size;
                    if ($length >= $plagiarizedWordNumberOfDocument) {
                        // Ici, on créait un fragment original.
                        array_push($fragments, $this->generateOriginalFragment($size));
                    } else {
                        // Ici, on créait un fragment plagié.
                        array_push($fragments, $this->generatePlagiarizedFragment($this->fragmentType, $size, $obfuscationType, $obfuscationComplexity));
                    }
                }
            } else { // Création des fragments en utilisant leur taille.
                // Calcule le nombre de mots originaux du document en cours de création.
                $originalWordNumberOfDocument = $desiredWordNumberOfDocument - $plagiarizedWordNumberOfDocument;
                // Calcule le nombre de mots plagiés par tailles de fragments (plagiés).
                $wordNumberOfLongLengthFragmentOfDocument = $this->crossMultiplication($this->averagePercentageOfLongPlagiarizedFragmentNumber, 100, $plagiarizedWordNumberOfDocument);
                $wordNumberOfMediumLengthFragmentOfDocument = $this->crossMultiplication($this->averagePercentageOfMediumPlagiarizedFragmentNumber, 100, $plagiarizedWordNumberOfDocument);
                $wordNumberOfShortLengthFragmentOfDocument = $this->crossMultiplication($this->averagePercentageOfShortPlagiarizedFragmentNumber, 100, $plagiarizedWordNumberOfDocument);

                // Créer différents types de fragments en leur affectant leur taille.
                $longPlagiarizedFragments = $this->generatePlagiarizedFragments($this->fragmentType, $this->minimumWordNumberForLongPlagiarizedFragments, $this->maximumWordNumberForLongPlagiarizedFragments, $wordNumberOfLongLengthFragmentOfDocument, $obfuscationType, $obfuscationComplexity);
                $mediumPlagiarizedFragments = $this->generatePlagiarizedFragments($this->fragmentType, $this->minimumWordNumberForMediumPlagiarizedFragments, $this->maximumWordNumberForMediumPlagiarizedFragments, $wordNumberOfMediumLengthFragmentOfDocument, $obfuscationType, $obfuscationComplexity);
                $shortPlagiarizedFragments = $this->generatePlagiarizedFragments($this->fragmentType, $this->minimumWordNumberForShortPlagiarizedFragments, $this->maximumWordNumberForShortPlagiarizedFragments, $wordNumberOfShortLengthFragmentOfDocument, $obfuscationType, $obfuscationComplexity);
                $originalFragments = $this->generateOriginalFragments($this->minimumWordNumberOfOriginalFragments, $this->maximumWordNumberOfOriginalFragments, $originalWordNumberOfDocument);

                // Fusionne les différents types de fragments.
                $fragments = array_merge($longPlagiarizedFragments, $mediumPlagiarizedFragments, $shortPlagiarizedFragments, $originalFragments);
            }

            // Créer un objet Document en lui affectant le tableau résultat.
            $document = new Document($i, $fragments);
            // Mélanger les passages.
            $document->shuffleFragments();
            // On ajoute le document courant créé au jeu de documents de sortie.
            $this->outputDataset->addDocument($document);
        }
    }

    /**
     * Calcule les méta-informations de chaque document du corpus de sortie nécessaire à son écriture. <br/>
     * Calcule le nombre de mots et le pourcentage de plagiat contenu dans chaque document généré.
     */
    public function calculateData() {
        $this->outputDataset->calculateData();
    }

    /**
     * Ecrit/Créé les documents du corpus de sortie.
     * @param   int $type   Type de sortie
     * <br/>Utilisation :
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Documents au format PLAIN TEXT.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Documents au format employé lors de la PAN (seulement les metadata au format XML).
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> (1 + 2) Documents avec le texte des fragments encadré avec leurs metadata au format XML.
     */
    public function writeOutputDataset($type) {
        switch ($type) {
            case 1:
                $this->outputDataset->generatePlainTextDocuments();
                break;
            case 2:
                $this->outputDataset->generateXmlMetaData();
                break;
            case 3:
                $this->outputDataset->generatePlainTextDocumentsWithXmlMetaData();
                break;
            default:
                $this->outputDataset->generatePlainTextDocuments();
                break;
        }
    }

    /**
     * Retourne si un nombre est compris entre 0 et 100 (le format d'un pourcentage).
     * @param   float   $percentage Pourcentage
     * @return  boolean <b>TRUE</b> si compris entre 0 et 100, <b>FALSE</b> sinon
     */
    private function isPercentageFormat($percentage) {
        $bool = true;
        if (($percentage < 0) || ($percentage > 100)) {
            $bool = false;
        }
        return $bool;
    }

    /**
     * Tire au sort une séquence de nombres aléatoires compris entre <b>min</b> et <b>max</b> jusqu'à ce que leur somme soit égale à <b>sum</b>.
     * @param   int     $min    Minimum
     * @param   int     $max    Maximum
     * @param   int     $sum    Somme des valeurs <br/> Si rien est précisé, <b>sum</b> est égal à <b>max</b> par défaut
     * @return  array   Nombres tirés au sort
     */
    private function factorialRandom($min, $max, $sum = -1) {
        $result = array();

        if ($sum == -1) {
            $sum = $max;
        }

        while (true) {
            $random = mt_rand($min, $max);
            if (($sum >= $max) && (($sum - $random) <= $min)) {
                
            } else if ($sum < $max) {
                array_push($result, $sum);
                break;
            } else {
                array_push($result, $random);
                $sum -= $random;
            }
        }
        return $result;
    }

    /**
     * Tire au sort <b>n</b> nombres aléatoires compris entre <b>min</b> et <b>sum</b> jusqu'à ce que leur somme soit égale à <b>sum</b>.
     * @param   int     $sum    Somme des valeurs <br/> Si rien est précisé, <b>sum</b> est égal à <b>max</b> par défaut
     * @param   int     $n      Nombre de nombres à tirer au sort
     * @param   int     $min    Minimum
     * @return  array   Nombres tirés au sort
     */
    private function sumRandom($sum, $n, $min) {
        $result = array();
        $i = 0;

        while (array_sum($result) != $sum) {
            $result[$i] = mt_rand($min, $sum / mt_rand(1, 5));

            if (++$i == $n) {
                $i = 0;
            }
        }

        shuffle($result);
        return $result;
    }

    /**
     * Tire au sort <b>n</b> nombres aléatoires compris entre <b>min</b> et <b>max</b> et ayant pour moyenne <b>mean</b>.
     * @param   int     $mean   Moyenne que devra donner l'ensemble des nombres tirés au sort
     * @param   int     $n      Nombre de nombres à tirer au sort
     * @param   int     $min    Minimum
     * @param   int     $max    Maximum
     * @return  array   Nombres tirés au sort
     */
    private function meanRandom($mean, $n, $min, $max) {
        $result = array();
        $totalMean = intval($mean * $n);

        while ($n > 1) {
            $allowedMax = $totalMean - $n - $min;
            $allowedMin = intval($totalMean / $n);

            $random = mt_rand(max($min, $allowedMin), min($max, $allowedMax));
            array_push($result, $random);
            $totalMean -= $random;
            $n--;
        }
        array_push($result, $totalMean);

        return $result;
    }

    /**
     * Retourne le résultat d'un produit en croix (règle de trois).<br/>
     * @param   float   $reference          Nombre de référence (a)
     * @param   float   $maximumReference   Base de référence (b)
     * @param   float   $maximumReal        Base réelle (c)
     * @return  float   Inconnue calculée (x = a * c / b)
     */
    private function crossMultiplication($reference, $maximumReference, $maximumReal) {
        return round($reference * $maximumReal / $maximumReference);
    }

    /**
     * Distribue selon une loi de probabilité des classes sur un certain nombre d'éléments.
     * @param   array   $probabilityDistribution    Loi de probabilité
     * @param   array   $classes                    Classes des éléments à distribuer
     * @param   int     $numberOfItems              Nombre d'éléments à distribuer
     * @return  array   Distribution
     */
    private function applyProbabilityDistribution($probabilityDistribution, $classes, $numberOfItems) {
        $distribution = array();
        $classNumber = 0;

        foreach ($probabilityDistribution as $probability) {
            $number = $this->crossMultiplication($probability, 100, $numberOfItems);
            for ($i = 0; $i < $number; $i++) {
                array_push($distribution, $classes[$classNumber]);
            }
            $classNumber++;
        }
        $diff = $numberOfItems - count($distribution);
        if ($diff > 0) {
            for ($i = 0; $i < $diff; $i++) {
                array_push($distribution, $classes[array_rand($classes)]);
            }
        }

        shuffle($distribution);
        return $distribution;
    }

}
