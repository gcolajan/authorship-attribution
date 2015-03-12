#! /usr/bin/php
<?php
define('DATA', '../data');
define('ANALYZE', '../analyze');
define('TMP_FILE', 'tmpAnalyze');

if (count($argv) != 2)
	exit("USAGE: ".$argv[0]." <file_to_compare>\n");
	
$file = $argv[1];

$frequency = getCorpusFrequency(ANALYZE.'/posfreq');

//print_r($frequency);

$tags = array_map("trim", file('tags'));

$datas = array();
foreach ($frequency as $author => $occurences)
{
	$datas[$author] = array();
	foreach ($tags as $tag)
		$datas[$author][$tag] = 0;

	foreach ($occurences as $tag => $occ)
	{
		if (in_array($tag, $tags))
			$datas[$author][$tag] = $occ;
	}
}

$wordsStats = getWords('../data/C50train');


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

/*


// Analyse du fichier soumis
exec("cat $file | tree-tagger-english | cut -f 2 > ".TMP_FILE);
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
print_r($diff);*/

function getWords($path) {
	$dir = opendir($path) or die('Directory doenst exist');
	$authors = array();

	$handle = opendir($path);
	while($author = readdir($handle))
	{
		if($author != '.' && $author != '..')
		{
			$tmp = array();
			$sum = 0;
			$authorHandle = opendir($path.'/'.$author);
			while($file = readdir($authorHandle))
			{
				
				if($file != '.' && $file != '..')
				{
					$words = file($path.'/'.$author.'/'.$file);
					foreach ($words as $key => $value)
					{
						$count = str_word_count($value);
						$tmp[] = $count;
						$sum += $count;
					}
				}
			}
			closedir($authorHandle);

			$avg = $sum/count($tmp);
			$sommeEcartType = 0;
			foreach ($tmp as $words)
				$sommeEcartType += pow($words - $avg, 2);
				
			$sommeEcartType /= count($tmp);
			$sommeEcartType = sqrt($sommeEcartType);
				
			$authors[$author] = array(
				'avg' => $avg,
				'sd' => $sommeEcartType
			);
			
		}
	}
	
	return $authors;
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
		
	return $freq;
}
?>
