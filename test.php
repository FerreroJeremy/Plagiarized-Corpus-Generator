<?php

set_time_limit(100000);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('upload_max_filesize', '64M');
ini_set('post_max_size', '64M');

require_once("lib/PlagiarizedCorpusGenerator.php");

$PCG = new PlagiarizedCorpusGenerator();

$PCG->setPlagiarizedInputDocumentType(0);

$PCG->setTextArea('https://fr.wikipedia.org/wiki/Paris');
$PCG->setPlagiarizedDocumentsPath('lib/upload/plagiarized_ressources/');
$PCG->setParallelDocumentsPath('lib/upload/plagiarized_ressources/');

$PCG->setFragmentType(0);
$PCG->setMaximumUsageOfPlagiarizedRessource(10);

$PCG->setOriginalDocumentsPath('lib/upload/original_ressources');

$PCG->setOutputDocumentNumber(12);
$PCG->setOutputDocumentPath('lib/output');


$PCG->setMinimumDocumentlength(300);
$PCG->setMaximumDocumentlength(1000);
$PCG->setAverageDocumentlength(800);

$PCG->setMinimumPercentageOfPlagiarismByDocument(30);
$PCG->setMaximumPercentageOfPlagiarismByDocument(80);
$PCG->setAveragePercentageOfPlagiarismByDocument(60);

$PCG->setAveragePercentageOfLongPlagiarizedFragmentNumber(0);
$PCG->setAveragePercentageOfMediumPlagiarizedFragmentNumber(50);
$PCG->setAveragePercentageOfShortPlagiarizedFragmentNumber(50);

$PCG->setMinimumWordNumberForLongPlagiarizedFragments(50);
$PCG->setMaximumWordNumberForLongPlagiarizedFragments(150);

$PCG->setMinimumWordNumberForMediumPlagiarizedFragments(25);
$PCG->setMaximumWordNumberForMediumPlagiarizedFragments(50);

$PCG->setMinimumWordNumberForShortPlagiarizedFragments(8);
$PCG->setMaximumWordNumberForShortPlagiarizedFragments(25);

$PCG->setMinimumWordNumberOfOriginalFragments(30);
$PCG->setMaximumWordNumberOfOriginalFragments(300);

$PCG->setNoneObfuscationPercentage(100);
$PCG->setSubstitutionObfuscationPercentage(0);
$PCG->setSplitObfuscationPercentage(0);
$PCG->setNoiseObfuscationPercentage(0);
$PCG->setDelationObfuscationPercentage(0);
$PCG->setTruncationObfuscationPercentage(0);

$PCG->setLowObfuscationPercentage(0);
$PCG->setMediumObfuscationPercentage(0);
$PCG->setStrongObfuscationPercentage(0);

$PCG->initializeDataset();
$PCG->initializeParameters();

if ($PCG->checkParameters()) {
    $PCG->run();
    $PCG->calculateData();
    $PCG->writeOutputDataset(1);
    $PCG->writeOutputDataset(2);
    $PCG->writeOutputDataset(3);
}

echo $PCG->getDocumentNumberOfOutputDataset() . ' generated documents, <br/>';
echo 'with an average length of ' . $PCG->getAverageSizeOfDocuments() . ' words, <br/>';
echo 'with an average size of ' . $PCG->getAverageFragmentNumberOfDocuments() . ' fragments, <br/>';
echo 'and an average plagiarism of ' . $PCG->getAveragePlagiarizedPercentageOfDocuments() . ' %. <br/>';
