<?php

/**
 * This is the implementation of LeeptyAnalyser.
 *
 * @author Techniv <vpeybernes.pro@gmail.com>
 * @version 1.0.0
 * @package LeeptyAnalyzer
 * 
 * @see LeeptyAnalyzer
 */
class LeeptyAnalyzerImpl implements LeeptyAnalyzer {
	
	/** @var CommonDictionary */
	private $dictionary;
	/** @var CoefficientedWords */
	private $coefficient;
	/** @var string */
	private $title;
	/** @var int */
	private $titleCoefficient;
	/** @var string */
	private $text;

	
	
	public function setCoefficientedWords(CoefficientedWords $words) {
		if(!isset($this->coefficient)) $this->coefficient = $words;
		else{
			$this->coefficient->merge($words);
		}
	}

	public function setDictionary(CommonDictionary $dictionary) {
		$this->dictionary = $dictionary;
	}

	public function setMaxWords($number) {
		if((int)$number <= 0) throw new LeeptyAnalyzerException('Set max return of analyse below 1 have no sense.',22);
		$this->maxWords = (int)$number;
	}

	public function setText($text) {
		$this->text = (string)$text;
	}

	public function setTitle($title, $coefficient = 1) {
		if(!is_int($coefficient)) throw new LeeptyAnalyzerException('The coefficient must be an integer', 24);
		
		$this->title = (string)$title;
		$this->titleCoefficient = $coefficient;
	}

	public function fireAnalyse() {
		if(!isset($this->coefficient)) $this->coefficient = new CoefficientedWords();
		if(!$this->isReady()) throw new LeeptyAnalyzerException('The analyser is not ready to lauch.',21);
		
		$scored_words = $this->analyse($this->text);
		
		if(isset($this->title)){
			$title = $this->analyse($this->title);
			$titleWords = array_keys($title);
			$this->coefficient->addWords($titleWords, $this->titleCoefficient);
			
			$scored_words = $this->scoreMerge($title, $scored_words);
		}
		
		foreach ($this->coefficient as $word => $value) {
			if(isset($scored_words[$word])){
				$scored_words[$word] = $scored_words[$word] * $value;
			}
		}
		
		arsort($scored_words);
		
		return $scored_words;
	}
	
	/**
	 * Check if all conf is set.
	 * @return boolean 
	 */
	public function isReady(){
		if(!isset($this->dictionary)) return false;
		if(!isset($this->coefficient)) return false;
		if(!isset($this->text))	return false;
		return true;
	}

	/**
	 * Analyse the subject.
	 * @param string $subject
	 * @return array the list of word with their score.
	 * @throws LeeptyAnalyzerException 
	 */
	protected function analyse($subject){
		if(!$this->isReady()) throw new LeeptyAnalyzerException('The analyser is not ready to lauch.',21);
		if(!is_string($subject)) throw new LeeptyAnalyzerException('Only strings can be analyzed.', 23);
		
		
		$subject = $this->dictionary->purge($subject);
		$subject = explode(' ', $subject);
		
		$result = array();
		
		foreach ($subject as $word){
			if($this->dictionary->search($word)) continue;
			
			if(!isset($result[$word])) $result[$word] = 1;
			else $result[$word]++;
		}
		
		return $result;
	}
	
	/**
	 * Merge two score arrays.
	 * @param array $score1
	 * @param array $score2
	 * @return array 
	 */
	private function scoreMerge($score1, $score2){
		foreach ($score1 as $key => $value) {
			if(isset($score2[$key])){
				$score2[$key] = $score2[$key] + $value;
			} else {
				$score2[$key] = $value;
			}
		}
		
		return $score2;
	}
}

?>
