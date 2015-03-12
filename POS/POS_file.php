#! /usr/bin/php
<?php
require 'lib/POS.php';

if (count($argv) != 4)
	exit("USAGE: ".$argv[0]." <text_to_analyze> <name> <result_csv_file>\n");
	
$file = $argv[1];
$name = $argv[2];
$dest = $argv[3];

// Analyse du fichier
$analysis = getShortAnalysis($file);


// Préparation du fichier à écrire
$fp = fopen($dest, "w");

$header = $TAGS;
// On ajoute nos deux nouvelles métriques aux $TAGS existants
$header[] = 'WordPerLine';
$header[] = 'StdDeviation_WPL';
// On ajoute une colonne auteur (1ère colonne)
array_unshift($header, "AUTHOR");
// On enregistre la première ligne d'entête
fputcsv($fp, $header);

array_unshift($analysis, $name);
fputcsv($fp, $analysis);
fclose($fp);
?>
