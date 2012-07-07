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

			extendDOMElement();

			if(typeof tplConf.styleFunc == 'function') tplConf.styleFunc(elements, tplConf);
			bindEvent();
			updateFeed(data);

		}
		
		function extendDOMElement(){
			elements.contener.get(0).updateFeed = function(data){
				updateFeed.call(that, data);
			};
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
			conf.language = (conf.language != 'default') ? conf.language : LeeptyHelpers.language();
			
			conf.lang = (langList[conf.language] != undefined) ? langList[conf.language] : langList['default'];
			conf.template.lang = conf.lang

			conf.template.cssBasePath = conf.cssBasePath;
			conf.template.imgBasePath = conf.imgBasePath;
			
			construct();

			//initLibs(libConf, construct, data);
		}

		function initCSS(){
			var cssList = conf.template.css;
			var cssBasePath = conf.cssBasePath;
			switch (typeof cssList){
				case 'string':
					LeeptyHelpers.importCSS(cssList, cssBasePath);
					break;
				case 'object':
					for(var i in cssList){
						var css = cssList[i];
						LeeptyHelpers.importCSS(css, cssBasePath);
					}
					break;
				default:
					return false;
			}
			return true;
		}

		function checkConf(){
			if(LeeptyHelpers && LeeptyHelpers.getModuleConf){
				defaultConf = LeeptyHelpers.extend(defaultConf, LeeptyHelpers.getModuleConf(that));
			}
			conf = LeeptyHelpers.extend(defaultConf, option);
			window.t = that;
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
