<?php
	
	$sess = null;
	
	if(isset($_SESSION[SSESSID]) and ($_SESSION[SSESSID] != "")){
		$sess = $_SESSION[SSESSID];
		
		// echo " sess = $sess ";
		fwriteLog('Сессия обнаружена');
		
	}elseif(isset($_COOKIE[CSESSID]) and ($_COOKIE[CSESSID] != "")){
		$sess = $_COOKIE[CSESSID];
		/*то выбрать sid по $_COOKIE[CSESSID]*/
		
		fwriteLog('Куки обнаружены');
		// echo " sess = $sess ";
		$query = "SELECT sid FROM sessions WHERE id = '$sess'";
		if($db){
			
			/* может ли такое быть, что бы сессии не было но было куки */
			/* ведь куки и сессии разинициализируются в данном приложении одновременени (при необходимости данного действия) */
			
			$result = $db->fetchFirst($query,true);
			if((is_string($result)) && (strpos($result,"error") !== false)){
				throw new ErrorException("500 SQL Error");
			}
			if(count($result)>0){
				$sess = $result["sid"];
				/* вероятно стоит создать сессию, раз её не было? */
				
				$_SESSION[SSESSID] = $sess;
				fwriteLog('Создана Сесссия по обнаруженым Кукам');
				
			}else{
				/* $sess = false; */
			}
		}
		
		
		
		//else echo " db not found ";
	}else{
		// echo " SSESSID is not found, CSESSID is not found ";
		fwriteLog('Ни Куки ни Сессии не обнаружены');
	}

	// echo " DOCUMENT_ROOT = ".$_SERVER['DOCUMENT_ROOT'];
	// print_r($_COOKIE);
	// print_r($_SESSION);

?>