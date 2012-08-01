
(function(window){
	
	var defaultSettings = {
		url:		'http://share.leepty.com/api/',
		display:	'LeeptyWidget'
	};
	
	LeeptyHelpers.onReady(function(){
		var leeptyTwitterClient = new LeeptyTwitterClient();
		leeptyTwitterClient.displayFeed();
		
		window.leeptyTwitterClient = leeptyTwitterClient
	});
	
	function LeeptyTwitterClient(option){
		var that = this;
		var settings = LeeptyHelpers.extend(defaultSettings, LeeptyHelpers.getModuleConf(that));
		var displayEngine;
		
		init(option);
		
		function init(option){
			
			settings = LeeptyHelpers.extend(settings, option);
			displayEngine = new window[settings.display]();
			
			
		}
		
		function displayFeed(){
			if(typeof settings.tags != 'object') throw('LeeptyTwitterClient can\'t define the post\'s tags.');
			
			getTweets(settings.tags, function(data){console.log(data)})
//			var feed = getFeedData(data, displayEngine.updateFeed);
		}
		
		function getTweets(keywordArray, callback) {
			//Translate parameter to proper string
			var qryStr = keywordArray.toString();
			qryStr = qryStr.replace(/,/g, "%40");
			//Set URL for query
			var qryUrl = 'http://search.twitter.com/search.json?q='+qryStr+'&callback=?';
			console.log(qryUrl);
  			//Define an array for our objects
  			var data = {items:[]};
  			//Run the query
  			$.getJSON(qryUrl, function(json) {
  				data.items = json.results;
  				callback(data);
  			});
  		};

		/* API */
		that.displayFeed = displayFeed;
	}
	
	
	
	window.LeeptyTwitterClient = LeeptyTwitterClient;
	
})(window);
