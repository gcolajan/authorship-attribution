#! /usr/bin/php
<?php
require 'lib/transition.php';

// Récupération de la liste des tag par ordre d'apparition pour chaque corpus de chaque auteur
// Récupération des occurences pour chaque transition possible (tableau 2D)
$transition = getCorpusFrequency(ANALYZE.'/posfreq');

$datas = array();

// Préparation du header
$headers = $TAGS;
array_unshift($headers, "TAG");

// Pour chaque auteur, on récupère le tableau de transition
foreach ($transition as $author => $tab)
{
	// On créer le fichier résultat
	$file = fopen(ANALYZE.'/transitions/'.$author.'.csv', "w");
	
	// On écrit le header du fichier
	fputcsv($file, $headers);

	// On y écrit, avec le tag en première colonne, la liste des occurences
	foreach ($tab as $tag => $l)
	{
		array_unshift($l, $tag);
		fputcsv($file, $l);
	}
	
	fclose($file);
}
?>
