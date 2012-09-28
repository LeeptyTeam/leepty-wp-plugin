(function(LeeptyHelpers){
	
	var templates = {
		sidebar: {
			parent:			'body',
			contenerId:		'leeptyWidget',
			contentClass:	'leeptyContent',
			itemListClass:	'leeptyList',
			scrollContener: 'lwScroller',

			insertMode:		'append',

			tplContener:	'/leeptySidebarContainer.ejs',
			tplItems:		'/leeptyItems.ejs',

			css:			'/leeptySidebar.css',

			defaultTumb:	'/file.png',

			dataFormater: function(data, tplConf){
				for(var key in data.posts){
					var item = data.posts[key];
					if(item.tumb == undefined) item.tumb = tplConf.imgBasePath+tplConf.defaultTumb;
				}

				return data;
			},

			styleFunc: function(elements, tplConf){
				var topMarge = parseInt(elements.html.css('margin-top'));
				elements.content.css('margin-top',(topMarge+20)+'px');
				elements.contener.css('height', $(document).height()+'px');
				elements.contener.css('background-color', elements.body.css('background-color'));
				
				var scroller = $('.'+tplConf.scrollContener);
				elements.scroller = scroller;
				
				var heigth = ($.LWScroller.windowHeight() - (20 + 39))+'px';
				
				scroller.LWScroller({
					height: heigth
				});
			},

			eventListener:{
				mouseenter: function(event){
					var elements = event.data.elements;
					var tplConf = event.data.tplConf;
					var width = parseInt(elements.content.css('width'));

					elements.contener.animate({
						width: width+'px'
					},500);
				},
				mouseleave: function(event){
					var elements = event.data.elements;
					var tplConf = event.data.tplConf;
					var width = elements.content.find('.leeptyHeader').css('margin-left');

					elements.contener.animate({
						width: width
					},500);
				}
			}
		},
		
		box: {
			brother:		'.post',
			contenerId:		'leeptyWidget',
			contentClass:	'leeptyContent',
			itemListClass:	'leeptyList',

			insertMode:		'after',

			tplContener:	'/leeptyBoxContainer.ejs',
			tplItems:		'/leeptyItems.ejs',

			css:			'/leeptyBox.css',
			defaultSkin:	'classic',
			skinCssList: {
				classic:	[],
				diary:		['/leeptyBox.diary.css']
			},

			defaultTumb:	'/file.png',
			
			dataFormater: function(data, tplConf){
				for(var key in data.posts){
					var item = data.posts[key];
					if(item.tumb == undefined) item.tumb = tplConf.imgBasePath+tplConf.defaultTumb;
				}

				return data;
			}
		}
	}
	
	templates['default'] = templates.sidebar;
	
	LeeptyHelpers.onLibReady('LeeptyWidget', function(LeeptyWidget){
		LeeptyWidget.prototype.templates = templates;
	});
})(LeeptyHelpers);
