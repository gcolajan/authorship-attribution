<?php
require 'constantes.php';

/***********************************
 * Analysis methods
 ***********************************/

function bestResponses($result, $limit=1) {
	$best = array();

	foreach ($result as $textRef => $res)
	{
		asort($res);
		$best[$textRef] = array_slice($res, 0, $limit);
	}

	return $best;
}


function getCummulativeError($oneFreqList, $corpus) {
	$cummulativeError = array();
	foreach ($corpus['lines'] as $author => $metrics)
	{
		$cummulativeError[$author] = 0.0;
		$nbTags = count($metrics['tags_freq']);
		for ($i = 0 ; $i < $nbTags ; ++$i)
			$cummulativeError[$author] += abs($metrics['tags_freq'][$i] - $oneFreqList[$i]);
	}

	return $cummulativeError;
}


function prepareCSV($path) {
	$first = true;
	$handle = fopen($path, "r");
	$csv = array(
		'header' => array(),
		'lines' => array());
	if ($handle !== FALSE)
	{
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			if ($first)
			{
				array_shift($data);
				$csv['header'] = $data;
				$first = false;
			}
			else
				$csv['lines'][array_shift($data)] = array(
					'wpl_sd' => array_pop($data),
					'wpl_avg' => array_pop($data),
					'tags_occ' => $data,
					'tags_freq' => mapFreq($data, array_sum($data)));
		}
		fclose($handle);
	}

	return $csv;
}


function mapFreq($occurences, $sum) {
	foreach ($occurences as $k => $occ)
		$occurences[$k] = $occ/$sum;
	return $occurences;
}

/***********************************
 * Generation methods
 ***********************************/

function getShortAnalysis($textPath) {
	genPOS($textPath, TMP_FILE);

	$words = getTextWord($textPath);
	$stats = getLinesStats($words);

	$freq = getFrequency(TMP_FILE);

	// Suppression du fichier temporaire
	unlink(TMP_FILE);

	return array_merge($freq, $stats);
}

// Prend une liste de fichiers ou un seul fichier [author => path]
// $result sera le fichier dans lequel sera enregistré le résultat
function getCorpusAnalysis($src, $training, $result) {
	global $TAGS;

	// Si la source n'est pas un array, on le transforme
	if (!is_array($src))
		$src = array("default" => $src);

	$frequency = array();

	foreach ($src as $author => $path)
		$frequency[$author] = getFrequency($path);


	/*
	 * Normalisation des données via les $TAGS (tous mis à 0)
	*/
	$datas = array();
	foreach ($frequency as $author => $occurences)
	{
		// Je prépare le tableau en initialisant la présence de tous les $TAGS existants à 0
		$datas[$author] = array();
		foreach ($TAGS as $tag)
			$datas[$author][$tag] = 0;

		foreach ($occurences as $tag => $occ)
		{
			if (in_array($tag, $TAGS))
				$datas[$author][$tag] = $occ;
		}
	}

	// Obtention de statistiques :
	//  NOmbre moyen de mots par ligne
	//  Écart-type sur cette même métrique
	$wordsStats = getWords($training);

	// Écriture du résultat dans le fichier frequency.csv
	$fp = fopen(ANALYZE."/frequency.csv", "w");

	$header = $TAGS;
	// On ajoute nos deux nouvelles métriques aux $TAGS existants
	$header[] = 'WordPerLine';
	$header[] = 'StdDeviation_WPL';
	// On ajoute une colonne auteur (1ère colonne)
	array_unshift($header, "AUTHOR");
	// On enregistre la première ligne d'entête
	fputcsv($fp, $header);

	// On sauve nos informations
	foreach ($datas as $author => $occ)
	{	
		// On fusionne les occurences avec les statistiques WPL
		$line = array_merge($occ, $wordsStats[$author]);
		// On supprime les clés
		$line = array_values($line);
		// On ajoute l'auteur sur la première colonne
		array_unshift($line, $author);
		// On enregistre
		fputcsv($fp, $line);
	}
}

function getWords($path) {
	$dir = opendir($path) or die('Directory doesn\'t exist');
	$authors = array();

	$handle = opendir($path);
	while($author = readdir($handle))
	{
		if($author != '.' && $author != '..')
		{
			$res = array();
			$authorHandle = opendir($path.'/'.$author);
			while ($file = readdir($authorHandle))
				if ($file != '.' && $file != '..')
					$res = array_merge($res, getTextWord($path.'/'.$author.'/'.$file));
			closedir($authorHandle);

			$authors[$author] = getLinesStats($res);
		}
	}
	
	return $authors;
}

function getTextWord($textPath) {
	$words = file($textPath);
	$tabCount = array();
	foreach ($words as $key => $value)
		$tabCount[] = str_word_count($value);
	return $tabCount;
}

function getLinesStats($wordsPerLines) {
	$sum = count($wordsPerLines);

	$avg = array_sum($wordsPerLines)/$sum;

	$ecartType = 0;
	foreach ($wordsPerLines as $words)
		$ecartType += pow($words - $avg, 2);
	$ecartType /= $sum;
	$ecartType = sqrt($ecartType);

	return array(
				'avg' => $avg,
				'sd' => $ecartType
			);
}


function getFrequency($fileName) {
	global $TAGS;

	$freq = array();

	foreach ($TAGS as $tag)
		$freq[$tag] = 0;

	$file = fopen($fileName, 'r');
	if ($file)
	{
		while (!feof($file))
		{
			$word = trim(fgets($file));
			if (in_array($word, $TAGS)) // Cleaning tags (only if referenced)
				$freq[$word]++;
		}
		fclose($file);
	}
		
	return $freq;
}
?>
