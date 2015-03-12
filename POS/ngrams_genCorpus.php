#! /usr/bin/php
<?php
require 'lib/ngrams.php';

$path = '';
$n = '';
$dest = '';
if (count($argv) == 1)
{
	$path = ANALYZE.'/posfreq';
	$n = 2;
}
else if (count($argv) == 3)
{
	$path = $argv[1];
	$n = $argv[2];
}
else
	exit("USAGE: ".$argv[0]." <POS_Path> <N>\n");

// Vérification et annonce
$dir = opendir($path) or die('Directory doenst exist');
echo "Source path used: ".$path."\n";
echo "Size of the n-gram: ".$n."\n";
echo "\n";
echo "Proceeding can take long...\n";

$time_start = microtime(true);

// Récupération des fichiers sources
$files = array();
$handle = opendir($path);
while($author = readdir($handle))
	if($author != '.' && $author != '..')
		$files[$author] = $path.'/'.$author;
closedir($handle);

// Having the list of the existing ngrams
$ngrams = array();
foreach ($files as $author => $path)
	$ngrams[$author] = getNgrams($path, $n);

// Calculating the number of occurences to process frequencies for each ngram
$freq = array();
foreach ($ngrams as $author => $ngram)
	$freq[$author] = getIntelligentFrequency(getOccurences($ngram), 1, 0.01, 5);

// Generating the CSV file
foreach ($freq as $author => $ngrams)
{
	// Intelligent frenquency method can lead to empty files (each ngrams are unique)
	if (count($ngrams) == 0) {
		echo "[DROP] ".$author."\n"; // No interesting ngrams (every ngram is represented only once)
		continue;
	}

	// On créer le fichier résultat
	$file = fopen(ANALYZE.'/ngrams/'.$author.'.csv', "w");
	
	// On écrit le header du fichier
	fputcsv($file, ["ngram", "freq"]);

	// On y écrit, avec le tag en première colonne, la liste des occurences
	foreach ($ngrams as $ngram => $frequence)
		fputcsv($file, [$ngram, $frequence]);

	fclose($file);	
}

//print_r(count($occ));
$time = round(microtime(true) - $time_start, 2);
echo "Done (".$time." s)\n";
?>
