<?php
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("request data is invalid. dataRecord is not found");
	}

	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	
	$dataRecord = str_replace('\'','/"',$dataRecord);
	$dataRecord = str_replace('\\"','/"',$dataRecord);
	$dataRecord = str_replace('\n','/n',$dataRecord);
	$dataRecord = str_replace('\t','/tab',$dataRecord);
	$dataRecord = str_replace('\\','/',$dataRecord);
	$dataRecord = str_replace('/n','\n',$dataRecord);
	$dataRecord = str_replace('/tab','\t',$dataRecord);
	$dataRecord = str_replace('/"','\\"',$dataRecord);
	
	/* при вставке в html мы должны заменять tab на ряд пробелов */
	// $dataRecord = str_replace('\t','&nbsp;&nbsp;&nbsp;',$dataRecord);
	
	$str = $dataRecord;
	// echo $dataRecord;
	
	$dataRecord = json_decode($dataRecord,true);
	
?>