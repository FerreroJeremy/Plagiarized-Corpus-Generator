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
 * @class Document
 * 
 * Classe représentant un document.
 */
class Document {
    /*     * *****************************************************************************************
     *                                      VARIABLES
     * **************************************************************************************** */

    /**
     * Identifiant du document.
     * @var int $id 
     */
    private $id = -1;

    /**
     * Liste de fragments constituant le document.
     * @var array   $fragments 
     */
    private $fragments = array();

    /**
     * Nombre total de caractères du document.
     * @var int $length
     */
    private $length = 0;

    /**
     * Nombre total de mots du document.
     * @var int $wordNumber
     */
    private $wordNumber = 0;

    /**
     * Pourcentage total de plagiat présent dans le document.
     * @var float   $plagiarizedPercentage
     */
    private $plagiarizedPercentage = 0.0;

    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur de la classe Document.
     * @param   int     $id         Identifiant du document
     * @param   array   $fragments  Fragments constituant le document
     */
    public function __construct($id, $fragments) {
        $this->id = $id;
        $this->fragments = $fragments;
    }

    /*     * *****************************************************************************************
     *                                      GETTERS
     * **************************************************************************************** */

    /**
     * Retourne l'identifiant du document.
     * @return  int Identifiant
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Retourne la taille du document en nombre de mots.
     * @return  int Nombre de mots
     */
    public function getWordNumber() {
        return $this->wordNumber;
    }

    /**
     * Retourne la taille du document en nombre de caractères.
     * @return  int Nombre de caractères
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * Retourne le nombre de fragments constituant le document.
     * @return  int Nombre de fragments
     */
    public function getFragmentNumber() {
        return count($this->fragments);
    }

    /**
     * Retourne le pourcentage de plagiat présent dans le document.
     * @return  float   Pourcentage
     */
    public function getPlagiarizedPercentage() {
        return $this->plagiarizedPercentage;
    }

    /*     * *****************************************************************************************
     *                                       SETTERS
     * **************************************************************************************** */

    /**
     * Affecte un identifiant au document.
     * @param   int $id Identifiant
     */
    public function setId($id) {
        $this->id = $id;
    }

    /*     * *****************************************************************************************
     *                                       METHODS
     * **************************************************************************************** */

    /**
     * Ajoute un fragment au document.
     * @param   Fragment    $fragment   Fragment
     */
    public function addFragment($fragment) {
        array_push($this->fragments, $fragment);
    }

    /**
     * Mélange les fragments au sein du document.
     */
    public function shuffleFragments() {
        shuffle($this->fragments);
    }

    /**
     * Calcule le nombre de mots contenu dans le document en sommant le nombre de mots de chaque fragment formant le document <br/>
     * et calcule le pourcentage de plagiat contenu dans le document en sommant le pourcentage de plagiat de chaque fragment plagié du document.
     */
    public function calculateData() {
        $this->length = 0;
        $this->wordNumber = 0;
        $this->plagiarizedPercentage = 0;
        $textOfDocument = '';

        foreach ($this->fragments as $fragment) {
            $this->length += $fragment->getThisLength();
            $this->wordNumber += $fragment->getThisWordNumber();
            $textOfDocument .= $fragment . chr(10);
        }
        foreach ($this->fragments as $fragment) {
            $fragment->setThisOffset($this->getOffsetInDocument(trim($fragment->getText()), $textOfDocument));
            if ($fragment->getType() == 1) {
                $this->plagiarizedPercentage += number_format($fragment->getThisWordNumber() / $this->getWordNumber() * 100, 2, '.', '');
            }
        }
    }

    /**
     * Ecrit le document au format plain texte.
     * @param   string  $outputDocumentPath Chemin vers le répertoire des documents
     */
    public function writeAsPlainText($outputDocumentPath) {
        foreach ($this->fragments as $fragment) {
            $this->writeInFile($outputDocumentPath . '/' . $this->id . '.txt', $fragment . chr(10));
        }
    }

