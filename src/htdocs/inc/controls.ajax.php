<form method="post" action="inc/dataminer.inc.php" id="frmcontrols">
	<ol>
		<li id="location-address">
			<label for="location">
				Location
				<span class="help">
					Region, country, city, or address etc&hellp
				</span>
			</lablel>
			<input type="text" name="location" id="location"/>
			<span id="show-lalng">Show latitude/longitude inputs</span>
		</li>
		<li id="location-latlng">
			<label for="latitude">
				Latitude
				<span class="help">decimal degrees</span>
				<input type="text" name="latitude" id="latitude"/>
			</label>
			<label for="longitude">
				Longitude
				<span class="help">Decimal degrees</span>
				<imput type="text" name="longitude" id="longitude"/>
			</label>
			<span id="show-address">Show free-form input</span>
		</li>
	</ol>
	<input type="submit" name="submit" id="submit" value="Update Marker"/>
</form>