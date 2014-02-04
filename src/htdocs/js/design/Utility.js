/* global define */
define([], function () {
	'use strict';


	var Utility = {};


	/**
	 * Performs a weighted linear interpolation on "x" producing "y". The weight of
	 * the interpolation is determined by the relative distance between "x" and the
	 * know points.
	 *
	 * @param x0 - The independent value of the first point to interpolate between.
	 * @param y0 - The dependent value of the first point to interpolate between
	 *             such that: f(x0) = y0.
	 * @param x1 - The independent value of the second point to interpolate between.
	 * @param y1 - The dependent  value of the second point to interpolate between
	 *             such that: f(x0) = y0.
	 * @param x  - The independent value for which we want to find a corresponding
	 *             (interpolated) dependent y value such that: f(x) = y.
	 *
	 * @return The interpolated y value such that f(x) = y. For the linear function
	 *         "f" defined by the known points (x0, y0) and (x1, y1).
	 */
	Utility.interpolate = function(x0,y0,x1,y1,x) {
		// We must parse floats on all input values to avoid string concatenation.
		x0 = parseFloat(x0); x1 = parseFloat(x1); x = parseFloat(x);
		y0 = parseFloat(y0); y1 = parseFloat(y1);
		return (y0+((y1-y0)*((x-x0)/(x1-x0))));
	};

	Utility.interpolatePoints = function(center, points, key) {
		var len = points.length;
		if (len === 4) {
			// Bi-linear interpolate results first by longitude, then by latitude
			var val_0,
			    val_1,
			    val,
			    p0 = points[0],
			    p1 = points[1],
			    p2 = points[2],
			    p3 = points[3];
			val_0 = Utility.interpolate(p0.longitude, p0[key], p1.longitude, p1[key], center.longitude);
			val_1 = Utility.interpolate(p2.longitude, p2[key], p3.longitude, p3[key], center.longitude);
			val = Utility.interpolate(p0.latitude, val_0, p2.latitude, val_1, center.latitude);
			return val;
		}

	};

	/**
	 * Formats the input "lat" to display in a friendly, human-readable format.
	 *
	 * @param lat - The decimal latitude value to format.
	 * @return A formatted latitude.
	 */
	Utility.prettyLat = function(lat) {
		var suffix = '&deg;N';
		if (lat < 0) {lat*= -1; suffix = '&deg;S';}
		lat = Math.round(lat * 100000) / 100000;
		return lat + suffix;
	};

	/**
	 * Formats the input "lon" to display a friendly, human-readable format.
	 *
	 * @param lon - The decimal longitude value to format.
	 * @return A formatted longitude.
	 */
	Utility.prettyLon = function(lon) {
		var suffix = '&deg;E';
		if (lon < 0) {lon*= -1; suffix = '&deg;W';}
		lon = Math.round(lon * 100000) / 100000;
		return lon + suffix;
	};

	/**
	 * Formats the input "lon" to display a friendly, human-readable format.
	 *
	 * @param lon - The decimal longitude value to format.
	 * @return A formatted longitude.
	 */
	Utility.prettyLng = function(lon) {
		return this.prettyLon(lon);
	};

	return Utility;
});