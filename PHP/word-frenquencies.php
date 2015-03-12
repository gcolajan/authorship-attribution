<?php

// Mode d'utilisation courant
define('TRAINING', 'TRAINING');
define('TESTING', 'TESTING');
define('CLASSIFY', 'CLASSIFY');
define('CURRENT_MODE', CLASSIFY);

// Variables utiles pour le traitement des données
$authorToTest = 'AaronPressman';
$fileToTest = 0;
$criterionThresholdTest = 0.9;
$csvDelemiter = ';';

// Mode d'utilisation courant
define('TRAINING', 'TRAINING');
define('TESTING', 'TESTING');
define('CLASSIFY', 'CLASSIFY');
define('CURRENT_MODE', TRAINING);

// Variables utiles pour le stockage des données
$currentDirectory = dirname(__FILE__);
$rootDirectory = dirname($currentDirectory);
$ds = DIRECTORY_SEPARATOR;
$matrixFileName = 'training-matrix.csv';
$trainingFileName = 'training-full.csv';
$testingFileName = 'testing-'.$authorToTest.'-'.$fileToTest.'.csv';


if(CURRENT_MODE != CLASSIFY) {

	$testingDirectory = $rootDirectory.$ds.'data'.$ds.'C50test';
	$trainingDirectory = $rootDirectory.$ds.'data'.$ds.'C50train';

	$filterShorterThan = 3;
	$stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
	$globalFrequencyDictionary = [];
	$globalFrequencyDictionaryAllAuthors = [];
	$articleFrequencyDictionary = [];
	$articleFrequencyDictionaryAllAuthors = [];
	$WordsCountByAuthors = [];
	$articlesCountByAuthors = [];
	$articleCollectionCount = 0;

	echo 'Reading...'.PHP_EOL;

	// On parcourt tous les auteurs
	if(CURRENT_MODE == TRAINING) {
		$authors = glob($trainingDirectory.$ds.'*', GLOB_ONLYDIR);
	}
	else if(CURRENT_MODE == TESTING) {
		$authors = glob($trainingDirectory.$ds.$authorToTest, GLOB_ONLYDIR);
	}
	foreach($authors as $author) {

		// On parcourt chaque texte de chaque auteur
		if(CURRENT_MODE == TRAINING) {
			$texts = glob($author.$ds.'*.txt');
		}
		else if(CURRENT_MODE == TESTING) {
			$texts = glob($author.$ds.'*.txt');
			$texts = [$texts[$fileToTest]];
		}
		$authorName = basename($author);
		foreach($texts as $text) {

			$articleCollectionCount++;
			$foundWordsInCurrentText = [];

			// Incremente le nombre d'articles écris par l'auteur
			if(isset($articlesCountByAuthors[$authorName])) {
				$articlesCountByAuthors[$authorName]++;
			}
			else {
				$articlesCountByAuthors[$authorName] = 1;
			}

			// On lit chaque texte de chaque auteur
			$textFile = fopen($text, 'r');
			while(($line = fgets($textFile)) !== false) {

				//$words = preg_split('/\s+/', $line);
				preg_match_all('/(\w+)/', $line, $words);
				foreach($words[1] as $word) {

					$word = strtolower($word);

					// Filtre sur la longueur des mots et le type (non-numérique)
					if(strlen($word) >= $filterShorterThan && !ctype_digit($word)) {

						// Incremente le nombre de mots écris par l'auteur
						if(isset($wordsCountByAuthors[$authorName])) {
							$wordsCountByAuthors[$authorName]++;
						}
						else {
							$wordsCountByAuthors[$authorName] = 1;
						}

						// Incremente "global frequency"
						if(isset($globalFrequencyDictionary[$authorName][$word])) {
							$globalFrequencyDictionary[$authorName][$word]++;
						}
						else {
							$globalFrequencyDictionary[$authorName][$word] = 1;
						}

						// Incremente "global frequency" pour tous les auteurs (confondus)
						if(isset($globalFrequencyDictionaryAllAuthors[$word])) {
							$globalFrequencyDictionaryAllAuthors[$word]++;
						}
						else {
							$globalFrequencyDictionaryAllAuthors[$word] = 1;
						}

						// Incremente "article frequency", uniquement si c'est la première fois qu'on trouve le mot
						if(!isset($foundWordsInCurrentText[$word])) {
							if(isset($articleFrequencyDictionary[$authorName][$word])) {
								$articleFrequencyDictionary[$authorName][$word]++;
							}
							else {
								$articleFrequencyDictionary[$authorName][$word] = 1;
							}
						}

						// Incremente "article frequency" pour tous les auteurs (confondus), uniquement si c'est la première fois qu'on trouve le mot
						if(!isset($foundWordsInCurrentText[$word])) {
							if(isset($articleFrequencyDictionaryAllAuthors[$word])) {
								$articleFrequencyDictionaryAllAuthors[$word]++;
							}
							else {
								$articleFrequencyDictionaryAllAuthors[$word] = 1;
							}
						}

						$foundWordsInCurrentText[$word] = true;
					}
				}
			}
			fclose($textFile);
		}
	}

	echo 'Writing...'.PHP_EOL;


	// Filtre les fréquences et les stopwords, et enregistre le résultat dans un fichier csv
	if(CURRENT_MODE == TRAINING) {
		$frenquencyCsv = fopen($currentDirectory.$ds.$trainingFileName, 'wb');
	}
	else if(CURRENT_MODE == TESTING) {
		$frenquencyCsv = fopen($currentDirectory.$ds.$testingFileName, 'wb');
	}
	fputcsv($frenquencyCsv, ['author', 'term', 'termCount', 'totalTerms', 'articleCount', 'totalArticles', 'termPercent', 'inverseDocumentFrequency', 'criterion'], $csvDelemiter);

	// On garde les meilleurs terme de chaque auteur en fonction du critère
	$bestCriterionTerms = [];

	foreach($globalFrequencyDictionary as $author => $words) {

		$bestCriterionTerms[$author] = [];

		foreach($words as $word => $frequency) {

			// On récupère toutes les données dont on a besoin
			$wordCount = $globalFrequencyDictionary[$author][$word]; // Nombre de fois que le mot est compté pour l'auteur courant
			$totalWords = $wordsCountByAuthors[$author]; // Nombre de mots total de l'auteur courant
			$articleCount = $articleFrequencyDictionary[$author][$word]; // Nombre d'articles dans lesquels le mot apparait pour l'auteur courant
			$articlesCountAllAuthors = $articleFrequencyDictionaryAllAuthors[$word]; // Nombre d'articles dans lesquals le mot apparait pour tous les auteurs
			$totalArticles = $articlesCountByAuthors[$author]; // Nombre d'articles total de l'auteur courant
			$totalArticlesAllAuthors = $articleCollectionCount; // Nombre d'articles total de tous les auteurs

			if($wordCount > 2 && !in_array($word, $stopwords)) {
				$termFrequency = $wordCount / $totalWords; // TF = Nombre d'occurences du mot / Nombre de mot total (pour l'auteur courant)
				$termPercent = $termFrequency * 100;
				$inverseDocumentFrequency = log($totalArticlesAllAuthors / $articlesCountAllAuthors); // IDF = Nombre d'articles total / Nombre d'articles dans lesquels le terme apparait
				$criterion = $termPercent * $inverseDocumentFrequency; // Critère de sélection du terme pour notre matrice
				fputcsv($frenquencyCsv, [$author, $word, $wordCount, $totalWords, $articleCount, $totalArticles, round($termPercent, 3), round($inverseDocumentFrequency, 3), round($criterion, 3)], $csvDelemiter);

				if($criterion >= $criterionThresholdTest) {
					$bestCriterionTerms[$author][$word] = $criterion;
				}

			}
		}
	}

	fclose($frenquencyCsv);


	// Creation de la matrice de probabilité d'apparences des termes pour chaque auteur
	if(CURRENT_MODE == TRAINING) {

		echo 'Compute Matrix...'.PHP_EOL;

		$matrixCsv = fopen($currentDirectory.$ds.$matrixFileName, 'wb');
		fputcsv($matrixCsv, ['author', 'term', 'criterion'], $csvDelemiter);

		foreach($bestCriterionTerms as $author => $terms) {
			foreach($terms as $term => $criterion) {
				fputcsv($matrixCsv, [$author, $term, $criterion], $csvDelemiter);
			}
		}

		fclose($matrixCsv);
	}
}
else if(CURRENT_MODE == CLASSIFY) {

	// Recrée la matrice
	$matrix = [];
	$matrixFile = fopen($currentDirectory.$ds.$matrixFileName, 'r');
	while(($data = fgetcsv($matrixFile, 0, $csvDelemiter)) !== false) {

		$author = $data[0];
		$term = $data[1];
		$criterion = $data[2];

		if(!isset($matrix[$author])) {
			$matrix[$author] = [];
		}
		if(!isset($matrix[$author][$term])) {
			$matrix[$author][$term] = $criterion;
		}
	}
	fclose($matrixFile);

	// Essaie de match le test avec un auteur de la matrice
	$testFile = fopen($currentDirectory.$ds.$testingFileName, 'r');
	while(($data = fgetcsv($testFile, 0, $csvDelemiter)) !== false) {
		// TODO
	}
	fclose($testFile);

}
