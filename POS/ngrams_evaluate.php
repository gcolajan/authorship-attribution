#! /usr/bin/php
<?php
require 'lib/ngrams.php';

if (count($argv) != 6)
	exit("USAGE: ".$argv[0]." <data_POS_training> <data_POS_test> <result_csv> <N> <limit>\n");

$time_start = microtime(true);
	
$trainingPath = $argv[1];
$testPath = $argv[2];
$resultFile = $argv[3];
$n = $argv[4];
$limit = $argv[5];

echo "Starting...\n";

exec("./ngrams_genCorpus.php $trainingPath $n $limit");
echo "Corpus generated!\n";


$fp = fopen($resultFile, "w");
$dir = opendir($testPath) or die('Directory doesn\'t exist');
while($author = readdir($dir))
	if($author != '.' && $author != '..')
	{
		$authorDir = opendir($testPath.'/'.$author);
		while ($text = readdir($authorDir))
		{
			if ($text != '.' && $text != '..')
			{
				$filePath = $testPath.'/'.$author.'/'.$text;
				exec("./ngrams_file.php $filePath $text $n $limit tmp_csv");
				exec("./ngrams_matching.php ../analyze/ngrams tmp_csv", $res);
				fwrite($fp, $author.','.$text.','.$res[0]."\n");
				unset($res);
			}
		}
		closedir($authorDir);
		echo "$author evaluated\n";
	}
closedir($dir);
unlink('tmp_csv');
fclose($fp);

$time = round(microtime(true) - $time_start, 3);
echo "Done (".$time."s)!\n";
?>
