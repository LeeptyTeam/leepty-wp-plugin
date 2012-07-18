<?php
include '../LeeptyAnalyzer.php';

class LeeptyAnalyzerImplTest extends LeeptyAnalyzerImpl{
	
	private $analyzer;
	
	function __construct() {
		$title = "Twitter Search API";
		$text = "The Twitter Search API is a dedicated API for running searches against the real-time index of recent Tweets. There are a number of important things to know before using the Search API which are explained below.
Limitations
The Search API is not complete index of all Tweets, but instead an index of recent Tweets. At the moment that index includes between 6-9 days of Tweets.
You cannot use the Search API to find Tweets older than about a week.
Queries can be limited due to complexity. If this happens the Search API will respond with the error:
Search does not support authentication meaning all queries are made anonymously.
Search is focused in relevance and not completeness. This means that some Tweets and users may be missing from search results. If you want to match for completeness you should consider using the Streaming API instead.
The near operator cannot be used by the Search API. Instead you should use the geocode parameter.
Queries are limited to 1,000 characters in length, including any operators.
When performing geo-based searches with a radius, only 1,000 distinct subregions will be considered when evaluating the query.
iPad2";
		
		$analyzer = new LeeptyAnalyzerImpl;
		$analyzer->setDictionary(CommonDictionary::getDictionary('en'));
		$analyzer->setMaxWords(5);
		$analyzer->setCoefficientedWords(new CoefficientedWords());
		$analyzer->setText($text);
		$analyzer->setTitle($title);
		$this->analyzer = $analyzer;
		
		var_dump($this->analyse($text));
	}
	
	function analyse($subject) {
		return $this->analyzer->fireAnalyse();
	}
}

new LeeptyAnalyzerImplTest;
