<?php
	if (!isset($HEAD)) {
		$HEAD = '';
	}

	$HEAD .= '<link rel="stylesheet" href="/css/template.css"/>';

	$APP_NAME = $_SERVER['APP_NAME'];

	$APP_BASE_DIR = $_SERVER['APP_BASE_DIR'];
	$APP_DATA_DIR = $_SERVER['APP_DATA_DIR'];

	$APP_WEB_DIR = $_SERVER['APP_WEB_DIR'];
	$APP_LIB_DIR = $_SERVER['APP_LIB_DIR'];
	$APP_CNF_DIR = $_SERVER['APP_CNF_DIR'];
	$APP_URL_PATH = $_SERVER['APP_URL_PATH'];
?>
