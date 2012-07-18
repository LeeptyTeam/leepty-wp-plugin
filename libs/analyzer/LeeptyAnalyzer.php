<?php
/**
 * The interface of sementic analyzer to extract scored tags
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
	function setTitle($title, $coefficient);
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
