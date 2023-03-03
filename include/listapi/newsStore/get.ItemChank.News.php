<?php

// header('Content-type:text/html');
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
		
	$lastDiapazone = false;
	if(isset($dataRecord['lastDiapazone']) && isset($dataRecord['lastDiapazone']['left']) && isset($dataRecord['lastDiapazone']['right']) ){
		$lastDiapazone = $dataRecord['lastDiapazone'];
	}
		
	include 'config.php';
	
	$query = "SELECT  r.id,r.pagetitle as name, r.menuindex, r.class_key,r.parent,r.createdon,r.content,r.introtext FROM modx_site_content as r";
	
	
	
	$query .= " WHERE parent=$idNews AND publishedby<>0 AND deleted=0 AND createdon >= ".$dataRecord['left']." AND createdon <= ".$dataRecord['right'];
	
	
	
	
	$result = $db->fetchAssoc($query,$uid);
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	$directiontop = false;
	
	/* убрать всё что принадлежит диапазону lastDiapazone */
	if($lastDiapazone !== false){
		
		if(($dataRecord['left'] <= $lastDiapazone['left']) && ($dataRecord['right'] >= $lastDiapazone['right'])){
			/* расширяем, то не принтуем предыдущее */
			
			/* 86400 секунд - это ровно сутки назад */
			if(($dataRecord['right'] > $lastDiapazone['right']) && (($dataRecord['right'] - $lastDiapazone['right']) >= 86400)){
				$directiontop = true;
				/* о том что новые записи нужно отобразить сперхзу первыми */
			}
			
			
			$k = 0;
			foreach($result as &$item){
				
				if(($item['createdon'] >= $lastDiapazone['left']) && ($item['createdon'] < $lastDiapazone['right'])){
					unset($result[$k]);
				}
				
				$k++;
			}
		}
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
	
	$dataOutput = array();
	
	
	
	if(count($result)>0){
		
		$imageFieldMin = null;
		/* подставим ссылки на рисунки из phpthumbof */
		require_once "$path../config.modx.php";
		$modx->resource = $modx->getObject('modResource',45);
	
		$modx->runSnippet('func');
		
		
		$createdon = null;
		foreach($result as &$item){
			if($item['imageField'] != ''){
				
				$imageFieldMin = $modx->runSnippet('phpthumbof',
					array(
						'input'=>'/assets/images/'.$item['imageField'],
						'options'=>'&w=100&h=100&zc=1'
					)
				);
				
				/* $imageFieldMax = $modx->runSnippet('phpthumbof',
					array(
						'input'=>'/assets/images/'.$item['imageField'],
						'options'=>'&w=500&h=500&zc=1'
					)
				);*/
				
				$imageFieldMax = '/assets/images/'.$item['imageField'];
				
				$item['imageField'] = array('min'=>$imageFieldMin,'max'=>$imageFieldMax);
			}else{
				$item['imageField'] = array('min'=>'','max'=>'');
			}
			
			/* заполнять готовыми чанками $dataOutput[] */
		
			/* {
				.class_key: "modDocument"
				.content: "<p>А вот содержимое ЛАЛАЛАЛА - ЛА</p>"
				.id: "33"
				.imageField: {min: "/assets/components/phpthumbof/cache/card_ru.f7014cca2df93d1aea5aef314643afce45.png", max: "/assets/components/phpthumbof/cache/card_ru.2026beeae7eb1b94ca760586acfc725845.png"}
				.name: "Новость от менеджера"
				.parent: "6"
				.createdon: "14 апреля 2021 10:21:00"
			} */
			
			$unixcreatedon = $item['createdon'];
			$createdon = getDateStr($item['createdon']);
			//$createdon = $createdon['fdatestr'].' '.$createdon['ftimestr'];
			$createdon = $createdon['shortdate'].' '.$createdon['ftimestr'];
			
			/* необходимо переделать в формат 
				2022-05-18 11:50:00 
			*/
			
			
			/* вместо substr исплользуйте mb_substr */
			
			$htmlForm = $modx->runSnippet('itemNews.call-chank',
				array(
					'id'=>$item['id'],
					'menuindex'=>$item['menuindex'],
					'title'=>mb_substr(strip_tags($item['name']),0,55,'UTF-8'),
					'content'=>mb_substr(strip_tags($item['content']),0,205,'UTF-8'),
					'introtext'=>mb_substr(strip_tags($item['introtext']),0,205,'UTF-8'),
					'createdon'=>$createdon,
					'imagefield'=>$item['imageField']
				)
			);
			
			if(!$htmlForm){
				throw new ErrorException("itemNews.call-chank is not found !!");
			}
			
			$dataOutput[] = array(
				'id'=>$item['id'],
				'createdon'=>$unixcreatedon,
				'html'=>$htmlForm
			);
			
			
		}
		
		
		$dataOutput = array_reverse($dataOutput);
		
		// print_r($dataOutput);
		
	}
	
	/*
	foreach($dataOutput as $item){
		
		print_r($item);
	}*/
	
	echo '{"success":1,"directiontop":'.($directiontop?$directiontop:0).',"listData":'.json_encode($dataOutput).'}';
	

}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	//echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}catch(Exception $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	// echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>