<?php

include 'LeeptyAnalyzer.php';

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
	
//	var_dump($i, $word, $dic->search($word));
	
	$index++;
}
$t2 = microtime(true);
$temp = $t2-$t1;

echo 'Nombre de test : '.$test.'<br>';
echo 'Temps : '.$temp.'s<br>';

$text = isset($_POST['t']) ? $_POST['t'] : 
"Lorem ipsum dolor sit amet, consectetur adipiscing : elit... Nam semper lacinia eros, non commodo massa tristique 
sit amet. Cras tempor tincidunt metus id imperdiet. Integer sodales feugiat augue, malesuada aliquet neque
pulvinar eget. Proin pharetra mi a dolor congue at euismod justo consectetur. Phasellus tempus lectus sit 
amet mauris fermentum varius. Integer arcu quam, molestie ut luctus nec, tristique vulputate enim. In hac 
habitasse platea dictumst. Curabitur elit leo, sollicitudin et accumsan a, venenatis vitae odio. Phasellus 
et elit in dui commodo suscipit. Ut porta aliquet neque eget faucibus. Nullam at diam orci, id ornare neque.
Vivamus ac erat mi.";

header("Content-Type: text/html; charset=utf-8");

$purge_exp = "#([^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s]+)|(\s[0-9.,]+\s)#Su";
echo $dic->purge($text);
?>

<form method="POST">
	<textarea name="t"><?php echo $text?></textarea>
	<input type="submit"/>
</form>