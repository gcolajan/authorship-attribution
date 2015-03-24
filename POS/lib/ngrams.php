<?php
require 'constantes.php';

// Caracter used to join the tags to make the ngrams easily comparable
define('GLUE', '!');


/***********************************
 * Analysis methods
 ***********************************/

function getConcordance($cmp, $src) {

	$nbConcord = 0;
	$srcGrams = array_keys($src);
	foreach ($cmp as $n => $freq)
		if (in_array($n, $srcGrams))
			$nbConcord++;

	return round($nbConcord/floatval(count($cmp)), 4);
}

function retrieveNgrams($path) {
	$first = true;
	$handle = fopen($path, "r");
	$csv = array();
	if ($handle !== FALSE)
	{
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			if ($first)
				$first = false;
			else
				$csv[array_shift($data)] = array_shift($data);
		}
		fclose($handle);
	}

	return $csv;
}

/***********************************
 * Generation methods
 ***********************************/

// We get frequencies of ngrams who as represented more than 1
function getIntelligentFrequency($occurences, $represented=1, $lowestRepresentation=0.01, $precision=5) {
	$sum = array_sum($occurences);
	$freq = array();
	foreach ($occurences as $k => $occ)
		if ($occ > $represented)
		{
			$f = round($occ/$sum, $precision);
			if ($f > $lowestRepresentation)
				$freq[$k] = $f;
		}
	ksort($freq);
	return $freq;
}


function getOccurences($list) {
	$occ = array();

	ksort($list);
	foreach ($list as $el) {
		if (isset($occ[$el]))
			$occ[$el]++;
		else
			$occ[$el] = 1;
	}

	return $occ;
}

// Get all the n-grams for a POS file with a specified size.
// Exemple : A B C D with $n=2 will give you : A!B, B!C, C!D (not only A!B and C!D)
function getNgrams($path, $n) {
	global $TAGS;

	$ngrams = array();

	$file = fopen($path, 'r');
	if ($file)
	{
		$tmp = array();
		for ($i = 0 ; $i < $n ; $i++)
			$tmp[] = array();
		$trigger = 0;
		while (!feof($file))
		{
			$tag = trim(fgets($file));
			if (in_array($tag, $TAGS)) // Cleaning tags (only if referenced)
			{
				// We store the our current trigger (decide wich tmp buffer will be glued)
				$curTrigger = $trigger%$n;

				// We put our tag in each tmp buffer
				for ($i = 0 ; $i < $n ; $i++)
					$tmp[$i][] = $tag;

				// We glue ONE buffer
				$ngrams[] = implode(GLUE, $tmp[$curTrigger]);

				// We reset that buffer to continue
				$tmp[$curTrigger] = array();
			
				// We will glue the next buffer next time
				$trigger = ($trigger+1)%$n;
			}
		}
		fclose($file);
	}
	return $ngrams;
}
?>
