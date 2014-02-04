/* global define */
define([
	'leaflet',
	'WWCalculator',
	'tablist/TabList',
	'design/Utility',
	'design/GlobalMapController'
], function (
	L,
	WWCalculator,
	TabList,
	Utility,
	GlobalMapController
) {
	'use strict';

	var GlobalMapView = function (options) {
		this._el = options.el;
		this._initialize();
	};
	// initialize map
	GlobalMapView.prototype._initialize = function () {
		this._map = new L.Map(this._el, {
			center: [0.0, 0.0],
			zoom: 1,
			maxZoom: 8,
			minZoom:1,
			worldCopyJump: true,
			layers: new L.TileLayer('http://earthquake.usgs.gov/basemap/tiles/grayscale/{z}/{y}/{x}.jpg')
		});


		/*
		var polygonOptions = {
			color: 'red',
			weight: 2,
			fillColor: '#fo3',
			fillOpacity: 0.1
		};

		var polygonSEAsia = L.polygon([
			[22, 94],
			[6, 94],
			[6, 92],
			[-10, 92],
			[-10, 118],
			[8, 118],
			[8, 108],
			[22, 108]
			], polygonOptions).addTo(this._map);

		var polygonAfghanistan = L.polygon([
			[41, 56],
			[26, 56],
			[26, 79],
			[41, 79]
			], polygonOptions).addTo(this._map);

		var polygonUS = L.polygon([
			[50, -125],
			[24.6, -125],
			[24.6, -65],
			[50, -65]
			], polygonOptions).addTo(this._map);

		var polygonAK = L.polygon([
			[72, -130],
			[50, -130],
			[50, -180],
			[72, -180]
			], polygonOptions).addTo(this._map);

		var polygonHI = L.polygon([
			[23, -154],
			[18, -154],
			[18, -161],
			[23, -161]
			], polygonOptions).addTo(this._map);

		var polygonPR = L.polygon([
			[19, -64.5],
			[17.5, -64.5],
			[17.5, -67.5],
			[19, -67.5]
			], polygonOptions).addTo(this._map);

		var polygonHA = L.polygon([
			[20.15, -71.5],
			[20.15, -74.5],
			[18, -74.5],
			[18, -71.5]
			], polygonOptions).addTo(this._map);
		*/


		// place marker on map
		var marker = L.marker([0.0,0.0], {draggable:'true'}).addTo(this._map);
		var popup = L.popup({
			maxHeight: 150
		});
		popup.setContent('Update location to view data.');
		marker.bindPopup(popup);

		// add new location popup info to marker after it is dragged to
		// a new location.
		var onMarkerMove = function () {
			var calc = new WWCalculator();
			// Pan map on marker move to the new marker location
			this._map.panTo([marker.getLatLng().lat,marker.getLatLng().lng]);
			calc.calculate({
				latitude: marker.getLatLng().lat,
				longitude: marker.getLatLng().lng

			}, function (data) {
				var formatLatLng = '<center>' +Utility.prettyLat(data.latitude) + ' ' +Utility.prettyLng(data.longitude) + '</center>',
			        center = {latitude:data.latitude,
						longitude:data.longitude};
			    if (data.status === 0) {
					popup.setContent(data.error);
					marker.openPopup();
				} else if (data.status === 1) {
					popup.setContent(formatLatLng);
					marker.openPopup();
				} else if (data.status === 2) {
					popup.setContent('TODO');
					marker.openPopup();
				} else if (data.status === 3) {
					popup.setContent('TODO');
				} else {
					var wrapper = document.createElement('div'),
					    headerEl = document.createElement('div'),
							tabEl = document.createElement('div'),
					    tabList = new TabList({el:tabEl}),
					    results = data.results;
					wrapper.appendChild(headerEl);
					wrapper.appendChild(tabEl);
					headerEl.innerHTML = formatLatLng;

					for (var i = 0, len = results.length; i < len; i++) {
						tabList.addTab(_formatResult(results[i], center));
					}
					popup.setContent(wrapper);
					marker.openPopup();
					// delete this later
					console.log(data.results);
				}
			});
		};
		marker.on('dragend', onMarkerMove);

		var _formatResult = function (result, center) {
			var title,
			    content,
			    points = result.points,
			    ss = Utility.interpolatePoints(center, points, 'ss'),
			    s1 = Utility.interpolatePoints(center, points, 's1');

			title = '<abbr title="' + result.description + '">' +
					result.display_tx + '</abbr>';
			content = [];
			content.push('<header>', result.description, '</header>');
			content.push('<dl>',
				'<dt>ss</dt><dd>', ss, '</dd>',
				'<dt>s1</dt><dd>', s1, '</dd>',
				'</dl>');
			content.push('<p>', result.info_tx, '</p>');

			return {title:title, content:content.join('')};
		};

		var latlngForm = document.querySelector('#appfrm'),
				latInput = latlngForm.querySelector('#latitude'),
				lngInput = latlngForm.querySelector('#longitude');

		latlngForm.addEventListener('submit', function (e) {
			var lat = latInput.value,
			    lng = lngInput.value;
			e.preventDefault();
			// TODO: valadate inputs
			marker.setLatLng(L.latLng(lat,lng));
			onMarkerMove.call(marker);
		});
	};
	return GlobalMapView;
});