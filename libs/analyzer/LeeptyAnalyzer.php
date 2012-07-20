<?php
/**
 * The interface of text analyzer to extract scored tags
 * from posts.
 * @author Techniv <vpeybernes.pro@gmail.com>
 * @package LeeptyAnalyzer
 * @version 1.0
 */
interface LeeptyAnalyzer {
	
	/**
	 * Set the dictionary used to filter text.
	 * @param CommonDictionary 
	 */
	function setDictionary(CommonDictionary $dictionary);
	
	/**
	 * Set the title of text with the coefficient of this.
	 * @param string $title
	 * @param int $coefficient
	 */
	function setTitle($title, $coefficient);
	
	/**
	 * Set the text to analyse.
	 * @param string $text 
	 */
	function setText($text);
	function setCoefficientedWords(CoefficientedWords $words);
	function fireAnalyse();
	
}

/**
 * ClassLoader for LeeptyAnalyzer.
 * @author Techniv <vpeybernes.pro@gmail.com>
 * @package LeeptyAnalyzer
 * @version 1.0
 */
class LeeptyAnalyzerClassLoader {
	
	private $path;
	
	
	function __construct(){
		
		$this->path = realpath(dirname(__FILE__));
		
		
		spl_autoload_register(array($this,'loadClass'));
	}
	
	function loadClass($name){
		
		$name = preg_replace('#\\\#', '/', $name);
		
		$filePath = realpath($this->path.'/'.$name.'.php');
		if(file_exists($filePath)) include_once $filePath;
	}
}
new LeeptyAnalyzerClassLoader();


class LeeptyAnalyzerException extends Exception{
	
}
