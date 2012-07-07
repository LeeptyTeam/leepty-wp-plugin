
(function(window){
	
	var defaultSettings = {
		url:		'http://share.leepty.com/api/',
		display:	'LeeptyWidget'
	};
	
	LeeptyHelpers.onReady(function(){
		var leeptyClient = new LeeptyClient();
		leeptyClient.displayFeed();
		
		window.leeptyClient = leeptyClient
	});
	
	function LeeptyClient(option){
		var that = this;
		var settings = LeeptyHelpers.extend(defaultSettings, LeeptyHelpers.getModuleConf(that));
		var displayEngine;
		
		init(option);
		
		function init(option){
			
			settings = LeeptyHelpers.extend(settings, option);
			displayEngine = new window[settings.display]();
			
			
		}
		
		function displayFeed(){
			if(typeof settings.pageLink != 'string') throw('LeeptyClient can\'t define the page link.');
			
			var data = {
				link: settings.pageLink
			}
			var feed = getFeedData(data, displayEngine.updateFeed);
		}
		
		function getFeedData(data, success){
			$.ajax({
				type: 'GET',
				url: settings.url,
				data: data,
				dataType: 'jsonp',
				success: success,
				jsonpCallback: 'jsonp'
			});
		}
		
		/* API */
		that.displayFeed = displayFeed;
	}
	
	
	
	window.LeeptyClient = LeeptyClient;
	
})(window);
