#! /usr/bin/php
<?php
require 'lib/transition.php';

if (count($argv) != 3)
	exit("USAGE: ".$argv[0]." <text_to_analyse> <result_csv_file>\n");
	
$file = $argv[1];
$dest = $argv[2];

genPOS($file, TMP_FILE);

// Récupération de la liste des tag par ordre d'apparition pour chaque corpus de chaque auteur
// Récupération des occurences pour chaque transition possible (tableau 2D)
$transitions = getOccurences(TMP_FILE);

$datas = array();

// Préparation du header
$headers = $TAGS;
array_unshift($headers, "TAG");

// On créer le fichier résultat
$fp = fopen($dest, "w");

// On écrit le header du fichier
fputcsv($fp, $headers);

// On y écrit, avec le tag en première colonne, la liste des occurences
foreach ($transitions as $tag => $l)
{
	array_unshift($l, $tag);
	fputcsv($fp, $l);
}

fclose($fp);
unlink(TMP_FILE);

?>