    /**
     * Ecrit le document en encadrant le texte des fragments avec les metadata au format XML.
     * @param   string  $outputDocumentPath Chemin vers le répertoire des documents
     */
    public function writePlainTextWithXmlMetaData($outputDocumentPath) {
        $id = 0;
        $this->writeInFile($outputDocumentPath . '/' . $this->id . '.xml', '<?xml version="1.0" encoding="UTF-8"?>' . chr(10));
        $this->writeInFile($outputDocumentPath . '/' . $this->id . '.xml', '<document reference="' . $this->id . '" plagiarized_percentage="' . $this->getPlagiarizedPercentage() . '" length="' . $this->getLength() . '" wordNumber="' . $this->getWordNumber() . '" >' . chr(10));

        foreach ($this->fragments as $fragment) {
            if ($fragment instanceof MonolingualFragment) {
                $feature = '<feature id="' . $id++ . '" '
                        . 'type="' . $this->getFragmentTypeNameByFragmentTypeId($fragment->getType()) . '" '
                        . 'percentage="' . number_format($fragment->getThisWordNumber() / $this->getWordNumber() * 100, 2, '.', '') . '" '
                        . 'this_wordNumber="' . $fragment->getThisWordNumber() . '" '
                        . 'this_language="' . $fragment->getLanguage() . '" '
                        . 'this_offset="' . $fragment->getThisOffset() . '" '
                        . 'this_length="' . $fragment->getThisLength() . '" '
                        . 'obfuscation_type="' . $this->getObfuscationNameByObfuscationId($fragment->getObfuscationType()) . '" '
                        . 'obfuscation_complexity="' . $fragment->getObfuscationComplexity() . '" '
                        . chr(10)
                        . 'source_reference="' . $fragment->getSource() . '" '
                        . 'source_wordNumber="' . $fragment->getSrcWordNumber() . '" '
                        . 'source_offset="' . $fragment->getSrcOffset() . '" '
                        . 'source_length="' . $fragment->getSrcLength() . '" '
                        . '>' . chr(10) . $fragment->getText() . chr(10) . '</feature>';
            } else if ($fragment instanceof CrossLanguageFragment) {
                $feature = '<feature id="' . $id++ . '" '
                        . 'type="' . $this->getFragmentTypeNameByFragmentTypeId($fragment->getType()) . '" '
                        . 'percentage="' . number_format($fragment->getThisWordNumber() / $this->getWordNumber() * 100, 2, '.', '') . '" '
                        . 'this_wordNumber="' . $fragment->getThisWordNumber() . '" '
                        . 'this_language="' . $fragment->getLanguage() . '" '
                        . 'this_offset="' . $fragment->getThisOffset() . '" '
                        . 'this_length="' . $fragment->getThisLength() . '" '
                        . 'obfuscation_type="' . $this->getObfuscationNameByObfuscationId($fragment->getObfuscationType()) . '" '
                        . 'obfuscation_complexity="' . $fragment->getObfuscationComplexity() . '" '
                        . chr(10)
                        . 'source_reference="' . $fragment->getSource() . '" '
                        . 'source_wordNumber="' . $fragment->getSrcWordNumber() . '" '
                        . 'source_offset="' . $fragment->getSrcOffset() . '" '
                        . 'source_length="' . $fragment->getSrcLength() . '" '
                        . chr(10)
                        . 'parallel_src_reference="' . $fragment->getParallelSource() . '" '
                        . 'parallel_src_language="' . $fragment->getParallelLanguage() . '" '
                        . 'parallel_src_offset="' . $fragment->getParallelSrcOffset() . '" '
                        . 'parallel_src_length="' . $fragment->getParallelSrcLength() . '" '
                        . 'parallel_src_wordNumber="' . $fragment->getParallelSrcWordNumber() . '" '
                        . '>' . chr(10) . $fragment->getText() . chr(10) . '</feature>';
            }
            $this->writeInFile($outputDocumentPath . '/' . $this->id . '.xml', $feature . chr(10));
        }
        $this->writeInFile($outputDocumentPath . '/' . $this->id . '.xml', '</document>');
    }

