<?php
	include_once 'authorized.inc.php';

	$TITLE = 'Worldwide seismic "DesignMaps" Web Application (Beta)';
	$WIDGETS = 'gmaps';

?>
<div sytle="row">
	<div style=".three-of-three">
		<p>
			<strong>Status Report</strong>: To date only relatively few datasets
			have been incorporated into this application. See the &ldquo;Underlying
			Datasets&rdquo; section in the introduction for a list, which now
			includes Haiti. More datasets will be added as time and resources
			permit. If you have or know of a dataset you would like us to add,
			please <a href="http://earthquake.usgs.gov/contactus/?to=wwdesignmaps&amp;subject=Worldwide+Design+Maps">email us</a>.
		</p>
	</div>
	<div style=".one-of-three" id="noscript-application">
		<noscript>
			<div>
				<?php include_once 'inc/controls.inc.php'; ?>
			</div>
		</noscript>
	</div>
</div>
