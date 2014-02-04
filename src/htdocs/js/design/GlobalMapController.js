/* global define */
define([

	], function () {
	'use strict';

var GlobalMapController = function() {
	this._options = {
		/* Defaults */
		/* The latitude and longitude in UFC_URL are dummy parameters to make
		 * the ColdFusion component work, not meaningful values.
		 */
		//"UFC_URL": "/cfusion/ufc.cfc?method=GetGSDLatLon&latitude=40&longitude=100",
		'UFC_URL': '/cfusion/ufc0111-cutdown.cfc?method=GetUFCData',
		//"UFC_URL": "/cfusion/ufc0111.cfc?method=GetGSDLatLon&latitude=40&longitude=100",
		'SUBREGION_URL': 'feeds/overlays.php'
	};

	this._view = null;
};

//GlobalMapController.prototype = Object.create(Controller.prototype);

GlobalMapController.prototype.initialize = function(options) {
	// Call the parent initialize method
	//Controller.prototype.initialize.call(this, options);

	// -- Instantiate our view -- //
	this._view = new GlobalMapView();

	// -- Initialize our view -- //
	this._view.initialize(this._options);

	// Set the controller for the view to this object
	this._view.setController(this);

	this._loadUFCData(this._options.UFC_URL);

	this._loadSubregionData(this._options.SUBREGION_URL);
};

GlobalMapController.prototype.onNotify = function(notification) {
	var type = notification.type;
	var data = notification.data;

	if (type === 'location-change') {

		if (data.latitude && data.longitude && notification.source !== this) {
			this._view.updateMarker(data.latitude, data.longitude);
		}

	} else if (type === 'design-updated') {
		this._view.setModel(data);
		this._view.redraw();
	}

};



GlobalMapController.prototype._loadUFCData = function(url) {
	Util.Ajax({
		'async': true,
		'dataType': 'xml',
		'cache': false,
		'context': this,
		'success': function(data, status, xhr) {
			this._handleUFCFeed(data, status);
		},
		'error': function(xhr, status, error) {
			alert('Error loading UFC data');
		},
		'type': 'GET',
		'url': url
	});
};

GlobalMapController.prototype._handleUFCFeed = function(d, s) {
	var _view = this._view;

	var datasets = {};

	// Gather the info for the datasets



	$('metainfo dataset', d).each(function(i, dataset) {
		var attr = function (a) {
			return dataset.getAttribute(a);
		};

		datasets[attr('id')] = {
			shorttitle: attr('shorttitle'),
			title: attr('title'),
			desc: attr('desc'),
			url: attr('url')
		};
	});

	var locations = {};

	// Re-normalize all of the data for each location
	$('results result', d).each(function(i, data_point) {
		var attr = function (a) {
			return data_point.getAttribute(a);
		};

		var loc_id = attr('lat') + ',' + attr('lon');

		locations[loc_id] = locations[loc_id] || {
			name: attr('name'),
			lat: attr('lat'),
			lon: attr('lon'),
			data: []
		};
		locations[loc_id].data.push(data_point);
	});

	$.each(locations, function() {
		_view.plotUFCMarker(
			_view.createUFCMarker(datasets, this)
		);
	});
};

GlobalMapController.prototype
._loadSubregionData = function(url) {
	Util.Ajax({
		'async': true,
		'dataType': 'json',
		'cache': false,
		'context': this,
		'success': function(data, status, xhr) {
			this._handleSubregionFeed(data, status);
		},
		'error': function(xhr, status, error) {
			alert('Error loading subregion data.');
		},
		'type': 'GET',
		'url': url
	});
};

GlobalMapController.prototype
._handleSubregionFeed = function(d, s) {
	var OVERLAYS = d.OVERLAYS;
	var num = OVERLAYS.length;

	for (var i = 0; i < num; ++i) {
		this._view.plotSubregionOverlay(
			this._view.createSubregionOverlay(OVERLAYS[i])
		);
	}
};
});