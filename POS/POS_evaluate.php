#! /usr/bin/php
<?php
require 'lib/POS.php';

if (count($argv) != 4)
	exit("USAGE: ".$argv[0]." <frequency_csv_file> <data_POS_test> <result_csv>\n");

$time_start = microtime(true);
	
$frequencyFile = $argv[1];
$testPath = $argv[2];
$resultFile = $argv[3];

echo "Starting...\n";

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
				exec("./POS_file.php $filePath ".$author." tmp_csv");
				exec("./POS_matching.php ".$frequencyFile." tmp_csv", $res);
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
