<?php
		$format = param('output_format', 'excel');
		$target_host = "igskcicgascfuse.cr.usgs.gov";
		$target_file = "/gsd_arup_all.cfm";

		$boundary='_content_boundary_separator_';

		// Build the header
		$header = "POST ${target_file} HTTP/1.0\r\n";
		$header .= 'HOST: ' . $_SERVER['SERVER_NAME'] . "\r\n";
		$header .= "Content-Type: multipart/form-data; boundary=${boundary}\r\n";

		$data = ''; // just to initialize the string

		// Add the posted files
		foreach($_FILES as $field=>$fileinfo) {
			$data .= "--${boundary}\r\n";
			$data .= "Content-disposition: form-data; name=\"${field}\"; ";
			$data .= 'filename="' . $fileinfo['name'] . "\"\r\n";
	        $data .= 'Content-Type: ' . $fileinfo['type'] . "\r\n\r\n";
	        //$data .= join('', file($fileinfo['tmp_name'])) . "\r\n";
	        $file_contents = file_get_contents($fileinfo['tmp_name']);
	        $file_contents = str_replace("\r\n", "\n", $file_contents);
	        $file_contents = str_replace("\r", "\n", $file_contents);
	        $file_contents = str_replace("\n", "\r\n", $file_contents);
	        $data .= "${file_contents}\r\n";
	        $data .= "--${boundary}\r\n";
		}

		// Add the form field variable
		foreach($_POST as $name=>$value) {
			$data .= "--${boundary}\r\n";
	                $data .= "Content-Disposition: form-data; name=\"${name}\"\r\n";
	                $data .= "\r\n${value}\r\n";
	                $data .= "--${boundary}\r\n";
		}

		$data .= "--${boundary}--\r\n";

		$header .= 'Content-length: ' . strlen($data) . "\r\n\r\n";
		$connection = fsockopen($target_host, 80);

		fputs($connection, $header . $data);

		$xml_result = '';
	        while(!feof($connection)) $xml_result .= fread($connection, 1024);
	        fclose($connection);

	        // Strip off the headers
	        $xml_result = substr(strstr($xml_result, "\r\n\r\n"), 4);

	        // Parse the result just for a status
	        $startTimer = microtime();
	        $xml_dom = simplexml_load_string($xml_result);
	        $status  = (string) $xml_dom->status['value'];

	        if ($status!=0) {
	                // Output the results
	                $fname = $_FILES['file']['name'];
	                if( ($index = strpos($fname, '.')) ) {
	                        $fname = substr($fname, 0, $index);
	                }
	                $fname .=  '_' . rand(1000,9999);

	                $fp = fopen('output/'.$fname.'_raw.xml', 'w+');
	                fwrite($fp, $xml_result);
	                fclose($fp);

	?>
	        <script type="text/javascript">/*<![CDATA[*/
	                setTimeout("window.top.showResultLinks('<?php print $fname;?>');", 250);
	        /*]]>*/</script>
	<?php
	        } else {
	                $message = (string) $xml_dom->message;
	?>
	        <script type="text/javascript">/*<![CDATA[*/
	                setTimeout("window.top.showError('<?php print $message;?>');", 250);
	        /*]]>*/</script>
	<?php
	        }

	        /** DEBUGGING **/
	        /*
	        $LOG = fopen('output/batch.log', 'w+');
	        fwrite($LOG, "POST: \n" . print_r($_POST, true) . "\n");
	        fwrite($LOG, "FILES: \n" . print_r($_FILES, true) . "\n");
	        fwrite($LOG, "REQUEST: \n" . $header . $data . "\n");
	        fwrite($LOG, "RESPONSE: \n", $xml_result . "\n");
	        fclose($LOG);
	        */
	?>