<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = false;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	/*	useExam
		dataReqNext({file:urlServerSide+'lib/get.smarty.pagination.flex.php',args:'dataRecord={"count":5200,"cMax":4,"field":"mop"}',type:'text'},
		function(resul){
			document.body.querySelectorAll('.placePagination').map(item => { item.innerHTML = ''; item.innerHTML = resul });
			console.log(resul);
		});
	*/
	
	if(!$resolution){
		/*if($uid === true){
			
			throw new ErrorException('401: Unauthozised');
		}else{*/
			throw new ErrorException('403: Forbiddeb');
		/*}*/
	}

	/* ради большей скорости не подключаем modx только из за 'MODX_CORE_PATH' */
	define('MODX_CORE_PATH', 'C:/Users/oto016/singleProjects/modx-info/core/');
	//require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	//$modx->smarty = $smarty;
	
	
	$cfield = 15;
	if(count($user) > 0){
		$cfield = $user["mop"];
	}
	
	/* DEFAULT */
	$cMax = 10;
	$mainlink = "";
	$sortly = "&ord=4";
	$p = 1;
	$classtable = "";
	
	if(empty($_POST['dataRecord'])){
		throw new ErrorException('arg dataRecord is empty');
	}
		
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	
	if(empty($dataRecord['count'])){
		throw new ErrorException('arg count is empty');
	}
	
	$count = (int) $dataRecord['count'];
	if($count <= 0){ 
		throw new ErrorException('arg count is invalid');
	}
	
	
	
	if(isset($dataRecord['cMax'])){
		$cMax = (int) $dataRecord['cMax'];
		if($cMax <= 0){ $cMax = 10; }
	}
	
	if(isset($dataRecord['p'])){
		$p = (int) $dataRecord['p'];
		if($p <= 0){ $p = 1; }
	}
	if(isset($dataRecord['mainlink'])){
		$mainlink = $dataRecord['mainlink'];
	}
	if(isset($dataRecord['sortly'])){
		$sortly = $dataRecord['sortly'];
	}
	if(isset($dataRecord['classtable'])){
		$classtable = $dataRecord['classtable'];
	}
	if(isset($dataRecord['field']) && (count($user) > 0)){
		$field = $dataRecord['field'];
		
		if(isset($user[$field])){
			$cfield = $user[$field];
		}else{
			echo '<script>message.print("field user.'.$field.' is not found");</script>';
		}
	}
	
	if($cfield == 0){
		$cfield = 15;
	}
	
	
	/* главные агрументы это $mop и $count */
	$pages = (int) ceil($count/$cfield);
		
		
	$smarty->assign("p",$p);/* текущая страница */
	$smarty->assign("classtable",$classtable);
	$smarty->assign("cMax",$cMax);
	$smarty->assign("pages",$pages);
	$smarty->assign("itemlink","$mainlink$sortly");
	
		
		
	$display = "";
	$display .= $smarty->display('../chunks/tmp.repage.tpl');

	//$display = $smarty->display('oFormtoBroneDevices.tpl');
	//$display .= $smarty->display($htmlform.'.tpl');
	
	//return $display;
	
	echo $display;

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>