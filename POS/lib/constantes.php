<?php
define('DATA', '../data');
define('ANALYZE', '../analyze');
define('TMP_FILE', 'tmpAnalyze');
$TAGS = array_map("trim", file('data/tags'));

function genPOS($from, $to) {
	exec("cat $from | tree-tagger-english | cut -f 2 > ".$to);
}
