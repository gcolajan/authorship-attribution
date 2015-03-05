#! /usr/bin/php
<?php

if (count($argv) != 2)
	exit("USAGE: "+$argv[0]+" <file_to_compare>");


function getFrequency() {
	$frequency = array();

	$path = 'Reuters50_50/frequency';
	$dir = opendir($path) or die('Directory doenst exist'); // on ouvre le contenu du dossier courant

	$handle = opendir($path);
	while($author = readdir($handle))
	{
		if($author != '.' && $author != '..')
		{
			$frequency[$author] = array();
			$nbWords = 0;
	
			$file = fopen($path.'/'.$author, 'r');
			if ($file)
			{
				while (!feof($file))
				{
					$word = fgets($file);
					if (!isset($frequency[$author][$word]))
						$frequency[$author][$word] = 0;
					
					$frequency[$author][$word]++;
					$nbWords++;
				}
				fclose($file);
			}
			
			foreach ($frequency[$author] as $tag => $occ)
				$frequency[$author][$tag] = $occ/$nbWords;
		}
	}
	closedir($handle);
	return $frequency;
}
?>
