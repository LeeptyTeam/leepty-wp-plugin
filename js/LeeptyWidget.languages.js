
(function(LeeptyHelpers){
	
	var languages = {
		en: {
			relatedPost: 'Related Post',
			oldBrowserMessage: 'You use an obsolete browser. Please, upgrade your browser or download a modern navigator.'
		},

		fr: {
			relatedPost: 'Article Relatif',
			oldBrowserMessage: 'Vous utilisez un navigateur obsolète. S\'il vous plaît, mettez à jour votre navigateur ou télécharger en un plus moderne.'
		}
	}
	
	languages['default'] = languages.en;
	
	
	// Include language in LeeptyWidget
	LeeptyHelpers.onLibReady('LeeptyWidget', function(LeeptyWidget){
		LeeptyWidget.prototype.languages = languages;
	});

})(LeeptyHelpers);
