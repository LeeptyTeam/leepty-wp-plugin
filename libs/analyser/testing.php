<?php

include 'LeeptyAnalyser.php';

$class = 'dictionary\\en';

$class = new ReflectionClass($class);
//$dic = $class->newInstance();
$dic = CommonDictionary::getDictionary('en');
/* @var $dic CommonDictionary */

var_dump($dic->search('.?.'));

$sample = array(
	'toto', 'constitusion', 'associated', 'station', 'so', 'sierie', 25, '.', '...', '?', '+a'
);

$test = 1000000;

$max = count($sample);
$index = 0;

$t1 = microtime(true);
for($i=0; $i < $test; $i++){
	if($index >= $max) $index = 0;
	
	$word = $sample[$index];
	
	//var_dump($i, $word, $dic->search($word));
	
	$index++;
}
$t2 = microtime(true);
$temp = $t2-$t1;

echo 'Nombre de test : '.$test.'<br>';
echo 'Temps : '.$temp.'s<br>';

