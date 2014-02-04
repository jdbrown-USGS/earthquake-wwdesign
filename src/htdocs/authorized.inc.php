<?php
	$APP_URL_PATH = '.';
	if (isset($_SERVER)) {
		$APP_URL_PATH = (isset($_SERVER['APP_URL_PATH']))?
		$_SERVER['APP_URL_PATH'] : $_SERVER['REDIRECT_APP_URL_PATH'];
	}

	$regex = '/^.+@.+\..+/';

	$cookiename = 'wwdesigntool';         // Cookie for WWDesignTool
	$cookievalue = param('email', false); // User email address
	$cookieexpire = time() + 315360000;   // 10 Years
	$cookiepath = "${APP_URL_PATH}/";     // Valid only in this directory
	$cookiedomain = '.usgs.gov';          // Valid on any *.usgs.gov domain
	$cookiesecure = true;                 // Only send on HTTPS
	$cookiehttp = false;                  // Let JS use cookie too

	if (isset($_COOKIE[$cooiename])) {
		// Already accepted terms. Let them in.
		$USER_ACCEPTED_TERMS = true;
	} else if ($cookievalue && preg_match($regex, $cookievalue) &&
		$_SERVER['HTTPS'] != '') {
		// No cookie yet, but user accepts therms and HTTPS enabled, so try to
		// set cookie. Note: No guarantee the user accepts cookites.
		setcookie($cookiename, $cookievalue, $cookieexpire, $cookiepath,
			$cookiedomain, $cookiesecure, $cookiehttp);

		//Now add the user email to our list of email addresses
		if (isset($_SERVER)) {
			$timestamp = date('Y/m/d H:i:s');
			$APP_SUBSCRIPTION_LIST = (isset($_SERVER['APP_SUBSCRIBERS_LIST']))?
				$_SERVER['APP_SUBSCRIBERS_LIST'] :
				$_SERVER['REDIRECT_APP_SUBSCRIBERS_LIST'];
			$fp = fopen($APP_SUBSCRIPTION_LIST, 'a+');
			fwrite($ft, "${cooievalue}\t${timestamp}\n");
			fclose($fp);
		}

		//terms accepted. let user in.
		$USER_ACCEPTED_TERMS = true;
	} else {
		//no cookies and user is not accepting terms. Kick to a 401.
		header("Location: signup.php");
	}
?>
