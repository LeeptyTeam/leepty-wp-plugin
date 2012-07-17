<?php

/**
 * This is a map betwen words and coefficients.
 *
 * @author Techniv <vpeybernes.pro@gmail.com>
 * @package LeeptyAnalyser
 * @version 1.0.0
 */
class CoefficientedWords implements Iterator{
	
	private $words;
	
	function __construct() {
		$this->words = array();
	}

	
	/**
	 * Add the word(s) in collection with the given coefficient.
	 * If the word(s) is already set and his coefficient upper than the given
	 * coefficient, the coefficient stay the greater.
	 * 
	 * @param string $words The word(s). String or array of string are accept.
	 * @param int $coefficient 
	 */
	function addWords($words, $coefficient){
		if(is_array($words)){
			foreach ($words as $word) {
				$this->addWords($word, $coefficient);
			}
		} else {
			if(!isset($this->words[$words]) || $this->words[$words] < $coefficient){
				$this->words[$words] = $coefficient;
			}
		}
	}
	
	/**
	 * Get the coefficient of word.
	 * 
	 * @param string $word
	 * @return int The coefficient. If the word is'nt set, return 1. 
	 */
	function getCoefficient($word){
		return isset($this->words[$word]) ? $this->words[$word] : 1;
	}
	
	/**
	 * Merge the CoefficientedWords ogject which given into this current
	 * CoefficientedWords.
	 * 
	 * @param CoefficientedWords $words 
	 * @return void
	 */
	function merge(CoefficientedWords $words){
		$words = $words->words;
		foreach ($words as $word => $coef) {
			$this->addWords($word, $coef);
		}
	}
	
	
	//<editor-fold defaultstate="collapsed" desc="Iterator implement">
	
	/*
	 * Iterator implement
	 */
	public function current() {
		return current($this->words);
	}

	public function key() {
		return key($this->words);
	}

	public function next() {
		return next($this->words);
	}

	public function rewind() {
		return reset($this->words);
	}

	public function valid() {
		$key = $this->key();
		return (isset($key) && $key != false);
	}
	
	//</editor-fold>
}

?>
