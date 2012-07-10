<?php

/**
 * Description of CommonDictionary
 *
 * @author Techniv <vpeybernes.pro@gmail.com>
 */
abstract class CommonDictionary {
	
	private $words;
	private $symbole = array(
		'base' => array('~','"','#','{','}','(',')','[',']','|','%','*','$',
			'£','¤','§','?',',',';','.',':','/','\\','!','<','>'),
		
		'solo'=> array('&','+','#','@')
	);
	
	public function __construct() {
		$this->symbole['all'] = array_merge($this->symbole['base'], $this->symbole['solo']);
		$this->words = $this->getWords();
	}

	protected abstract function getWords();
	protected function findWord($word){
		$first = substr($word, 0,1);
		if(!isset($this->words[$first])) return false;
		return in_array($word, $this->words[$first]);
	}


	public final function search($word){
		$first = substr($word, 0,1);
		if(in_array($first, $this->symbole['all'])){
			$matchOther = false;
			$l = strlen($word);
			for($i=1; $i < $l; $i++){
				if(!in_array(substr($word, $i,1), $this->symbole['all'])){
					$matchOther = true;
					break;
				}
			}
			if(!$matchOther) return true;
		}
		return $this->findWord($word);
	}
	
	private static $dictionary = array();
	public static function getDictionary($lang){
		$lang = strtolower($lang);
		if(!isset(self::$dictionary[$lang])){
			$className = 'dictionary\\'.$lang;
			$class = new ReflectionClass($className);
			self::$dictionary[$lang] = $class->newInstance();
		}
		
		return self::$dictionary[$lang];
	}
}
