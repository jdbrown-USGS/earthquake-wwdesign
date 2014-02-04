<?php
	$section = '.';
	if (isset($_SERVER)) {
		$section = (isset($_SERVER['APP_URL_PATH'])) ?
			$_SERVER['APP_URL_PATH'] : $_SERVER['REDIRECT_APP_URL_PATH'];
	}

	print navItem("${section}/index.php", 'Introduction');
	print navItem("${section}/application.php", 'Use Application');
	print navItem("${section}/documentation.php", 'Documentation');
	print navItem("http://earthquake.usgs.gov/learn/faq/?categoryID=42",
		'Frequently Asked Questions');

?>
