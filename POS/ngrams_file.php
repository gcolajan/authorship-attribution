#! /usr/bin/php
<?php
require 'lib/ngrams.php';

$path = '';
$name = '';
$n = '';
$dest = '';
if (count($argv) == 5)
{
	$path = $argv[1];
	$name = $argv[2];
	$n = $argv[3];
	$dest = $argv[4];
}
else
	exit("USAGE: ".$argv[0]." <text_path> <name> <N> <result_csv_file>\n");

// Vérification et annonce
echo "Source path used: ".$path."\n";
echo "Size of the n-gram: ".$n."\n";

$time_start = microtime(true);

// Génération des POS
genPOS($path, TMP_FILE);

// Getting the frequency representation of the N grams.
$ngrams = getIntelligentFrequency(getOccurences(getNgrams(TMP_FILE, $n)), 1, 0.01, 5);
unlink(TMP_FILE);

// On créer le fichier résultat
$file = fopen($dest, "w");
	
// On écrit le header du fichier
fputcsv($file, ["ngram", "freq"]);

// On y écrit, avec le tag en première colonne, la liste des occurences
foreach ($ngrams as $ngram => $frequence)
	fputcsv($file, [$ngram, $frequence]);

fclose($file);	


$time = round(microtime(true) - $time_start, 2);
echo "Done (".$time." s)\n";
