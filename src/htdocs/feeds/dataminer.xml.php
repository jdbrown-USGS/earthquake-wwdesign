<?php
	$expires = 60 * 60 * 24 * 7;
	include_once 'functions.inc.php';

	if (!function_exists('createResult')) {
		function createResult ($result) {
			$o = array(
				'points'       => array(),
				'description'  => (string) $result['description'],
				'display_tx'   => (string) $result['display_tx'],
				'grid_spacing' => floatval($result['grid_spacing']),
				'info_tx'      => (string) $result['info_tx'],
				'link'         => (string) $result['link'],
				'source'       => (string) $result['source'],
				'status'       => intval($result['status'])

			);
			foreach ($result->point as $point) {
				$gm = $point->ground_motion;
				$o['points'][] = array(
					'latitude'  => floatval($point['latitude']),
					'longitude' => floatval($point['longitude']),
					'ss'        => floatval($gm['ss']),
					's1'        => floatval($gm['s1'])
				);
			}
			return $o;
		}
	}
	$latitude = param('latitude', '10');
	$longitude = param('longitude', '10');
	$url = 'http://geohazards.usgs.gov/cfusion/gsd_arup_all.cfc';
	//returns values from Richard's XML output from database
	$fp=fopen(
			sprintf(
					"${url}?method=GetGSDLatLon&latitude=%s&longitude=%s",
					$latitude,$longitude),
			'r'
		);
	header("Content-Type: application/json");

	header('Expires: ' + gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
	$response = '';
	while(($str=fread($fp, 1024))) {
		$response .= $str;
	// 	print $str;
	}
	$xml = simplexml_load_string($response);


	$status = $xml->status;
	if (intval($status['value']) !== 0) {
		$json = array();
		$json['results'] = array();
		$json['status'] = intval($status['value']);
		$location = $xml->location;
		$json['latitude'] = floatval($location['latitude']);
		$json['longitude'] = floatval($location['longitude']);

		foreach ($xml->result as $result) {
			$json['results'][] = createResult($result);
		}
		print str_replace('\/', '/', json_encode($json));
		// print 'callback(' . str_replace('\/', '/', json_encode($json)) . ');';
  	} else {
		// TODO handle error
		print str_replace('\/', '/', json_encode(array(
			'results' => array(),
			'status' => 0,
			'latitude' => floatval($latitude),
			'longitude' => floatval($longitude),
			'error' => (string) $xml->message
		)));
	}
	fclose($fp);
?>