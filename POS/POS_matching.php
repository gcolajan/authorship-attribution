#! /usr/bin/php
<?php
require 'lib/POS.php';

if (count($argv) != 3)
	exit("USAGE: ".$argv[0]." <corpus_POS_csv> <to_match_POS_csv>\n");
	
$corpus = $argv[1];
$matchs = $argv[2];

$corpus = prepareCSV($corpus);
$matchs = prepareCSV($matchs);

// Having some results with frequency matching (cummulative error)
$results = array();
foreach ($matchs['lines'] as $textRef => $metrics)
	$results[$textRef] = getCummulativeError($metrics['tags_freq'], $corpus);

print_r(bestResponses($results, 3));

// Having some results with WPL metric (half of author : nearest and after, taking 10 lowest SD)
$wpl = array();
foreach ($matchs['lines'] as $textRef => $metrics)
	$wpl[$textRef] = filterAccuracy(getNearest($metrics['wpl_avg'], $corpus), $metrics['wpl_sd'], $corpus, 10);

print_r($wpl);

echo "Done (corpus=".count($corpus['lines']).", matched=".count($matchs['lines']).")!\n";
?>
