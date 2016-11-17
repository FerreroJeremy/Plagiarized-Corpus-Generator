# Plagiarized-Corpus-Generator
This is a generator of documents for the evaluation of plagiarism detection tools.

A document is represented by a sequence of fragments that can be plagiarized or originals.
The generator takes as input a set of files where it will extract the plagiarized fragments, another set of files where it will extract the original fragments, and a list of parameters influencing the generation of the output documents.
The specified plagiarized files are not necessarily really plagiarized files; it is just a set of files that the generator uses to build the plagiarized fragments and thus it is the content which must be retrieving by a plagiarism detection.

## Input

The input files must be in plain text. 
It is necessary to specify a path of a directory containing the original files (files not being present on the web in the case of the evaluation of Web plagiarism detection). 
The specification of the plagiarized files can be done either in the same way as the original files, by specifying a folder containing plain text files, either by specifying a list of urls (in this way, it is sure that the plagiarized fragments will be available on the Web).

## Settings

* The number of desired output documents. 
* The maximum recommended number of times that one file can be used as input resource. The number is just an advised number of times, i.e. the generator will always consider as far as possible the limit but if it is forced to transgress this number in order to generate the specified number of documents, it will do it, while minimizing the number of extractions by files.
* The length (in number of words) of the output documents. A minimum, maximum and average value is required. The lengths of the generated documents will vary between the minimum and the maximum value toward the average value.
* The percentage of plagiarism in the output documents. This is the same as the length mentioned above, a minimum, a maximum and an average value is required.
* The distribution of the lengths of the plagiarized fragments of the output documents:
  * The percentage of small plagiarized fragments (between min and max words, these extremes can also be specified);
  * The percentage of medium plagiarized fragments (between min and max words, these extremes can also be specified);
  * The percentage of long plagiarized fragments (between min and max words, these extremes can also be specified).

The lengths of the non plagiarized fragments are left to the discretion of the generator. <br/>
The sum of the complementary percentages should be equal to 100 in each case. <br/>
More various resources are given in input, more the output documents will match the expectations of the user settings.

## Fragment Obfuscation

Obfuscation is the act of hiding a plagiarism to make it more difficult to detect, in particular by software or detection tools.
The generator is able to generate several types of obfuscation, all listed below:

* <i>None (No obfuscation):</i> the text is copied without any change, i.e. copy and paste; 
* <i>Order change:</i> the words of the text are randomly shuffled with the risk that the text is no longer syntactically correct;
* <i>Substitution:</i> some words of the text are replaced by one of their –onym words (e.g. synonyms, hypernyms or antonym). The text is apparently no longer the same as the original, but still retains a similar meaning (not necessarily the same but may be the opposite or complementary sense). The –onym words are extracted from [DBNary](https://github.com/FerreroJeremy/DBNary-PHP-Interface);
* <i>Addition:</i> some characters or words are randomly inserted between the words of the text;
* <i>Deletion:</i> some words are randomly removed;
* <i>Truncation:</i> the last letter of some words is randomly removed.

It is also possible to specify the percentage of plagiarized fragments slightly, moderately or heavily obfuscated. 
The sum of these percentages should be equal to 100. 
These values are called obfuscation densities. 
They determine the proportion of words in the fragment that will be affected by the obfuscation.

## How does it work ?

In a first time, knowing the number of documents to generate and the law of distribution of the lengths of these documents, a number of words is assigned to each document to generate. 
In a second time, knowing the law of distribution of plagiarism percentages, the number of plagiarized words are calculated and assigned in each document to generate.

Two choices are possible for the third part (the generation of the fragments).
It is possible to generate the output documents by specifying the number of the fragments or the length of the fragments. 
* <i>Build by number of fragments:</i>
Knowing the distribution probability of the number of fragments by document, a number of fragments is assigned to each document to generate (their lengths are set randomly between min and max). 
Then, the fragments will be labeled plagiarized or original according to their lengths to match the desired number of words plagiarized.
* <i>Build by size of the fragments:</i>
Knowing the distribution percentage of sizes (small, medium or large) of the fragments by document, a number of words for each types of plagiarized fragments according to their size is calculated. 
Then, a number of fragments of each type of size is randomly generated, such that their sum is the calculated value of the previous step.

For each fragment, a contiguous sequence of sentences as close as the number of words intended for the related extraction (i.e. fragment) is extracted from an adequate resource (depending on it should be plagiarized or original).

## Output

Each output document is represented by three files:
* A plain text file with the extension <i>.txt</i>;
* A XML file with the extension <i>_meta.xml</i> including only the meta-data about the related plain text file;
* A XML file that contains the fragments in their text form as they are in the plain text file but framed by their meta-data as they are in the <i>_meta.xml</i> file.

An output XML files consists of a root markup <i>\<document\></i>, which comprises a list of <i>\<feature\></i> markups each relating a fragment. 
The <i>\<document\></i> markup has the following attributes: the percentage of total plagiarism that contains the document, and its lengths in characters and words. 
Each <i>\<feature\></i> markup contains meta-data relating to a fragment.

The meta-data and their meanings are reported below:
* <i>type:</i> if the fragment is plagiarized or original;
* <i>percentage:</i> percentage represented by the fragment within the document;
* <i>this_wordNumber:</i> number of words of the fragment within the document;
* <i>this_language:</i> language of the fragment;
* <i>this_offset:</i> start offset of the fragment within the document;
* <i>this_length:</i> number of characters of the fragment within the document;
* <i>obfuscation_type:</i> type of obfuscation of the fragment;
* <i>obfuscation_complexity:</i> obfuscation density of the fragment;
* <i>source_reference:</i> name, path or url of the source file;
* <i>source_wordNumber:</i> number of words of the fragment within the source file;
* <i>source_offset:</i> start offset of the fragment within the source file;
* <i>source_length:</i> number of characters of the fragment within the source file.

The meta-data that are specific to the cross-language context are reported below:
* <i>parallel_src_reference:</i> name, path or url of the parallel file of the original source file;
* <i>parallel_src_language:</i> language in which is written the fragment within the parallel file;
* <i>parallel_src_offset:</i> start offset of the fragment within the parallel file;
* <i>parallel_src_length:</i> number of characters of the fragment within the parallel file;
* <i>parallel_src_wordNumber:</i> number of words of the fragment within the parallel file.

These structure and nomenclature are compatible with those adopted by the PAN (http://pan.webis.de/clef15/pan15-web/plagiarism-detection.html).

To evaluate a plagiarism detection tool, you simply must give it the generated plain text file in input and compare its analysis report with the generated <i>_meta.xml</i> file related to the plain text file analyzed.

## Usage

A sample of the use of the <i>PlagiarizedCorpusBuilder</i> library can be found in the script <i>test.php</i>.

<i>The comments are in French but the names of attributes and methods are in English and quite self-explanatory.</i> <br/>
If you have additional questions, please send it to me by email at jeremy.ferrero@compilatio.net.

## Conception
<p align="center"><img src="https://raw.githubusercontent.com/FerreroJeremy/Plagiarized-Corpus-Generator/master/docs/PlagiarizedCorpusGenerator.png?token=AL6uBo2o7exZBkeeg_cbouFqCYkIbWQJks5YNCVfwA%3D%3D"></p>
The above diagram was modeled with <a rel="staruml" href="http://staruml.io/">StarUML</a>.

