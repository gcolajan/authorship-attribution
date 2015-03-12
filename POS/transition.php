#! /usr/bin/php
<?php
define('DATA', '../data');
define('ANALYZE', '../analyze');
define('TMP_FILE', 'tmpAnalyze');

//if (count($argv) != 2)
//	exit("USAGE: ".$argv[0]." <file_to_compare>\n");
	
//$file = $argv[1];

$tags = array_map("trim", file('tags'));

$frequency = getCorpusFrequency(ANALYZE.'/posfreq');




$datas = array();
foreach ($frequency as $author => $tab)
{
	$file = fopen(ANALYZE.'/transitions/'.$author.'.csv', "w");
	array_unshift($tags, "TAG");
	fputcsv($file, $tags);
	
	foreach ($tab as $tag => $l)
	{
		array_unshift($l, $tag);
		fputcsv($file, $l);
	}
	
	fclose($file);
}


$fp = fopen("frequency.csv", "w");

$tags[] = 'WordPerLine';
$tags[] = 'StdDeviation_WPL';
array_unshift($tags, "AUTHOR");
fputcsv($fp, $tags);
foreach ($datas as $author => $occ)
{
	$line = array_merge($occ, $wordsStats[$author]);
	$line = array_values($line);
	array_unshift($line, $author);
	fputcsv($fp, $line);
}


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
	global $tags;
	
	$freq = array();
	foreach ($tags as $tag)
	{
		$freq[$tag] = array();
		foreach ($tags as $t)
			$freq[$tag][$t] = 0;
	}
	$prev = '';
	$word = '';
	$nbWords = 0;

	$file = fopen($fileName, 'r');
	if ($file)
	{
		while (!feof($file))
		{
			$word = trim(fgets($file));
			if (in_array($word, $tags))
			{
				if (!empty($prev))
					$freq[$prev][$word]++;
					
				$prev = $word;
			}
		}
		fclose($file);
	}

	return $freq;
}
?>
