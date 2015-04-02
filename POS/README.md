# Autorship Attribution

## Seconde méthode - Part-of-Speech

### Utilisation

Une machine sous GNU/Linux est recommandée.

- Ouvrir un terminal
- Vérifier que PHP est bien installé `php -v` (version PHP 5.4 minimum)
- Installer TreeTagger : http://www.cis.uni-muenchen.de/~schmid/tools/TreeTagger/
- Se placer dans le dossier `/POS`
- Préparer le corpus
- Exécuter un des modes disponibles
	- POS frequency
	- N-Grams
	- Étude des transition (prototype non fonctionnel)

### Les différents modes

À l'exception de la préparation du corpus, l'ensemble des modes fonctionne selon la même idée :
- ./mode_genCorpus.php dans l'optique de préparer le corpus à l'étude
- ./mode_file.php afin de préparer le texte à l'analyse
- ./mode_matching.php pour connaître le résultat sur un texte précis
- ./mode_evaluate.php pour connaître la performance de ce mode sur l'ensemble du corpus de test


#### PREPARATION DU CORPUS

Étape obligatoire. Le corpus doit être annoté via le tree-tagger. Assurez-vous d'avoir configuré ce logiciel dans votre PATH afin de procéder à cette étape.

Dès lors, exécutez `./POS_frequency.sh` qui est un script bash qui placera dans le répertoire `analyse/posfreq` l'analyse concatannée de tous les auteurs utiles à l'ensemble des modes présentés ci-après.

Assurez-vous que le répertoire `analyse/posfreq` soit existant.

#### POS FREQUENCY

En premier lieu, on doit préparer le corpus pour obtenir les fréquences d'apparition des tags sur chaque auteur. On réalise cette action via la commande suivante : `./POS_genCorpus.php`, qui, sans paramètre, ira chercher les informations où nous venons de les déposer (répertoire posfreq) et posera son résultat dans le fichier `/analyze/frequences.csv`.

Ce mode a recours aux fichiers source afin de créer une métrique concernant le nombre de mots par ligne.

Préparons ensuite un texte à comparer avec la commande suivante : `./POS_file.php ../data/C50test/AlanCrosby/226494newsML.txt nomTest ../analyze/testCrosby.csv`

Par cette commande, je récupère un texte d'Alan Crosby se situant dans le corpus de test, je nomme cet exemplaire "nomTest" et j'exporte le résultat dans un fichier CSV qui nous sert à l'étape suivante.

Je souhaite maintenant connaître le résultat de cet algorithme, j'appelle donc la commande suivante :
`./POS_matching.php ../analyze/frequency.csv ../analyze/testCrosby.csv`

Cette commande me permet de comparer l'intégralité de mon corpus à un texte et ressort, sur la ligne de commande la liste des auteurs les plus pertinents (peut être vide). Dans notre cas, on se retrouve avec ces deux auteurs :
	MarcelMichelson,DarrenSchuettler,,,,,,,,

Si vous souhaitez tester l'intégralité du corpus de test, le script `./POS_evaluate.php` le permet (attention, requiert environ 10 minutes) et prend trois paramètres : le fichier de fréquence (analyze/frequences.csv) ainsi que le chemin vers le corpus de test. Le dernier paramètre concerne le fichier CSV de résultat pour exploitation personnelle.

# N-GRAMS

Cette méthode est exécutable en respectant le même schéma que précédemment. Il faut en premier lieu préparer le corpus à cette exploitation en générant les n-grams selon la longueur désirée.

Première étape, appeler ./ngrams_genCorpus.php qui générera (et écrasera si existant) des fichiers CSV contenant la suite des tags suivi de leur fréquence d'apparition dans le répertoire `analyze/ngrams`. Chaque auteur dispose de son résultat généré sur les textes d'entraînement, l'ensemble est généré en une dizaine de secondes.

Seconde étape, préparer le fichier à comparer selon le même traitement que précédemment via la commande `./ngrams_file.php ../data/C50test/AlanCrosby/226494newsML.txt ngramsTest 2 0.001 ../analyze/resNgrams.csv`

Le premier paramètre concerne le texte à étudier, le deuxième, le nom attribué. Les deux nombres représentent respectivement la longueur des ngrams (doit obligatoirement être égal à la préparation du corpus) et la fréquence d'apparition minimale de considération des ngrams. Le dernier paramètre et le fichier CSV stockant le résultat.

Enfin, nous exploitons notre algorithme de comparaison en appelant la commande suivante :  `./ngrams_matching.php ../analyze/ngrams ../analyze/resNgrams.csv`. Le premier paramètre concerne le répertoire dans lequel est stocké l'étude préparative du corpus et le second paramètre est le résultat de l'étude du fichier texte à comparer. Cette commande nous rend une ligne présentant les cinq auteurs les plus pertinents :
	HeatherScoffield,BernardHickey,JoeOrtiz,BradDorfman,MatthewBunce

Afin d'effectuer une étude sur l'ensemble du corpus, une méthode est mise à disposition. Prévoyez environ 10 à 15 minutes en fonction des paramètres : `./ngrams_evaluate.php ../analyze/ngrams/ ../data/C50test/ ../analyze/resFull.csv 2 0.001`

Les paramètres correspondent respectivement au répertoire de préparation du corpus, au répertoire du corpus de test (brut), le chemin du fichier CSV dans lequel vous souhaitez insérer les résultats et enfin, les paramètres de longueur des ngrams et la limite de considération de la fréquence d'apparition des ngrams.

# ÉTUDE DES TRANSITIONS

Attention ! Ce mode n'est pas fonctionnel et permettent uniquement de constuire une matrice de transition. Un début d'exploitation a été érigée dans le script eigen.py bien que non fonctionnel.

Néanmoins, la création de la matrice est fonctionnelle via l'enchaînement suivant :
	./transition_genCorpus.php

Cette commande prend les fichiers dans `/analyze/posfreq` et créé une matrice par auteur dans `/analyze/transitions`.

La même méthode est disponible pour créer une matrice sur un fichier texte brut :
	./transition_file.php ../data/C50test/AlanCrosby/226494newsML.txt ../analyze/transitionCrosby.csv

Le premier paramètre correspond au fichier à analyser, le second au fichier résultat.

Enfin, un script Python est disponible afin de calculer des eigenvalues des matrices, effectuer des produtis scalaires, etc.
