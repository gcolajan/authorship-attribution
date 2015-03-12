#! /usr/bin/php
<?php
require 'lib/transition.php';

if (count($argv) != 3)
	exit("USAGE: ".$argv[0]." <text_to_analyze> <result_csv_file>\n");
	
$file = $argv[1];
$dest = $argv[2];


// Récupération de la liste des tag par ordre d'apparition pour chaque corpus de chaque auteur
// Récupération des occurences pour chaque transition possible (tableau 2D)
$transition = getFileFrequency($file);

$datas = array();

// Préparation du header
$headers = $TAGS;
array_unshift($headers, "TAG");

// On créer le fichier résultat
$file = fopen($dest, "w");

// On écrit le header du fichier
fputcsv($file, $headers);

// On y écrit, avec le tag en première colonne, la liste des occurences
foreach ($tab as $tag => $l)
{
	array_unshift($l, $tag);
	fputcsv($file, $l);
}

fclose($file);

?>
