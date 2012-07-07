(function(window){
	
	function LeeptyWidget(option, data){

		var that = this;
		var defaultConf = {
			libBasePath: '/libs',
			templateBasePath: '/templates',
			cssBasePath: '/css',
			imgBasePath: '/images',

			template: 'default',
			language: 'default'
		};
		var conf = undefined;

		var $ = window.jQuery;
		var elements = {};


		function construct(){
			// Local jQuery binding to prevent conflict.
			var tplConf = conf.template;

			elements.html = $('html');
			elements.body = $('body');
			elements.head = $('head');
			
			if(tplConf.parent) elements.parent = $(tplConf.parent);
			if(tplConf.brother){
				elements.brother = $(tplConf.brother);
				elements.parent = elements.brother.parent();
			}

			initCSS();

			var contenerTpl = new EJS({url: tplConf.tplBasePath+tplConf.tplContener});
			var contenerHTML = contenerTpl.render(compilConstructData());

			if(elements.brother){
				elements.brother[tplConf.insertMode](contenerHTML);
			}
			else{
				elements.parent[tplConf.insertMode](contenerHTML);
			}
			
			
			elements.contener = $('#'+that.id);
			elements.content = elements.contener.find('.'+tplConf.contentClass)
			elements.itemList = elements.contener.find('.'+tplConf.itemListClass);

			if(typeof tplConf.styleFunc == 'function') tplConf.styleFunc(elements, tplConf);
			bindEvent();
			updateFeed(data);

		}

		function bindEvent(){
			var tplConf = conf.template;

			for(var event in tplConf.eventListener){
				var listener = tplConf.eventListener[event];

				switch(typeof listener){
					case 'function':
						elements.contener.on(
							event, 
							{
								elements: elements,
								tplConf: tplConf
							},
							listener
						);
						break;

					default:
				}
			}
		}

		function compilConstructData(){
			var tplConf = conf.template;
			var data = {
				contenerId: that.id,
				itemListClass : tplConf.itemListClass,
				lang: tplConf.lang
			}
			return data;
		}

		function getFormatedData(data){
			if(data == undefined) return '';
			var tplConf = conf.template;
			var template = new EJS({url: tplConf.tplBasePath+tplConf.tplItems});

			data = tplConf.dataFormater(data, tplConf);

			var compiledData = {
				data: data
			}
			return template.render(compiledData);
		}

		function updateFeed(data){
			var content = getFormatedData(data);
			elements.itemList.append(content);
		}

		/**
		* Initialization before construction.
		*/  
		function init(option, data){

			checkConf();

			conf.template = LeeptyWidget.prototype.templates[conf.template];
			conf.template.tplBasePath = conf.templateBasePath;
			if(conf.template.misc == undefined) conf.template.misc = {};
			that.id = conf.template.contenerId+'-'+that.id;
			LeeptyWidget.prototype.id++;

			var langList = LeeptyWidget.prototype.languages;
			conf.lang = (langList[conf.language] != undefined) ? langList[conf.language] : langList['default'];
			conf.template.lang = conf.lang

			conf.template.cssBasePath = conf.cssBasePath;
			conf.template.imgBasePath = conf.imgBasePath;
			
			construct();

			//initLibs(libConf, construct, data);
		}

		/**
		* Check and import dependencies.
		* @deprecated move on LeeptyHelper
		* @TODO remove initLibs.
		*/
		function initLibs(libs, callback, args){
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
				var path = /^http(s)?:/.test(lib.path) ? lib.path : conf.libBasePath+lib.path;

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

		function initCSS(){
			var cssList = conf.template.css;
			switch (typeof cssList){
				case 'string':
					importCSS(cssList);
					break;
				case 'object':
					for(var i in cssList){
						var css = cssList[i];
						importCSS(css);
					}
					break;
				default:
					return false;
			}
			return true;
		}

		function importCSS(css){
			switch (typeof css){
				case 'string':
					var cssTag = '<link type="text/css" rel="stylesheet" href="'+getCSSUrl(css)+'"/>'
					elements.head.append(cssTag);
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

		function checkConf(){
			if(LeeptyHelpers && LeeptyHelpers.getModuleConf){
				defaultConf = LeeptyHelpers.extend(defaultConf, LeeptyHelpers.getModuleConf(that));
			}
			conf = LeeptyHelpers.extend(defaultConf, option);

			if(typeof conf.widgetBasePath == 'string'){
				for(var key in conf){
					if(/BasePath$/.test(key) && key != 'widgetBasePath'){
						conf[key] = LeeptyHelpers.checkPath(conf.widgetBasePath+conf[key]);
					}
				}
			}

			if (LeeptyWidget.prototype.templates[conf.template] == undefined) conf.template = 'default';
		}


		/* - API - */

		that.updateFeed = updateFeed

		/* Execution */
		init(option,data);
	}
	LeeptyWidget.prototype.id = 1;

	/**
	* Local wording
	*/
	LeeptyWidget.prototype.languages = {
		en: {
			relatedPost: 'Related Post'
		}
	}
	LeeptyWidget.prototype.languages['default'] = LeeptyWidget.prototype.languages.en;
	
	
	window.LeeptyWidget = LeeptyWidget;
	
})(window)
