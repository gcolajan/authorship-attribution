<?php
require 'constantes.php';

function getCorpusFrequency($path) {
	$frequency = array();

	$dir = opendir($path) or die('Directory doenst exist');

	$handle = opendir($path);
	while($author = readdir($handle))
	{
		if($author != '.' && $author != '..')
		{
			$frequency[$author] = getOccurences($path.'/'.$author);
		}
	}
	closedir($handle);
	return $frequency;
}


function getOccurences($fileName) {
	global $TAGS;
	
	$freq = array();
	foreach ($TAGS as $tag)
	{
		$freq[$tag] = array();
		foreach ($TAGS as $t)
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
			if (in_array($word, $TAGS))
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
