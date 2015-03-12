#! /usr/bin/php
<?php
require 'lib/POS.php';

if (count($argv) != 3)
	exit("USAGE: ".$argv[0]." <corpus_POS_csv> <to_match_POS_csv>\n");
	
$corpus = $argv[1];
$matchs = $argv[2];

$corpus = prepareCSV($corpus);
$matchs = prepareCSV($matchs);

$results = array();
foreach ($matchs['lines'] as $textRef => $metrics)
	$results[$textRef] = getCummulativeError($metrics['tags_freq'], $corpus);

print_r(bestResponses($results, 3));

echo "Done (corpus=".count($corpus['lines']).", matched=".count($matchs['lines']).")!\n";
?>
