require.config({
	baseUrl: 'js',
	paths: {
		'leaflet': '/leaflet/dist/leaflet-src',
		'mvc': '/hazdev-webutils/src/mvc',
		'util': '/hazdev-webutils/src/util',
		'tablist': '/hazdev-tablist/src/tablist'
	},
	shim: {
		leaflet: {
			exports: 'L'
		}
	}
});

require([
	'design/GlobalDesignApplication'
], function (
	GlobalDesignApplication
) {
	'use strict';

	// create an application element
	var applicationEl = document.createElement('div');
	applicationEl.id = 'application';

	// replace no script content with application element
	var noscriptEl = document.querySelector('#noscript-application');
	noscriptEl.parentNode.replaceChild(applicationEl, noscriptEl);
	noscriptEl = null;

	//initialize application
	new GlobalDesignApplication({
		el: applicationEl
	});
});
