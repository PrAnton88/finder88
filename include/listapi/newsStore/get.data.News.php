<?php

header('Content-type:application/json');

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
	
	$query = "SELECT  r.id,r.pagetitle as name,r.class_key,r.parent,r.publishedon,r.content FROM modx_site_content as r";
	
	
	
	$query .= " WHERE parent=$idNews AND publishedby<>0 AND deleted=0 AND publishedon > ".$dataRecord['left']." AND publishedon < ".$dataRecord['right'];
	
	$query .= " ORDER BY publishedon DESC";
	
	
	$result = $db->fetchAssoc($query,$uid);
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	foreach($result as &$item){
		
		$query = "SELECT c.value as listtv FROM modx_site_tmplvar_contentvalues as c WHERE c.contentid = ".$item['id'];
		$resultTmplvars = $db->fetchAssoc($query,$uid);
		
		$listtv = array();
		$itemtv = null;
		
		foreach($resultTmplvars as &$itemTmpl){
			
			if(strpos($itemTmpl['listtv'],'[{') !== false){
				/*
				// echo $itemTmpl['listtv'].'<br />';
				
				$itemtv = json_decode($itemTmpl['listtv'],true);
				$itemtv = $itemtv[0];
				
				// print_r($itemtv);
				
				if(isset($itemtv['imageFieldNext'])){
					$listtv[] = $itemtv['imageFieldNext'];
				}
				if(isset($itemtv['imageField'])){
					$listtv[] = $itemtv['imageField'];
				}
				*/
			}elseif((strpos($itemTmpl['listtv'],'.pdf') === false) && (strpos($itemTmpl['listtv'],'.doc') === false)){
				
				$itemtv = $itemTmpl['listtv'];
			}
		}
		
		$item['imageField'] = $itemtv;
	}
	
	
	// print_r($result);
	
	if(count($result)>0){
		
		$imageFieldMin = null;
		/* подставим ссылки на рисуики из phpthumbof */
		require_once "$path../config.modx.php";
		$modx->resource = $modx->getObject('modResource',45);
	
		
		foreach($result as &$item){
			if($item['imageField'] != ''){
				
				$imageFieldMin = $modx->runSnippet('phpthumbof',
					array(
						'input'=>'/assets/images/'.$item['imageField'],
						'options'=>'&w=100&h=100&zc=1'
					)
				);
				
				$imageFieldMax = $modx->runSnippet('phpthumbof',
					array(
						'input'=>'/assets/images/'.$item['imageField'],
						'options'=>'&w=500&h=500&zc=1'
					)
				);
				
				$item['imageField'] = array('min'=>$imageFieldMin,'max'=>$imageFieldMax);
			}else{
				$item['imageField'] = array('min'=>'','max'=>'');
			}
		}
		
	}
	// print_r($result);
	
	/*
	$result = array(
		array('id'=>1,'name'=>'Июнь 2021'),
		array('id'=>2,'name'=>'Июль 2021'),
		array('id'=>3,'name'=>'Август 2021'),
		array('id'=>4,'name'=>'Сентябрь 2021'),
		array('id'=>5,'name'=>'Октябрь 2021'),
	);
	*/
	
	echo '{"success":1,"listData":'.json_encode($result).'}';


}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>