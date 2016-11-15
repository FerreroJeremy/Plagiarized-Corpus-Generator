# Plagiarized-Corpus-Generator
This is a corpus builder of documents for the evaluation of plagiarism detection tools.

A document is represented by a sequence of fragments that can be plagiarized or originals.
The builder takes as input a set of files where it will extract the plagiarized fragments, another set of files where it will extract the original fragments, and a list of parameters influencing the output documents.
The plagiarized files are not necessarily really plagiarized files; it is just a set of files that the builder uses to build the plagiarized fragments and thus it is the content which must be retrieving by plagiarism detection.

## Input

The input files must be in plain text. 
It is necessary to specify a path of a directory containing the original files (files not being present on the web in the case of the evaluation of Web plagiarism detection). 
The specification of the plagiarized files can be done either in the same way as the original files, by specifying a folder containing plain text files either by specifying a list of urls (in this way, it is sure that the plagiarized fragments will be available on the Web).

## Settings

* The maximum recommended number of times that one file can be used as input resource. The number is just an advised number of times i.e. the builder will always consider as far as possible the limit but if it is forced to transgress this number in order to generate the specified number of documents, it will do it, while minimizing the number of extractions by files.
* The number of desired output documents. 
* The length (in number of words) of the output documents. A minimum, maximum and average value is required. The lengths of generated documents will vary between the minimum and maximum value toward the average value.
* The percentage of plagiarism in the output documents. This is the same as the length mentioned above, a minimum, maximum and average value is required.
* The distribution of the lengths of the plagiarized fragments of the output documents. The lengths of the non plagiarized fragments are left to the discretion of the builder;
* The percentage of small plagiarized fragments (between min and max words);
* The percentage of medium plagiarized fragments (between min and max words);
* The percentage of long plagiarized fragments (between min and max words).

The sum of these percentages should be equal to 100. 
More varied resources are given in input; more the output documents will match the expectations of the user settings.

## Fragment Obfuscation

Obfuscation is the act of hiding a plagiarism so that it becomes more difficult to detect, in particular by software or detection tools.
The builder is able to generate several types of obfuscation, all listed below:

* <i>None (No obfuscation):</i> the text is copied without any change, i.e. it's a strictly copy and paste; 
* <i>Change of order:</i> the words of the text are randomly shuffled with the risk that the text is no longer syntactically correct;
* <i>Substitution:</i> some words of the text are replaced by one of their –onym words (e.g. synonyms, hypernyms or antonym). The text is apparently no longer the same as the original, but still retains a similar meaning (not necessarily the same but may be the opposite or complementary sense). The –onym words are extracted from DBNary;
* <i>Addition:</i> some characters or words are randomly inserted between the words of the text;
* <i>Deletion:</i> some words are randomly removed;
* <i>Truncation:</i> the last letter of some words is randomly removed.

It is possible to specify the percentage of plagiarized fragments slightly, moderately or heavily obfuscated. 
The sum of these percentages should be equal to 100. This value is called obfuscation density. It determines the proportion of words in the text that will be affected by the obfuscation. 

## Output

Each output document is represented by three files.
* A plain text file with the extension <i>.txt</i>.
* A XML file with the extension <i>_meta.xml</i> including only the meta-data about the related plain text file.
* A XML file that contains the fragments in their text form as they are in the plain text file but framed by their meta-data as they are in the <i>_meta.xml</i> file.

The root element in the output XML files is <i>\<document\></i>.
It is composed of a list of <i>\<feature\></i> elements, each representing a fragment. 
The <i>\<document\></i> has the following attributes: the percentage of total plagiarism that contains the document, and its lengths in characters and its size in words. 
Each <i>\<feature\></i> element contains meta-data relating to the fragment.

Below the meta-data and their meanings:
* <i>type:</i> if the fragment is plagiarized or original;
* <i>percentage:</i> percentage represented by the fragment within the generated document;
* <i>this_wordNumber:</i> number of words of the fragment within the generated document;
* <i>this_language:</i> language of the fragment within the generated document;
* <i>this_offset:</i> start offset of the fragment within the generated document;
* <i>this_length:</i> number of characters of the fragment within the generated document;
* <i>obfuscation_type:</i> type of obfuscation of the fragment;
* <i>obfuscation_complexity:</i> obfuscation density of the fragment;
* <i>source_reference:</i> name, path or urls of the source file;
* <i>source_wordNumber:</i> number of words of the fragment within the source file;
* <i>source_offset:</i> start offset of the fragment within the source file;
* <i>source_length:</i> number of characters of the fragment within the source file.

Below the meta-data that are specific to the cross-language context:
* <i>parallel_src_reference:</i> name, path or urls of the parallel file of the original source file;
* <i>parallel_src_language:</i> language in which is written the fragment within the parallel file of the original source file;
* <i>parallel_src_offset:</i> start offset of the fragment within the parallel file of the original source file;
* <i>parallel_src_length:</i> number of characters of the fragment within the parallel file of the original source file;
* <i>parallel_src_wordNumber:</i> number of words of the fragment within the parallel file of the original source file.

To evaluate a plagiarism detection tool, you simply must give it the generated plain text file in input and compare its analysis report with the generated <i>_meta.xml</i> file related to the plain text file analyzed.

## Usage

A sample of the use of the <i>PlagiarizedCorpusBuilder</i> library is in the script <i>test.php</i>.

## How does it work ?

<i>Under construction</i>

