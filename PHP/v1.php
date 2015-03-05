<?php

$currentDirectory = dirname(__FILE__);
$ds = DIRECTORY_SEPARATOR;
$testingDirectory = $currentDirectory.$ds.'data'.$ds.'C50test';
$trainingDirectory = $currentDirectory.$ds.'data'.$ds.'C50train';

$filterShorterThan = 1;
$stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
$globalFrequencyDictionary = [];
$articleFrequencyDictionary = [];
$WordsCountByAuthors = [];
$articlesCountByAuthors = [];


// On parcourt tous les auteurs
$authors = glob($trainingDirectory.$ds.'*', GLOB_ONLYDIR);
foreach($authors as $author) {

	// On parcourt chaque texte de chaque auteur
	$texts = glob($author.$ds.'*.txt');
	$authorName = basename($author);
	foreach($texts as $text) {

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
				if(strlen($word) > $filterShorterThan && !ctype_digit($word)) {

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

					// Incremente "article frequency", uniquement si c'est la première fois qu'on trouve le mot
					if(!isset($foundWordsInCurrentText[$word])) {
						if(isset($articleFrequencyDictionary[$authorName][$word])) {
							$articleFrequencyDictionary[$authorName][$word]++;
						}
						else {
							$articleFrequencyDictionary[$authorName][$word] = 1;
						}
					}

					$foundWordsInCurrentText[$word] = true;
				}
			}
		}
		fclose($textFile);
	}
}


// Filtre les fréquences et les stopwords, et enregistre le résultat dans un fichier csv
$frenquencyCsv = fopen($currentDirectory.$ds.'data'.$ds.'frequency-train.csv', 'wb');
fputcsv($frenquencyCsv, ['author', 'word', 'wordCount', 'totalWords', 'articleCount', 'totalArticles']);

foreach($globalFrequencyDictionary as $author => $words) {
	foreach($words as $word => $frequency) {
		$wordCount = $globalFrequencyDictionary[$author][$word];
		$totalWords = $wordsCountByAuthors[$author];
		$articleCount = $articleFrequencyDictionary[$author][$word];
		$totalArticles = $articlesCountByAuthors[$author];
		if($wordCount > 2 && !in_array($word, $stopwords)) {
			fputcsv($frenquencyCsv, [$author, $word, $wordCount, $totalWords, $articleCount, $totalArticles]);
		}
	}
}

fclose($frenquencyCsv);
