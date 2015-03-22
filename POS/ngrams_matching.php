#! /usr/bin/php
<?php
require 'lib/ngrams.php';

// HOWTO PROCESS?
// Taking the text to analyse
// Compare it to the other authors
// For each author if we can FIND at least a third concordance, that's seem to be quite good!
// To distinguish two authors with the same amount of ngrams concordance, we sort by cummulative error

if (count($argv) != 3)
	exit("USAGE: ".$argv[0]." <ngrams_dir_path> <ngram_file>\n");

$corpusPath = $argv[1];
$comparePath = $argv[2];

// Retrieving datas from our corpus splitted into authors
$corpus = array();
$dir = opendir($corpusPath) or die('Directory doesn\'t exist');
while($file = readdir($dir))
	if($file != '.' && $file != '..')
		$corpus[strstr($file, '.', true)] = retrieveNgrams($corpusPath.'/'.$file);
closedir($dir);

// Getting ngrams of the text we want to match with one of our author
$compare = retrieveNgrams($comparePath);

$matching = array();
foreach ($corpus as $author => $ngrams)
	$matching[$author] = getConcordance($compare, $ngrams);

arsort($matching);
echo implode(',', array_slice(array_keys($matching), 0, 5))."\n";
?>
