<?php
	include_once 'parse_xml_result.inc.php';
	// Parse the xmL and create the result
	$xml_dom = simplexml_load_String($xml_string);
	foreach($xml_dom->query as $query) {
		print '<p style="border-bottom:2px solid #F00;">';

		$location = $query->location[0];

		print '<pre style="background-color:#EEEEEE;">' . print_r($query, true) . '</pre>';

		foreach($query->result as $result) {
			// Find the (interpolated) Ss and S1
			$ground_motion = interpolate_points($result, $location);
			$r_source = (string) $result['source'];

			printf("%8.5f,%7.5f,%s,%1.6f,%1.6f\n",
				$location['longitude'], $location['latitude'], $r_source,
				$ground_motion['ss'], $ground_motion['s1']
			);
		} // END: foreach(result)
			print '</p>';
	} // END: foreach(query)
?>