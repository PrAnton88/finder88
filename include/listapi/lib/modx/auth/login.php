<?php

header('Content-type:text/html');

	$path = '../../../';
	require_once "$path../start.php";

try{
	
	/*
		dataReqNext({file:'listapi/modx/auth/login.php',type:'json'},
			console.log
		);
	*/
	
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	$login = "finder88";
	$password = "03cotrue88modxlocal";
	
	/* login_context=mgr&modahsh=&returnUrl=%2Fmanager%2F&username=finder88&password=03cotrue88modxlocal&login=1 */
	
	$dataRecord = array("login_context"=>"mgr","modahsh"=>"","returnUrl"=>"%2Fmanager%2F","login"=>"1");
	
	$dataRecord["username"] = $login;
	$dataRecord["password"] = $password;
	
	
	$postdata = http_build_query(
	/*array(
		"dataRecord"=>json_encode($dataRecord)
	)*/
	$dataRecord
	);
	
	$opts = array(
	  'http'=>array(
		'method'=>"post",
		'General '=>"Request URL: http%3A%2F%2Flocalhost%2Fmanager%2F\r\n".
					"Referrer Policy: strict-origin-when-cross-origin\r\n",
		'header'=>"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9\r\n".
				  "Accept-Encoding: gzip, deflate, br\r\n".
				  "Accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7\r\n".
				  "Cache-Control: max-age=0\r\n".
				  "Connection: keep-alive\r\n".
				  "Content-Length: 105\r\n".
				  "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n".
				  "Cookie: Idea-f8f479f1=85732af5-6741-4a1f-8373-d4e5aa96d86f; _uetvid=6f6d9bf05bff11ec82818911f84a8266; _ga=GA1.1.1398238876.1639638959; __atuvc=189%7C50%2C0%7C1%2C273%7C52%2C136%7C2; _clck=1tubv4x|1|f01|0; LAST_LOCALE=ru; _ga_TE494EK46C=GS1.1.1655274677.398.1.1655277914.0; modxlocalcsessid=8e67091cd44724eb9ea03ec70e9237c9; PHPSESSID=ctoff7u1bieecf2vfsmlflb2qt\r\n".
				  "Host: localhost\r\n".
				  "Origin: http%3A%2F%2Flocalhost\r\n".
				  "Referer: http%3A%2F%2Flocalhost%2Fmanager%2F\r\n".
				  "Sec-Fetch-Dest: document\r\n".
				  "Sec-Fetch-Mode: navigate\r\n".
				  "Sec-Fetch-Site: same-origin\r\n".
				  "Sec-Fetch-User: 	%3F1\r\n".
				  "Upgrade-Insecure-Requests: 1\r\n".
				  "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36\r\n",
		'content' => $postdata,
		'timeout' => 20
	  )
	);
	
	$context = stream_context_create($opts);
	
	// Open the file using the HTTP headers set above
	$file = file_get_contents(
	   'http://localhost/manager/',
		false, 
		$context
	);
	
	
	
	
	echo $file;
	// echo '{"success":1,"listData":'.json_encode($file).'}';

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