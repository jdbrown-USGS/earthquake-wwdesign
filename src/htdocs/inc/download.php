<?php
	include_once 'parse_xml_result.inc.php';
	$format = param('format', 'raw');
	$fileid = param('fileid', false);

	$fname = 'output/' . $filedid . '_raw.xml';
	if (!$fileid || !file_exists($fname)) {
		header('Location: /notfound.php'); return;
	}

	$raw_xml_content = file_get_contents($fname);

	print parse_results($raw_xml_content, $format);
?>