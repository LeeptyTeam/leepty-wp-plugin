<?php

/**
 * Description of CommonDictionary
 *
 * @author Techniv <vpeybernes.pro@gmail.com>
 */
abstract class CommonDictionary {
	
	private $words;
	
	private static $purge_exp = "#([^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s]+)|(\s[0-9.,]+\s)#Su";


	public function __construct() {
		$this->words = $this->getWords();
	}

	/**
	 * Get the dictionary list of words.
	 * @return array
	 */
	protected abstract function getWords();
	protected function findWord($word){
		$first = substr($word, 0,1);
		if(!isset($this->words[$first])) return false;
		return in_array($word, $this->words[$first]);
	}


	public final function search($word){
		if(preg_match(CommonDictionary::$purge_exp, $word) == 1) return true;
		return $this->findWord($word);
	}
	
	public final function purge($text, $selective = true){
		return strtolower(
				preg_replace(CommonDictionary::$purge_exp, ' ', $text)
			);
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
