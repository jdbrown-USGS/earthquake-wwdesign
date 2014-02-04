/* global define */
define([
	'util/Xhr'
], function (
	Xhr
) {
	'use strict';

	var WWCalculator = function () {
		// Function implementation
		// this._options = {
		// 	"datminer": "feeds/dataminer.xml.php"
		// };
	};

	WWCalculator.prototype.calculate = function (request, callback) {
		Xhr.ajax({
			url: 'feeds/dataminer.xml.php',
			data: request,
			success: function (data) {
				console.log(data);
				callback(data);
			},
			error: function (data) {
				console.log(data);
				callback(data);
			}
		});
	};

	return WWCalculator;
});