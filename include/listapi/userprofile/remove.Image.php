<?php
header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	/* useExam
	
		dataReqNext({file:'listapi/userprofile/remove.Image.php',type:'json'},
			function(responseJson){
				console.log(responseJson);
				
			}
		);
	*/
	
	if(!$resolution){
		//header("HTTP/1.1 401 Unauthorized"); 
		
		header("HTTP/1.1 403 Forbidden");
		exit;
	}

	
	// $result = $db->update("users",array(),array('avatar'=>""),"id=".$user['id']);
	
	/* выбрать имя файла из таблицы images */
	$query = "SELECT i.image,i.path FROM images as i LEFT JOIN images_context as ic ON ic.nimage = i.id WHERE type=2 AND ic.ncontext=".$user['uid'];
	$result = $db->fetchFirst($query, $uid);
	if( is_string($result) && (strpos($result,"error") !== false)){
		throw new Exception("SQL Error in remove.Image.php");
	}
	
	$avatar = $result['image'];
	/* avatar - имя файла который нужно удалить */
	if(!unlink($_SERVER['DOCUMENT_ROOT'].$result['path'].$avatar)){
		throw new Exception("RemoveFile Error in remove.Image.php");
	}
	
	
	$query = "DELETE FROM images_context WHERE type=2 AND ncontext=".$user['uid'];
	$result = $db->query($query, true, true);
	if( is_string($result) && (strpos($result,"error") !== false)){
		throw new Exception("SQL Error in remove.Image.php");
	}
	
	
	
	echo '{"success":1,"description":"аватар очищен","data":"/assets/uploads/avatars/avatar.png"}';
	
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>