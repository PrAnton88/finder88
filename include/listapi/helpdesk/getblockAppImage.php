<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;

try{
	
	/* useExam
		dataReqNext({file:urlServerSide+'helpdesk/getblockAppImage.php',type:'text'},
			function(respHtml){}
		); 
		
		
		let mediaTmp = null;
		if((mediaTmp = fget('actions_'+nrequest)) && (mediaTmp = fgetParent(mediaTmp).querySelector('.media-thumb'))){
		
			dataReqNext({file:urlServerSide+'helpdesk/getblockAppImage.php',type:'text'},
			function(respHtml){
				// 1. превратить в объект, и если есть скрипты - вставить их как скрипты
				// 2. (снова) назначить обработчики событий
				
				
				// let script = selectScriptResponse(respHtml);
				
				Array.from(convertResponseToObject(respHtml)).map(item => {
					
					mediaTmp.appendChild(item);
				});
				
				setHandPhotoPushActionForIconsUI(fgetParent(fget('actions_'+nrequest)),nrequest);
				
			}); 
		}
	*/
	
	$path = '../';
	require_once "$path../start.php";
	

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}


	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	
	
	$smarty->assign("pathAppChunk",$_SERVER['DOCUMENT_ROOT'].'/core/Smarty/chunks');
	
	return $smarty->display('../chunks/tmp.blockAppImage-MultipleLoader.tpl');
	
	
}catch(ErrorException $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}catch(Error $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}