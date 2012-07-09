<?php

include 'LeeptyAnalyser.php';

$class = 'dictionary\\En';

$class = new ReflectionClass($class);
$dic = $class->newInstance();
/* @var $dic CommonDictionary */

var_dump($dic->search('april'));
