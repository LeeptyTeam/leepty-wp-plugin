<?php

/**
 * Description of CommonDictionary
 *
 * @author Techniv <vpeybernes.pro@gmail.com>
 */
abstract class CommonDictionary {
	
	private $words;
	
	private static $purge_exp = "#(\s[0-9.,-]+\s)|([^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ]+)#";


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


	public final function search($word, $purged_text = true){
		if (!$purged_text) {
			if(preg_match(CommonDictionary::$purge_exp, $word) == 1) return true;
		}
		return (empty($word) ||$this->findWord($word));
	}
	
	public final function purge($text){
		/* HTML purge */
		$text = strip_tags($text);
		$text = htmlentities($text);
		$text = preg_replace("#&.+;#", ' ', $text);
		
		/* Common purge */
		$text = preg_replace(CommonDictionary::$purge_exp, ' ', $text);
		$text = strtolower($text);
		echo $text;
		return $text;
	}


	private static $dictionary = array();
	public static function getDictionary($lang, $useDefault = false){
		$lang = strtolower($lang);
		if(!isset(self::$dictionary[$lang])){
			try{
				$className = 'dictionary\\'.$lang;
				$class = new ReflectionClass($className);
				self::$dictionary[$lang] = $class->newInstance();
			}
			catch (ReflectionException $e){
				if($useDefault){
					self::$dictionary[$lang] = new dictionary\en();
				} else {
					throw new LeeptyAnalyzerException('Sorry but the "'.$lang.'" is not supported by the LeeptyAnalyser.', 31, $e);
				}
			}
		}
		
		return self::$dictionary[$lang];
	}
}
