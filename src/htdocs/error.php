<?php
	$code = param('code', '404');
	$titles = array(
		'401' => '
			<p class="four-zero-one">
				You are not currently authorized to access this web appication.
				In order to gain access to the application, please visit the <a
				href="signup.php">signup page</a> where you can supply your
				email and accept out <a href="documentation.php#terms">terms and
				conditions</a>. Once you have done this your iwll be allowed to
				access teh web aplication.
			</p>
		',
		'404' => '
			<p class="four-zero-four">The requested page was not found. Use the
			lins on the left to find what you were looking for. If you still
			can not find the page you were looking for, please <a
			href="http://earthquake.usgs.gov/contactus/?to+wwdesignmaps">contact us for
			assistance</a></p>
		'
	);

	$TITLE = (isset($titles[$code]))?titles[$code]:'Error';
	$STYLES = '
		.four-zero-one {
			background-color:#FCC;
			border:2px dashed #F33;
			padding: 8px 8px 8px 78px;
			bacground:#FCC url("images/warning.png") no-repeat 6px 6 px;
		}
		.four-zero-four {
		}
	';
	$CONTACT = 'emartinez';

	include_once $SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php';

	print (isset($messages[$code]))?$messages[$code]:'Error';
?>
