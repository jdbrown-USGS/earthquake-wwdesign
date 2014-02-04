<!--Creates list for non JavaScript users-->
<form method="post" action="inc/dataminer.ajax.php" id="appfrm" >
	<ul class="four column container">
		<li id="location" class="four column container">
			<ul>
				<li class="four column">
					<label for="latitude" class="two column">Latitude</label>
					<span class="four column help">
						Decimal degrees. Negative values for southern latitudes.
					</span>
					<input type="text" id="latitude" name="latitude"
							class="two column"/>
				</li>
				<li class="four column">
					<label for="longitude" class="two column">Longitude</label>
					<span class="four column help">
						Decimal degrees. Negative values for western longitudes.
					</span>
					<input type="text" id="longitude" name="longitude"
							class="two column"/>
				</li>
			</ul>
		</li>
		<li class="four column">
			<input type="submit" id="btn_compute" name="btn_compute" value="Compute" />
		</li>
	</ul>
</form>

