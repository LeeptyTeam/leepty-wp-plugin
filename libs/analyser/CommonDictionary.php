<?php

/**
 * Description of CommonDictionary
 *
 * @author Techniv <vpeybernes.pro@gmail.com>
 */
abstract class CommonDictionary {
	
	private $words;
	
	function __construct() {
		$this->words = $this->getWords();
	}

	protected abstract function getWords();
	protected function findWord($word){
		$first = substr($word, 0,1);
		if(!isset($this->words[$first])) return false;
		return in_array($word, $this->words[$first]);
	}


	public function search($word){
		return $this->findWord($word);
	}
	
}
