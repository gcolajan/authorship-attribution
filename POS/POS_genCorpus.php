#! /usr/bin/php
<?php
require 'lib/POS.php';

$path = '';
$training = '';
$dest = '';
if (count($argv) == 1)
{
	$path = ANALYZE.'/posfreq';
	$training = DATA.'/C50train';
	$dest = ANALYZE.'/frequences.csv';
}
else if (count($argv) == 3)
{
	$path = $argv[1];
	$training = $argv[2];
	$dest = $argv[3];
}
else
	exit("USAGE: ".$argv[0]." <POS_Path> <training_path> <Result_CSV>\n");

// Vérification et annonce
$dir = opendir($path) or die('Directory doenst exist');
echo "Source path used: ".$path."\n";
echo "Training path: ".$training."\n";
echo "Destination file: ".$dest."\n";


// Récupération des fichiers sources
$files = array();
$handle = opendir($path);
while($author = readdir($handle))
	if($author != '.' && $author != '..')
		$files[$author] = $path.'/'.$author;
closedir($handle);


// Écriture du résultat
getCorpusAnalysis($files, $training, $dest);

echo "Done!\n";
?>
