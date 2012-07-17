<?php
/**
 * The interface of sementic analyser to extract scored tags
 * from posts.
 * @author Techniv <vpeybernes.pro@gmail.com>
 * @package LeeptyAnalyser
 * @version 1.0
 */
interface LeeptyAnalyser {
	
	/**
	 * Set the dictionary used to filter text.
	 * @param CommonDictionary 
	 */
	function setDictionary($dictionary);
	function setMaxWords($number);
	function setTitle($title, $coefficient);
	function setText($text);
	function setCoefficientedWords($words);
	function fireAnalyse();
	
}

/**
 * ClassLoader for LeeptyAnalyser.
 * @author Techniv <vpeybernes.pro@gmail.com>
 * @package LeeptyAnalyser
 * @version 1.0
 */
class LeeptyAnalyserClassLoader {
	
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
new LeeptyAnalyserClassLoader();


class LeeptyAnalyserException extends Exception{
	
}
