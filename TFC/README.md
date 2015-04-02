# Autorship Attribution

## Première méthode - Term-frequency criterion

### Utilisation

- Ouvrir un terminal
- Vérifier que PHP est bien installé `php -v` (version PHP 5.4 minimum)
- Se placer dans le dossier `/TFC`
- Exécuter la commande `php word-frequencies.php`

### Les différents modes

#### TRAINING

Ce mode permet de réaliser un entrainement complet sur la collection de données contenu dans le dossier `/data/C50train`

Excuter le script `word-frequencies.php` dans ce mode, créera un fichier `training-full.csv` et `training-matrix.csv` dans le dosser `/PHP`. Le pemier correspond à toutes les données collectées pendant l'entrainement, le second est une version compactée qui ne contient que les données nécéssaire à la classification.

#### TESTING

Ce mode permet de réaliser un test (selon la même méthode que pour l'entrainement) pour chaque texte de chaque auteur sur la collection de données contenu dans le dossier `/data/C50test`.

Exécuter le script `word-frequencies.php` dans ce mode, créera pour chaque texte de chaque auteur, un fichier au format `$NomDeLAuteur-$IndexDuTexte` dans le dossier `/PHP/test`.

#### CLASSIFY

Ce mode permet de de comparer chaque fichier test créé (pendant le mode TESTING) avec le fichier d'entrainement créé (pendant le mode TRAINING) pour tenter de classifier les textes à partir des données collectées.

Exécuter le script `word-frequencies.php` dans ce mode, essaiera d'attribuer un auteur à chaque texte et affichera le résultat en toute fin d'exécution (qui peu prendre quelques minutes).

    2500 tested texts
    1193 well classified (47.72%)
    1300 misclassified
    7 not classified
    Time: 61.74sec

#### Changer de mode

Pour changer de mode, il suffit de changer la valeur de la constante `CURRENT_MODE` (ligne 7 du script).

Pour utiliser le mode TRAINING

    define('CURRENT_MODE', TRAINING);

Pour utiliser le mode TESTING

    define('CURRENT_MODE', TESTING);

Pour utiliser le mode CLASSIFY

    define('CURRENT_MODE', CLASSIFY);
