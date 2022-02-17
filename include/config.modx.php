<?php

	define('MODX_CORE_PATH', 'C:/Users/oto016/singleProjects/modx-info/core/');
	define('MODX_CONFIG_KEY', 'config');
	
	if (!@include_once (MODX_CORE_PATH . "model/modx/modx.class.php")) {
		$errorMessage = 'Site temporarily unavailable';
		@include(MODX_CORE_PATH . 'error/unavailable.include.php');
		header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
		echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
		exit();
	}
	
	ob_start();

	/* Create an instance of the modX class */
	$modx= new modX();
	if (!is_object($modx) || !($modx instanceof modX)) {
		ob_get_level() && @ob_end_flush();
		$errorMessage = '<a href="setup/">MODX not installed. Install now?</a>';
		@include(MODX_CORE_PATH . 'error/unavailable.include.php');
		header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
		echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
		exit();
	}
	
	/* Set the actual start time */
	$modx->startTime= microtime(true);

	/* Initialize the default 'web' context */
	$modx->initialize('web');

	/* execute the request handler */
	if (!MODX_API_MODE) {
		$modx->handleRequest();
	}
?>