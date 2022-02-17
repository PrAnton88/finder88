<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	/* if($resolution){
		$resolution = checkUserLaws('adminOrderDocs');
	} */
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	require_once "$path../config.modx.php";
	// require_once "$path../config.smarty.php";
	// $modx->smarty = $smarty;
	
	
	
	
	/*
	$chunk = $modx->getObject('modChunk',array(
		'name' => 'headerLoginChank'
	));

	if(!$chunk){
		echo 'No headerLoginChank chunk!'; 
	}else{ 
		
		return $chunk->process(array(
			'username' => 'lala name',
			'guest' => 0,
			'mess' => 'lala message'
		));
	}
	*/
	/* 
	$chunk = $modx->getObject('modChunk',array(
        'name' => 'tpl.formauth'
    ));
    
	print_r($chunk);
	
    if(!$chunk){
        return 'No tpl.formauth chunk!';
    }else{
    
        return $chunk->process(array(
            'username' => 'Гость',
            'guest' => 1
        ));
        
    }*/
	/* 
	$getUserAuthData = true;
	
	$outputStart = $modx->runSnippet('start', array('getUserAuthData'=>true));
	$db = $outputStart["db"];
	$uid = $outputStart["uid"];
	
	print_r($outputStart);
	*/
	/*
	$outputStart = $modx->runSnippet('getPage', 
		array(
			'element'=>`getResources`,
			'sortby'=>`{"menuindex":"ASC"}`,
			'limit'=>`5`,
			'tpl'=>`itemNews`,
			'includeContent'=>`1`,
			'includeTVs'=>`1`
		)
	); 

	
	$outputStart = $modx->runSnippet('getPage', 
		array(
			'element'=>'getResources',
			'sortby'=>'{"menuindex":"ASC"}',
			'limit'=>'5',
			'tpl'=>'itemNews',
			'includeContent'=>'1',
			'includeTVs'=>'1'
		)
	);
	
	
	
	$outputStart = $modx->runSnippet('resourcesList', 
		array(
			'parent'=>'-1',
			'resources'=>'40',
			'template'=>'itemFolder'
		)
	);
	
	*/
	// $modx->resource->set('id',114); -
	
	
	/* $outputStart = $modx->runSnippet('getResources', +
		array(
			'parents'=>6,
			'sortby'=>'{"menuindex":"ASC"}',
			'limit'=>5,
			'tpl'=>'itemNews.test.fromapi',
			'includeContent'=>1,
			'includeTVs'=>1,
			'offset'=>0,
			'debug'=>1,
			'parseDocumentSource'=>1
		)
	); */
	
	/*
	$outputStart = $modx->runSnippet('migxcalGetEvents', 
		array(
			'showWeekNumber'=>true,
			'showWeekends'=>true,
			'allDaySlot'=>true,
			'showDialog'=>true
		)
	); */
	
	/* [[!getPage? &element=`getResources` &sortby=`{"menuindex":"ASC"}` &limit=`5` &tpl=`itemNews` &includeContent=`1`  &includeTVs=`1`]]
	 */
	
	/* $outputStart = $modx->runSnippet('getPage', +-
		array(
			'parents'=>6,
			'element'=>'getResources',
			'sortby'=>'{"menuindex":"ASC"}',
			'limit'=>5,
			'tpl'=>'itemNews.test.fromapi',
			'includeContent'=>1,
			'includeTVs'=>1,
			'processTVs'=>'imageField',
			'offset'=>0,
			'debug'=>1,
			'parseDocumentSource'=>1
		)
	); */
	
	$modx->resource = $modx->getObject('modResource',45);
	
	$outputStart = $modx->runSnippet('getResources',
		array(
			'parents'=>6,
			'sortby'=>'{"menuindex":"ASC"}',
			'limit'=>5,
			'tpl'=>'itemNews.test.fromapi',
			'includeContent'=>1,
			'includeTVs'=>1,
			'processTVs'=>'imageField',
			'offset'=>0,
			'debug'=>1,
			'parseDocumentSource'=>1
		)
	);
	
	echo $outputStart;
	
	/* [[!phpthumbof? &input=`[[+tv.imageField]]` &options=`&w=100&h=100&zc=1`]] */
	
	/* ++
	$modx->resource = $modx->getObject('modResource',45);
	$outputStart = $modx->runSnippet('phpthumbof',
		array(
			'input'=>'/assets/images/listBP.PNG',
			'options'=>'&w=100&h=100&zc=1'
		)
	);
	
	echo '<img class="thumb" src="'.$outputStart.'" alt="Photo" />';
	*/ 
	
	
	
	//echo 'I am exist <script>var comptetHi = function(){ alert("comptetHi"); }</script>';
	
	// return $smarty->display('tableAnyPoint.tpl');
	
	
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>