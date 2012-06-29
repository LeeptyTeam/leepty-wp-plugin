

(function(window){
	
	var settings = {
		config: false,
		basePath: '/',
		libBasePath: 'js',
		libs:{
			jQuery:{
				func: 'jQuery',
				path: 'jquery-1.7.2.min.js'
			}
			, EJS:{
				func: 'EJS',
				path: 'ejs-1.0.min.js'
			}
			, LeeptyWidget:{
				func: 'LeeptyWidget',
				path: 'LeeptyWidget.js'
			}
			, LeeptyClient:{
				func: 'LeeptyClient',
				path: 'LeeptyClient.js'
			}
		},
		moduleSettings:{}
	};

	var readyCallback = {};

var LeeptyHelpers = {
	
	config: function(option){
		settings = LeeptyHelpers.extend(settings, option);
		if(typeof settings.basePath == 'string'){
			for(var key in settings){
				if(/BasePath$/.test(key)){
					settings[key] = LeeptyHelpers.checkPath(settings.basePath+'/'+settings[key]+'/');
				}
			}
		}
	}
	
	, checkPath: function(input){
		if(typeof input == 'string'){
			return input.replace(/([^:])\/(\/)+/g, '$1/');
		}
		if(typeof input == 'object'){
			for(var key in input){
				if(/path/i.test(key)){
					input[key] = LeeptyHelpers.checkPath(input[key]);
				}
			}
			return input;
		}
		
		return input;
	}
	
	, getModuleConf: function(module){
		var moduleName = module.constructor.name;
		if(settings.moduleSettings[moduleName]){
			return settings.moduleSettings[moduleName];
		}
		return {};
	}
	
	, extend: function (src, add){
		var result = src.constructor();
		for(var i in src){
			result[i] = src[i];
		}
		for(var i in add){
			result[i] = add[i];
		}
		return result;
	}
	
	, importCSS: function (css){
		switch (typeof css){
			case 'string':
				var cssTag = '<link type="text/css" rel="stylesheet" href="'+getCSSUrl(css)+'"/>'
				$('head').append(cssTag);
				break;
			default:
		}
		
		function getCSSUrl(url){
			if(/^http(s)?:/.test(url)) return url;
			else{
				var baseUrl = conf.template.cssBasePath;
				return baseUrl+url;
			}
		}
	}
	
	/**
	 * Check and import dependencies.
	 */
	, initLibs: function (libs, callback, args){
		if ( typeof libs != 'object' ) return false;
		var libCount = {
			total: 0,
			check: 0
		}
		
		for(var key in libs){
			libCount.total++;
			var lib = libs[key];
			
			if(window[lib.func]) libCount.check++;
			else importLib(lib);
		}
		
		var succesCheck = window.setInterval(function(){
			if(libCount.check >= libCount.total){
				window.clearInterval(succesCheck);
				callback.call(window, args);
			}
		}, 1);
		
		return true;
		
		
		function importLib(lib){
			var path = /^http(s)?:/.test(lib.path) ? lib.path : settings.libBasePath+lib.path;
			console.log(path);
			var head = document.getElementsByTagName('head')[0];
			var importContener = document.createElement('script');
			importContener.type = 'text/javascript';
			importContener.src = path;
			head.appendChild(importContener);
			var interval = window.setInterval(function(){
				if(typeof window[lib.func] == 'function'){
				window.clearInterval(interval);
				libCount.check++;
			}
			},1);
		}
		
	}
	
	, initLeeptyDependency: function(){
		if(settings.config) config({});
		
		LeeptyHelpers.initLibs(settings.libs , method.execReady);
	}
	
	, onReady: function(callback){
		readyCallback[callback] = callback;
	}
};

	var method = {
		execReady: function(){
			for(var key in readyCallback){
				readyCallback[key].call(window);
			}
		}
	}

	window.LeeptyHelpers = LeeptyHelpers;

})(window)
