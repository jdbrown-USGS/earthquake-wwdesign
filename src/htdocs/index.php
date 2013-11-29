<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'Worldwide Seismic Design Tool';

	$HEAD = '<link rel="stylesheet" href="leaflet/dist/leaflet.css"/>' .
		'<link rel="stylesheet" href="css/index.css"/>';
	$NAVIGATION = true;
	$FOOT = '
		<script src="requirejs/require.js" data-main="js/index.js"></script>
	  <script src="http://localhost:35729/livereload.js?snipver=1"></script>
	';

	include 'template.inc.php';
}
?>
<div style="width: 100%; background-color:#EEE;border:1px dashed #CCC;padding:5px;">
	<p>
		<strong>Status Report</strong>: To date only relatively few datasets
		have been incorporated into this application. See the &ldquo;Underlying
		Datasets&rdquo; section in the introduction for a list, which now
		includes Haiti. More datasets will be added as time and resources
		permit. If you have or know of a dataset you would like us to add,
		please <a href="http://earthquake.usgs.gov/contactus/?to=wwdesignmaps&amp;subject=Worldwide+Design+Maps">email us</a>.
	</p>
</div>

<div id="noscript-application">
	<noscript>
		<div>
			<?php include_once 'inc/controls.inc.php'; ?>
		</div>
	</noscript>
</div>
