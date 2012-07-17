<?php
include '../LeeptyAnalyser.php';


$words = new CoefficientedWords();
var_dump($words);

$words->addWords(array('plop','encore-plop'), 2);
$words->addWords(array('plop plop','re-plop', 'plop'), 1);
$words->addWords(array('plop plop plop','encore-plop'), 3);

var_dump($words);

$words2 = new CoefficientedWords;
$words2->addWords(array('test', 'plop'), 5);

$words->merge($words2);

var_dump($words);

var_dump('test', $words->getCoefficient('test'));
var_dump('re-test', $words->getCoefficient('re-test'));

foreach ($words as $key => $value){
	echo $key.' -> '.$value.'<br/>';
}

