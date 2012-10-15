/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function(window){
	
	
	
	var init = function(jQuery, window){
		
		var $ = jQuery;
		var cst = {
			auto: 'auto'
		}
		var defaultSettings = {
			height: cst.auto 
		};
		
		var methods = {
			init: function(element,options){
				var elements = { 
					contener: element,
					holder: undefined,
					fixbar: undefined,
					dragbar: undefined
				};
				
				var style = {
					overflow: 'hidden',
					paddingRight: '30px'
				};
				if(/[0-9]+px/.test(options.height)){
					console.log(element);
					style.height = options.height;
				}
				
				element.css(style);
				element.children().wrapAll('<div class="LWSHolder" />');
				element.append('<div class="LWSfixbar"><div class="LWSdragbar"></div></div>');
				elements.holder = element.children('.LWSHolder');
				elements.fixbar = element.children('.LWSfixbar');
				elements.dragbar = element.children('.LWSdragbar');
				
				
				elements.contener.css({
					overflow: 'hidden'
				});
				
				elements.fixbar.css({
					position: 'relative',
					top: '0px',
					right: '0px',
					height: style.height,
					width: '20px'
				});
				
				return elements;
			}
			
			, bindEvents: function(elements){
				console.log(elements.contener.height() < elements.holder.height());
				elements.contener.bind('mousewheel', function(e){
					if(elements.contener.height() < elements.holder.height()){
						e.preventDefault();
						var delta = e.originalEvent.wheelDelta;


						var curScroll = elements.holder.css('margin-top');
						console.log(curScroll);
						if(/(\-?\d*)px/.test(curScroll)){
							curScroll = parseInt(RegExp.$1);
						} else {
							curScroll = 0;
						}
						var maxScroll = elements.contener.height() - elements.holder.height();

						var newScroll = delta + curScroll;
						if(delta < 0){
							if(newScroll < maxScroll) delta = delta + maxScroll - newScroll;
						} else {
							if(newScroll > 0) delta = delta - newScroll;
						}

						elements.holder.css('margin-top',(curScroll+delta)+'px');
					}
				});
			}
			
			, barPosition: function(elements){
				elements.contener.css('width');
			}
		}

		function LWScroller(){
				
			if(arguments.length == 0 || (arguments.length == 1 && typeof arguments[0] == 'object')){
				var options = {};
				if(arguments.length == 1){
					options = arguments[0];
				}
				options = $.extend({}, defaultSettings, options);
				var elements = methods.init(this,options);
				methods.bindEvents(elements, methods);
			}
		}
		
		var utils = {
			windowHeight: function(){
				var wh = 0;
				if(window.innerHeight){
					wh = window.innerHeight;
				} else if(document.documentElement.clientHeight && document.documentElement.clientHeight > 0){
					wh = document.documentElement.clientHeight;
				} else if(document.body.clientHeight){
					wh = document.body.clientHeight;
				} else {
					wh = false;
				}
				return wh;
			}
			
		}
		
		$.fn.LWScroller = LWScroller;
		
		$.LWScroller = utils;
		
	}
	
	if(window.jQuery){
		init(window.jQuery,window);
	} 
	else if(window.LeeptyHelpers){
		window.LeeptyHelpers.onLibReady('jQuery', function(jQuery){
			init(jQuery,window);
		});
	}
	
})(window)