    /**
     * Ecrit le document au format employé lors de la PAN (seulement les metadata au format XML).
     * @param   string  $outputDocumentPath Chemin vers le répertoire des documents
     */
    public function writeOnlyXmlMetaData($outputDocumentPath) {
        $id = 0;
        $this->writeInFile($outputDocumentPath . '/' . $this->id . '_meta.xml', '<?xml version="1.0" encoding="UTF-8"?>' . chr(10));
        $this->writeInFile($outputDocumentPath . '/' . $this->id . '_meta.xml', '<document reference="' . $this->id . '" plagiarized_percentage="' . $this->getPlagiarizedPercentage() . '" length="' . $this->getLength() . '" wordNumber="' . $this->getWordNumber() . '" >' . chr(10));

        foreach ($this->fragments as $fragment) {
            if ($fragment instanceof MonolingualFragment) {
                $feature = '<feature id="' . $id++ . '" '
                        . 'type="' . $this->getFragmentTypeNameByFragmentTypeId($fragment->getType()) . '" '
                        . 'percentage="' . number_format($fragment->getThisWordNumber() / $this->getWordNumber() * 100, 2, '.', '') . '" '
                        . 'this_wordNumber="' . $fragment->getThisWordNumber() . '" '
                        . 'this_language="' . $fragment->getLanguage() . '" '
                        . 'this_offset="' . $fragment->getThisOffset() . '" '
                        . 'this_length="' . $fragment->getThisLength() . '" '
                        . 'obfuscation_type="' . $this->getObfuscationNameByObfuscationId($fragment->getObfuscationType()) . '" '
                        . 'obfuscation_complexity="' . $fragment->getObfuscationComplexity() . '" '
                        . chr(10)
                        . 'source_reference="' . $fragment->getSource() . '" '
                        . 'source_wordNumber="' . $fragment->getSrcWordNumber() . '" '
                        . 'source_offset="' . $fragment->getSrcOffset() . '" '
                        . 'source_length="' . $fragment->getSrcLength() . '" '
                        . '/>';
            } else if ($fragment instanceof CrossLanguageFragment) {
                $feature = '<feature id="' . $id++ . '" '
                        . 'type="' . $this->getFragmentTypeNameByFragmentTypeId($fragment->getType()) . '" '
                        . 'percentage="' . number_format($fragment->getThisWordNumber() / $this->getWordNumber() * 100, 2, '.', '') . '" '
                        . 'this_wordNumber="' . $fragment->getThisWordNumber() . '" '
                        . 'this_language="' . $fragment->getLanguage() . '" '
                        . 'this_offset="' . $fragment->getThisOffset() . '" '
                        . 'this_length="' . $fragment->getThisLength() . '" '
                        . 'obfuscation_type="' . $this->getObfuscationNameByObfuscationId($fragment->getObfuscationType()) . '" '
                        . 'obfuscation_complexity="' . $fragment->getObfuscationComplexity() . '" '
                        . chr(10)
                        . 'source_reference="' . $fragment->getSource() . '" '
                        . 'source_wordNumber="' . $fragment->getSrcWordNumber() . '" '
                        . 'source_offset="' . $fragment->getSrcOffset() . '" '
                        . 'source_length="' . $fragment->getSrcLength() . '" '
                        . chr(10)
                        . 'parallel_src_reference="' . $fragment->getParallelSource() . '" '
                        . 'parallel_src_language="' . $fragment->getParallelLanguage() . '" '
                        . 'parallel_src_offset="' . $fragment->getParallelSrcOffset() . '" '
                        . 'parallel_src_length="' . $fragment->getParallelSrcLength() . '" '
                        . 'parallel_src_wordNumber="' . $fragment->getParallelSrcWordNumber() . '" '
                        . '/>';
            }
            $this->writeInFile($outputDocumentPath . '/' . $this->id . '_meta.xml', $feature . chr(10));
        }
        $this->writeInFile($outputDocumentPath . '/' . $this->id . '_meta.xml', '</document>');
    }

    /**
     * Ecrit dans un fichier.
     * @param   string  $name       Nom du fichier
     * @param   string  $content    Contenu à écrire
     */
    private function writeInFile($name, $content) {
        $temporaryFileHandle = fopen($name, "a");
        fwrite($temporaryFileHandle, $content);
        fclose($temporaryFileHandle);
    }

    /**
     * Retourne sous forme de chaîne le type d'un fragment.
     * @param   int     $index  Indice du type
     * <br/>Interprétation :
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Original.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Plagié.
     * @return  string  Type
     */
    private function getFragmentTypeNameByFragmentTypeId($index) {
        return (($index == 0) ? 'original' : 'plagiarism' );
    }

    /**
     * Retourne sous forme de chaîne le type d'obfuscation opéré sur le fragment.
     * @param   int     $index  Indice du type
     * <br/>Interprétation :
     * <br/><b>&nbsp;&nbsp;-1 :</b> Aucune.
     * <br/><b>&nbsp;&nbsp;&nbsp;0 :</b> Substitue un certain pourcentage de mots par leur synonyme.
     * <br/><b>&nbsp;&nbsp;&nbsp;1 :</b> Inverse l'ordre un certain pourcentage de mots au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;2 :</b> Ajoute un certain pourcentage de bruit au sein des phrases.
     * <br/><b>&nbsp;&nbsp;&nbsp;3 :</b> Supprime du texte un certain pourcentage de mots.
     * <br/><b>&nbsp;&nbsp;&nbsp;4 :</b> Supprime la dernière lettre d'un certain pourcentage de mots du texte.
     * @return  string  Type
     */
    private function getObfuscationNameByObfuscationId($index) {
        $type = '';
        switch ($index) {
            case 0:
                $type = 'substitution';
                break;
            case 1:
                $type = 'split';
                break;
            case 2:
                $type = 'noise';
                break;
            case 3:
                $type = 'delation';
                break;
            case 4:
                $type = 'truncation';
                break;
            default:
                $type = 'none';
                break;
        }
        return $type;
    }

    /**
     * Retourne la position d'une sous-chaîne de caractères dans une chaîne de caractères.
     * @param   string  $excerpt    Sous-chaîne à rechercher
     * @param   string  $source     Chaîne dans laquelle rechercher
     * @return  int     Position
     */
    private function getOffsetInDocument($excerpt, $source) {
        $matches = array();
        if (preg_match_all('~' . preg_quote($excerpt, '~') . '~ui', $source, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $occurrence) {
                return $occurrence[1];
            }
        }
    }

}
