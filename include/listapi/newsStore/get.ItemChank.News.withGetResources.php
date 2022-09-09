<?php

header('Content-type:text/html');
// header('Content-type:application/json');

	$path = '../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException('arg dataRecord is not found');
	}
		

	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	if(! (isset($dataRecord['left']) && isset($dataRecord['right'])) ){
		throw new ErrorException('args diapazone data is invalide');
	}
		

	include 'config.php';
	
	
	
	//$query = "SELECT  r.id,r.pagetitle as name,r.class_key,r.parent,r.publishedon,r.content,r.introtext FROM modx_site_content as r";
	//$query .= " WHERE parent=$idNews AND publishedby<>0 AND deleted=0 AND publishedon >= ".$dataRecord['left']." AND publishedon <= ".$dataRecord['right'];
	
	

	/* подставим ссылки на рисунки из phpthumbof */
	require_once "$path../config.modx.php";
	// $modx->resource = $modx->getObject('modResource',45);
	
		
	//'where'=>`{"id:=":"156"}`
			
	/* $dataOutput = $modx->runSnippet('getPage',
		array(
			'element'=>'getResources',
			'parents'=>91,
			'includeTVs'=>1,
			'processTVs'=>1,
			'includeContent'=>1,
			'idx'=>1,
			'includeTVList'=>'tv,itemTag,fieldTag,imageField,tagTV',
			'limit'=>5,
			'tpl'=>'itemPostPreview'
		)
	); */
	
	$dataOutput = $modx->runSnippet('resourcesList',
		array(
			'parent'=>91,
			'includeTVs'=>1,
			'processTVs'=>1,
			'includeContent'=>1,
			'idx'=>1,
			'includeTVList'=>'tv,itemTag,fieldTag,imageField,tagTV',
			'limit'=>5,
			'tpl'=>'itemPostPreview'
		)
	);
	
	if(!$dataOutput){
		throw new ErrorException("getPage snippet is not found !!");
	}
			
	
	echo $dataOutput;

}catch(ErrorException $ex){
	// echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}catch(Exception $ex){
	// echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>