#! /usr/bin/php
<?php
define('PATH', '../../Reuters50_50');
define('TMP_FILE', 'analyze');

if (count($argv) != 2)
	exit("USAGE: ".$argv[0]." <file_to_compare>\n");
	
$file = $argv[1];

$frequency = getCorpusFrequency(PATH.'/frequency');

// Analyse du fichier soumis
exec("cat $file | tree-tagger-english | cut -f 2 | grep [A-Z] > ".TMP_FILE);
$analyse = getFrequency(TMP_FILE);
unlink(TMP_FILE);

// Comparaison des fréquences du fichier analysé avec le corpus
$diff = array();
foreach ($frequency as $author => $tags) {
	$diff[$author] = 0.0;
	foreach ($tags as $tag => $freq)
	{
		if (!isset($analyse[$tag]))
			$analyse[$tag] = 0.0;
			
		$diff[$author] += abs($analyse[$tag] - $freq);
	}
}

asort($diff);
print_r($diff);


function getCorpusFrequency($path) {
	$frequency = array();

	$dir = opendir($path) or die('Directory doenst exist');

	$handle = opendir($path);
	while($author = readdir($handle))
	{
		if($author != '.' && $author != '..')
		{
			$frequency[$author] = getFrequency($path.'/'.$author);
		}
	}
	closedir($handle);
	return $frequency;
}


function getFrequency($fileName) {
	$freq = array();
	$nbWords = 0;

	$file = fopen($fileName, 'r');
	if ($file)
	{
		while (!feof($file))
		{
			$word = trim(fgets($file));
			if (!isset($freq[$word]))
				$freq[$word] = 0;
			
			$freq[$word]++;
			$nbWords++;
		}
		fclose($file);
	}
	
	foreach ($freq as $tag => $occ)
		$freq[$tag] = $occ/$nbWords;
		
	return $freq;
}
?>
