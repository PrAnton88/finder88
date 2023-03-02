<?php
header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	/* useExam
	
		dataReqNext({file:'listapi/helpdesk/remove.File.php',type:'json',args:'dataRecord={"nimage":53}'},
			console.log
		);
	*/
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	$admin = ($user["priority"] == 3);
	
	
	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	if(empty($dataRecord['nimage'])){
		throw new Exception("arg nimage is empty (for remove.File)");
	}
	
	$nimage = (int)$dataRecord['nimage'];
	if($nimage <= 0){
		throw new Exception("arg nimage is invalid (for remove.File)");
	}
	
	// $result = $db->update("users",array(),array('avatar'=>""),"id=".$user['id']);
	
	/* выбрать имя файла из таблицы images */
	$query = "SELECT i.image,i.path,ic.ncontext FROM images as i LEFT JOIN images_context as ic ON ic.nimage = i.id WHERE type=1 AND ic.nimage=".$nimage;
	$result = $db->fetchFirst($query, $uid);
	if( is_string($result) && (strpos($result,"error") !== false)){
		throw new Exception("SQL Error (for remove.File)");
	}
	
	if((!is_array($result)) || (count($result) == 0)){
		throw new Exception("Data image is not found (for remove.File)");
	}
	
	$image = $result['image'];
	$pathImage = $result['path'];
	$nrequest = $result['ncontext'];
	
	/* if(!$admin){ */
		/* теперь рассмотреть права пользователя отновительно этой заявки */
		/* выбираем данные по заявке $nrequest */
		require_once $path."lib/query/get.query.ListTickets.php";
		$query = getQueryToGetTickets()." WHERE a.id = ".$nrequest;
		$dataTickets = $db->fetchFirst($query,$uid);
		if(is_string($dataTickets) && (strpos($dataTickets,'error')!== false)){
			throw new ErrorException('SQL Error');
		}
		/* проверяем права авторизованного */
		
		/* являемся ли мы инициатором. а Ответственный - он имеет права админа, а когда он админ - ему можно удалять любые изображения заявок */
		if((!$admin) && ($dataTickets['applicant'] != $user['uid'])){
			header("HTTP/1.1 403 Forbidden");
			exit;
		}
		
		if($dataTickets['hidden'] == 1){
			header("HTTP/1.1 403 Forbidden");
			exit;
		}
		
		
	/* } */
	
	// throw new ErrorException($_SERVER['PHP_SELF']);
	//throw new ErrorException($_SERVER['DOCUMENT_ROOT']);
	
	
	/* теперь узнать, не встречается ли это имя файла в других записях images, и если нет - то удаляем физически это изображение  */
	$query = "SELECT i.image,i.path,ic.ncontext FROM images as i LEFT JOIN images_context as ic ON ic.nimage = i.id WHERE i.image='$image' AND ic.nimage<>".$nimage;
	$result = $db->fetchFirst($query, $uid);
	if( is_string($result) && (strpos($result,"error") !== false)){
		throw new Exception("SQL Error (for remove.File)");
	}
	
	if(count($result) == 0){
		/* это имя файла, который нужно удалить */
		if(!unlink($_SERVER['DOCUMENT_ROOT'].$pathImage.$image)){
			throw new Exception("RemoveFile Error (for remove.File)");
		}
	}
	
	/* type=1 это изображения к заявкам */
	$query = "DELETE FROM images_context WHERE type=1 AND nimage=".$nimage;
	$result = $db->query($query, true, true);
	if( is_string($result) && (strpos($result,"error") !== false)){
		throw new Exception("SQL Error (for remove.File)");
	}
	
	echo '{"success":1,"description":"файл удален","data":"'.$image.'"}';
	
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>