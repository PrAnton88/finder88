<?php

function notFoundTemplate(&$val,&$name){
	
	echo '== notFoundTemplate '.$val.' == '.$name;
	
}

	define('SMARTY_DIR', MODX_CORE_PATH . 'model/smarty/');
	
	include_once (SMARTY_DIR . "SmartyBC.class.php");
	
	
	
	$smarty = new Smarty;
	
	$smarty->default_template_handler_func = notFoundTemplate;
	
	$smarty->setTemplateDir(MODX_CORE_PATH . 'Smarty/fromapi/');
	// $smarty->setTemplateDir(MODX_CORE_PATH . 'Smarty/');
	
?>