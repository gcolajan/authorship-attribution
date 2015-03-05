#! /bin/bash

cd ../../Reuters50_50/C50train/

for author in *; do
	cat $author/*.txt | tree-tagger-english | cut -f 2 | grep [A-Z] > "../frequency/$author"
done

#cat *.txt | tree-tagger-english | cut -f 2 | grep [A-Z] > frequency
