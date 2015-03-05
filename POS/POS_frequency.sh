#! /bin/bash

cd ../data/C50train/

for author in *; do
	cat $author/*.txt | tree-tagger-english | cut -f 2 > "../../analyze/posfreq/$author"
done

#cat *.txt | tree-tagger-english | cut -f 2 | grep [A-Z] > frequency
