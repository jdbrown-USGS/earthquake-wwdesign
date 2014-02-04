<?php
	        /**
	         * This file defines a function to parse the returned XML given from the
	         * database server and produce a finalized XML format. XML must be
	         * well-formed and conform to the following template.
	         *
	         * <?xml version="1.0" encoding="UTF-8"?>
	         * <batch>
	         *   <query>
	         *     <location latitude="___" longitude="___" />
	         *     <!-- The requested point fell between four bounding grid points -->
	         *     <result display_tx="___" source="___" grid_spacing="___" status="4">
	         *       <point latitude="TOP" longitude="LEFT">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *       <point latitude="TOP" longitude="RIGHT">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *       <point latitude="BOTTOM" longitude="LEFT">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *       <point latitude="BOTTOM" longitude="RIGHT">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *     </result>
	         *     <!-- The requested point fell on a latitude between longitudes. -->
	         *     <result display_tx="___" source="___" grid_spacing="___" status="2">
	         *       <point latitude="LAT" longitude="LEFT">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *       <point latitude="LAT" longitude="RIGHT">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *     </result>
	         *     <!-- The requested point fell on a longitude between latitudes. -->
	         *     <result display_tx="___" source="___" grid_spacing="___" status="2">
	         *       <point latitude="TOP" longitude="LNG">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *       <point latitude="BOTTOM" longitude="LNG">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *     </result>
	         *     <!-- The requested point fell on a grid point in the data set. -->
	         *     <result display_tx="___" source="___" grid_spacing="___" status="1">
	         *       <point latitude="LAT" longitude="LNG">
	         *         <ground_motion ss="___" s1="___"/>
	         *       </point>
	         *     </result>
	         *     <!-- The requested point was not in the result set. -->
	         *     <!-- There is no markup generated for this case. -->
	         *
	         *     <status value="9" />
	         *   </query>
	         *   <query>
	         *     <!-- The requested point does not exist in any data set. -->
	         *     <message>___</message>
	         *     <status value="0" />
	         *   </query>
	         *   <!-- Additional queries may follow. -->
	         * </batch>
	         *
	         * See the file, "GlobalDesignBatch.xsd" for a complete schema.
	         */

	        // Known file formats (must be all lowercase values).
	        $RAW_FORMAT         = "raw";
	        $EXCEL_FORMAT       = "excel";
	        $KML_FORMAT         = "kml";
	        $CSV_FORMAT         = "csv";

	        // Association of known formats to their respective content-types
	        $AVAILABLE_FORMATS  = array(
	                $RAW_FORMAT   => 'application/xml',
	                $EXCEL_FORMAT => 'text/xml',
	                $CSV_FORMAT   => 'text/plain',
	                $KML_FORMAT   => 'application/vnd.google-earth.kml+xml'
	        );

	        $EXTENSIONS = array(
	                $RAW_FORMAT   => 'xml',
	                $EXCEL_FORMAT => 'xls',
	                $CSV_FORMAT   => 'csv',
	                $KML_FORMAT   => 'kml'
	        );

	        //
	        // IMPORTANT: Read and understand the following before modifying this file.
	        //
	        // To add a new "known format", create a *_FORMAT variable above, then add
	        // it with its corresponding content-type to the AVAILABLE_FORMATS
	        // associative array and add it with its corresponding content-disposition
	        // to the EXTENSIONS array. Finally, write a new function called
	        //     parse_<FORMAT>_results($xml_string)
	        // The naming convention and method signature is strictly enforced and output
	        // is undefined if you specify your function inconsistently.
	        //

	        function parse_results($xml_string, $format) {
	                // Pull in the global variables
	                global $AVAILABLE_FORMATS;
	                global $EXTENSIONS;

	                $format = strtolower($format);

	                // Make sure we got a known format request
	                if (!array_key_exists($format, $AVAILABLE_FORMATS)) {
	                        return unknown_output_format();
	                } else {
	                        // Call the appropriate function
	                        $func_name = 'parse_' . $format . '_results';
	                        $result = call_user_func($func_name, $xml_string);

	                        if (!$result) {
	                                return error_output();
	                        } else {
	                                // Send the appropriate header for the requested file format
	                                header("Content-Type: " . $AVAILABLE_FORMATS[$format]);
	                                header('Content-disposition: attachment; ' .
	                                                'filename="GlobalDesignResults.'.$EXTENSIONS[$format].'"');
	                                return $result;
	                        }
	                }
	        }

	        function unknown_output_format() {
	                return "Error: Requested Unknown Output Format";
	        }

	        function error_output() {
	                return "Error: Output Parsing Function Failed";
	        }

	        ///////////////////////////////////////////////////////////////////////////
	        // Below are each of the output parsing functions.
	        ///////////////////////////////////////////////////////////////////////////

	        /**
	         * This method echoes the raw results. Realistically this function need not
	         * exist, but for consistency reasons it does.
	         *
	         * @param $xml_string - The XML to echo.
	         * @return The same $xml_string that was passed as an argument.
	         */
	        function parse_raw_results($xml_string) {
	                return $xml_string;
	        }

	        /**
	         * This method parses the raw results and produces an excel-readable XML
	         * format. That is, the produced output is in reality an XML file, however
	         * the Microsoft Excel application (2003 and newer) are able to interpret
	         * this format and display the results in an Excel file.
	         *
	         * @param $xml_string - The XML to parse.
	         */
	        function parse_excel_results($xml_string) {
	                $sheets = array(); // An associative array of excel sheets.

	                $output = '<?xml version="1.0" encoding="UTF-8"?>';
	                $output .= '<ss:Workbook ' .
	                                'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';

	                // Cell styles
	                $output .= '<ss:Styles>';
	                        $output .= '<ss:Style ss:ID="header">';
	                                $output .= '<ss:Font ss:Bold="1"/>';
	                        $output .= '</ss:Style>';
	                $output .= '</ss:Styles>';

	                // Parse the XML and create the result
	                $xml_dom = simplexml_load_string($xml_string);
	                foreach($xml_dom->query as $query) {
	                        $location = $query->location[0];
	                        foreach($query->result as $result) {
	                                // Find the (interpolated) Ss and S1
	                                $ground_motion = interpolate_points($result, $location);
	                                $r_source = (string) $result['source'];
	                                if (array_key_exists($r_source, $sheets)) {
	                                        $sheet = $sheets[$r_source];
	                                } else {
	                                        // Create a new sheet
                                        $sheet = '<ss:Worksheet ss:Name="'.$r_source.'">';
	                                        // Create the table element
	                                        $sheet .= '<ss:Table>';
	                                        // Create the header row
	                                        $sheet .= '<ss:Column ss:Width="80"/>';
	                                        $sheet .= '<ss:Column ss:Width="80"/>';
	                                        $sheet .= '<ss:Column ss:Width="80"/>';
	                                        $sheet .= '<ss:Column ss:Width="80"/>';
	                                        $sheet .= '<ss:Row ss:StyleID="header">';
	                                                $sheet .= '<ss:Cell>';
	                                                        $sheet .= '<ss:Data ss:Type="String">Longitude</ss:Data>';
	                                                $sheet .= '</ss:Cell>';
	                                                $sheet .= '<ss:Cell>';
	                                                        $sheet .= '<ss:Data ss:Type="String">Latitude</ss:Data>';
	                                                $sheet .= '</ss:Cell>';
	                                                $sheet .= '<ss:Cell>';
                                                        $sheet .= '<ss:Data ss:Type="String">Ss</ss:Data>';
	                                                $sheet .= '</ss:Cell>';
	                                                $sheet .= '<ss:Cell>';
	                                                        $sheet .= '<ss:Data ss:Type="String">S1</ss:Data>';
	                                                $sheet .= '</ss:Cell>';
	                                        $sheet .= '</ss:Row>';
	                                }

	                                // We now have a sheet string to append a row to
	                                $sheet .= '<ss:Row>';
	                                        $sheet .= '<ss:Cell>';
	                                                $sheet .= '<ss:Data ss:Type="Number">';
	                                                        $sheet .= sprintf("%8.5f", $location['longitude']);
	                                                $sheet .= '</ss:Data>';
	                                        $sheet .= '</ss:Cell>';
	                                        $sheet .= '<ss:Cell>';
	                                                $sheet .= '<ss:Data ss:Type="Number">';
	                                                        $sheet .= sprintf("%7.5f", $location['latitude']);
	                                                $sheet .= '</ss:Data>';
	                                        $sheet .= '</ss:Cell>';
	                                        $sheet .= '<ss:Cell>';
	                                                $sheet .= '<ss:Data ss:Type="Number">';
	                                                        $sheet .= sprintf("%0.5f", $ground_motion['ss']);
	                                                $sheet .= '</ss:Data>';
	                                        $sheet .= '</ss:Cell>';
	                                        $sheet .= '<ss:Cell>';
	                                                $sheet .= '<ss:Data ss:Type="Number">';
	                                                        $sheet .= sprintf("%0.5f", $ground_motion['s1']);
	                                                $sheet .= '</ss:Data>';
	                                        $sheet .= '</ss:Cell>';
	                                $sheet .= '</ss:Row>';

	                                // Put the sheet back into our array
	                                $sheets[$r_source] = $sheet;
	                        } // END: foreach(result)

	                } // END: foreach(query)

	                foreach($sheets as $sheet) {
	                        // Append the sheet contents.
	                        $output .= $sheet;
	                        // We still need to close the sheet tags.
	                        $output .= '</ss:Table></ss:Worksheet>';
	                }

	                // We still need to close the workbook tag.
	                $output .= '</ss:Workbook>';

	                return $output;
	        }

	        /**
	         * This method parses the raw results and produces a kml (XML) file that can
	         * be opened in Google Earth.
	         *
	         * @param $xml_string - The XML to parse.
	         * @return A KML file with interpolated results for each data set for each
	         *         point (where the point is in the data set).
	         */
	        function parse_kml_results($xml_string) {
	                $output  = '<?xml version="1.0" encoding="UTF-8"?>';
	                $output .= '<kml xmlns="http://www.opengis.net/kml/2.2">';
	                $output .= '<Document><name>USGS: Global Seismic Design Tool</name>';
	                $output .= '<Style id="batchresult">';
	                        $output .= '<IconStyle>';
	                                $output .= '<color>ffffffff</color>';
	                                $output .= '<Icon><href>';
	                                $output .= server_uri() . '/research/hazmaps/interactive/global' .
	                                                '/images/marker_img.png'; // Use PNG for GEarth
	                                $output .= '</href></Icon>';
	                                $output .= '<scale>1</scale>';
	                                $output .= '<heading>0</heading>';
	                                $output .= '<hotSpot x="0.5" y="1.0" xunits="fraction" yunits="' .
	                                                'fraction"/>';
	                        $output .= '</IconStyle>';
	                $output .= '</Style>';

	                $xml_dom = simplexml_load_string($xml_string);
	      foreach($xml_dom->query as $query) {
	         $location = $query->location[0];

	                        $output .= '<Placemark>';
	                        $output .= '<styleUrl>#batchresult</styleUrl>';
	                        $output .= "<description><![CDATA[\n";
	                        $output .= sprintf("<strong>Location: %8.5f, %7.5f</strong>",
	                                        $location['longitude'], $location['latitude']
	                                );
	                        $output .= '<table cellpadding="0" cellspacing="0" border="0">';
	                        $output .= '<tr><th>Data Source</th><th>S<sub>S</sub></th>';
	                        $output .= '<th>S<sub>1</sub></th></tr>';

	         foreach($query->result as $result) {
	            // Find the (interpolated) Ss and S1
	            $ground_motion = interpolate_points($result, $location);
  		        $r_source = (string) $result['source'];
	                                $output .= sprintf(
	                                        "<tr><td>%s</td><td>%1.6f</td><td>%1.6f</td></tr>",
	                                        $r_source, $ground_motion['ss'], $ground_motion['s1']
	                                );
	         } // END: foreach(result)
	                        $output .= "</table>\n";
	                        $output .= ']]></description>';
	                        $output .= sprintf("<Point><coordinates>%s,%s,0</coordinates></Point>",
	                                        $location['longitude'], $location['latitude']
	                                );
	                        $output .= '</Placemark>';

	      } // END: foreach(query)

	                $output .= '</Document></kml>';
	                return $output;
	        }

	        /**
	         * This method parses the raw results and produces a CSV (text) file that can
	         * be opened with any standard text editor, most spreadsheet editors, and is
	         * suitable for additional post-processing.
	         *
	         * @param $xml_string - The XML to parse.
	         * @return A CSV file with interpolated results for each data set for each
	         *         point (where the point is in the data set).
	         */
	        function parse_csv_results($xml_string) {
	                $output = "Longitude,Latitude,Data Source,Ss,S1\n";

	                // Parse the XML and create the result
	      $xml_dom = simplexml_load_string($xml_string);
	      foreach($xml_dom->query as $query) {
	         $location = $query->location[0];
	         foreach($query->result as $result) {
	            // Find the (interpolated) Ss and S1
	            $ground_motion = interpolate_points($result, $location);
	            $r_source = (string) $result['source'];
	                                $output .= sprintf("%8.5f,%7.5f,%s,%1.6f,%1.6f\n",
	                                                $location['longitude'], $location['latitude'], $r_source,
	                                                $ground_motion['ss'], $ground_motion['s1']
	                                        );
	         } // END: foreach(result)
	      } // END: foreach(query)


	                return $output;
	        }

	        ///////////////////////////////////////////////////////////////////////////
	        // Below are common utility functions. Any custom parsing function to
	        // output a new format should be defined above this section.
         ///////////////////////////////////////////////////////////////////////////

	        /**
	         * This function accepts an XML "result" object that contains a number of
	         * XML "points". The result make contain 1, 2, or 4 points for possible
	         * interpolation.
	         *
	         * If the result contains 1 point, then the ground motion result for that
	         * specific point are simply returned.
	         *
	         * If the result contains 2 points, then a linear interpolation is performed
	         * on the ground motion values and the interpolated result is returned.
	         *
	         * If the result contains 4 points, then a bilinear interpolation is
	         * performed first with respect to longitude, and then with respect to
	         * latitude. The single interpolated ground motion result is returned.
	         *
	         * @param $result - An XML result object. This object contains four
	         *                  attributes and 1, 2, or 4 "point"s as children. The
	         *                  attributes are as follows:
	         *
	         *                    source       : The source ID of this data set.
	         *                    display_tx   : The display text for this data set.
	         *                    grid_spacing : The grid spacing of the underlying data.
	         *                    status       : The number of contained "point"s.
	         *
	         * @param $location - An XML location object. This object contains two
	         *                    attributes, namely a latitude and longitude value.
	         *                    Negative values represent southern or western
	         *                    coordinates (respectively) and vice-versa.
	         *
	         * @return The (possibly/likely) interpolated result as an associative array
	         *         with:  ground motion key => ground motion value
	         *
	         *         For Example: array('ss'=>'0.0127', 's1'=>'0.22453')
	         *         Note: Above example values are not known to be actual Ss or S1
	         *               values for any specific location.
	         */
	        function interpolate_points($result, $location) {
	                $num_points  = (int) $result['status'];
	                $request_lat = (float) $location['latitude'];
	                $request_lng = (float) $location['longitude'];
	                $ss = PHP_INT_MAX; $s1 = PHP_INT_MAX;

	                if ($num_points == 1) {
	                        $point = $result->point[0];
	                        $gm    = $point->ground_motion[0];
	                        $ss    = $gm['ss'];
	                        $s1    = $gm['s1'];
	                } else if ($num_points == 2) {
	                        $point0 = $result->point[0];
	                        $point1 = $result->point[1];

	                        if (doubleval($point0['latitude']) == doubleval($request_lat) ) {
	                                // Latitudes match, interpolate longitudes.
	                                $ss = interpolate($point0['longitude'],$point0->ground_motion['ss'],
	                                                $point1['longitude'], $point1->ground_motion['ss'],
	                                                $request_lng
	                                        );
	                                $s1 = interpolate($point0['longitude'],$point0->ground_motion['s1'],
	                                                $point1['longitude'], $point1->ground_motion['s1'],
	                                                $request_lng
	                                        );
	                        } else if (doubleval($point0['longitude']) == doubleval($request_lng) ) {
	                                // Longitudes match, interpolate latitudes.
	                                $ss = interpolate($point0['latitude'],$point0->ground_motion['ss'],
	                                                $point1['latitude'], $point1->ground_motion['ss'],
	                                                $request_lat
	                                        );
	                                $s1 = interpolate($point0['latitude'],$point0->ground_motion['s1'],
	                                                $point1['latitude'], $point1->ground_motion['s1'],
	                                                $request_lat
	                                        );
	                        } else { // Error case
	                                $ss = '-99999999'; $s1 = '-99999999';
	                        }
	                } else if ($num_points == 4) {
	                        // Perform a bilinear interpolation
	                        $point0 = $result->point[0]; $point1 = $result->point[1];
	                        $point2 = $result->point[2]; $point3 = $result->point[3];

	                        // Interpolate first two points with respect to longitude
	                        $ss_0 = interpolate($point0['longitude'],$point0->ground_motion['ss'],
	                                                $point1['longitude'], $point1->ground_motion['ss'],
	                                                $request_lng
	                                        );
	                        $ss_1 = interpolate($point2['longitude'],$point2->ground_motion['ss'],
	                                                $point3['longitude'], $point3->ground_motion['ss'],
	                                                $request_lng
	                                        );
	                        // Interpolate the second two points with respect to longitude
	                        $s1_0 = interpolate($point0['longitude'],$point0->ground_motion['s1'],
	                                                $point1['longitude'], $point1->ground_motion['s1'],
	                                                $request_lng
	                                        );
                         $s1_1 = interpolate($point2['longitude'],$point2->ground_motion['s1'],
	                                                $point3['longitude'], $point3->ground_motion['s1'],
	                                                $request_lng
	                                        );

	                        // Interpolate the intermediate values with respect to latitude
	                        $ss = interpolate($point0['latitude'], $ss_0, $point2['latitude'],
	                                $ss_1, $location['latitude']);
	                        $s1 = interpolate($point0['latitude'], $s1_0, $point2['latitude'],
	                                $s1_1, $location['latitude']);
	                }  else {
	                        // Error case

	                }

	                // Return the result
	                return array('ss'=>$ss,'s1'=>$s1,'n'=>$num_points);
	        }

	        function interpolate($x0, $y0, $x1, $y1, $x) {
	                $x0 = doubleval($x0); $x1 = doubleval($x1);
	                $y0 = doubleval($y0); $y1 = doubleval($y1);
	                $x  = doubleval($x);
	                return ($y0+(($y1-$y0)*(($x-$x0)/($x1-$x0))));
	        }
	?>