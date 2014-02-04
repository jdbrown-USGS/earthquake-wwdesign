/* global define */
define([
	'design/GlobalMapView'
], function (
	GlobalMapView
) {
	'use strict';

	var GlobalDesignApplication = function (options) {
		this._el = options.el;
		this._initialize();
	};

	GlobalDesignApplication.prototype._initialize = function () {
		this._el.innerHTML = '<div class="map"></div>';
		this._mapView = new GlobalMapView({
			el: this._el.querySelector('.map')
		});
	};

	return GlobalDesignApplication;
});
