<?php
header('Content-type:application/json');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	/* $getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php"; */

try{


	/* useExam 
	
	
		dataReqNext({file:'listapi/lib/listusers/get.desc.php',type:'json'},
			console.log
		);

	*/


	$args = array("sintphone"=>14,"limit"=>20,"page"=>1);
	$description = "По поводу аргументов 'page' и 'limit'. Если мы укажем 'page', то 'limit' будет взят из профиля авторизованного. Если укажем 'limit' то это переопределит limit авторизованного. ";
	$description .= "Если не укажем ни page ни limit то безлимитная выборка. ";

	// $description = str_replace("'","\'",$description);

	echo '{"success":1,"dataRecord":'.json_encode($args).',"description":"'.$description.'"}';
	
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>