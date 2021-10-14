<?php
	
	/* getUserData(&$db,&$uid,$where) */
	/* getUserDataWithoutOptions(&$db,&$uid,$where) */
	require_once "queryUserData.php";
	// require_once(PATH."queryUserData.php");
	
	try{
		session_start();
		ini_set('display_errors', 'On');
		
		/*
		if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE") !== false) {
		  header("Cache-Control: no-cache");
		  header("Pragma: no-cache");
		}
		*/
		
		$spath = '';
		if(isset($path)){
			$spath = $path;
		}
		
		$resolution = true;
		$user = array();
		
		define('PATH',$spath.'../../');
		
		
		require_once("error.reporting.php");
		require_once("queryUserData.php");
		
		// trigger_error("This event WILL fire", E_WARNING);
		// trigger_error("This event WILL fire", E_USER_NOTICE);
		// throw new Error("test");
		
		
		/* схема 1 - обязательно наличие проинициализированнон переменной selector */
		require_once(PATH."config/connDB.php");
		/*get $db*/

		
		$uid = true;
		/* схема 3 и 4 - если проинициализированы переменные 3)getUserAuthData >> и 4)sessAccesser */
		if(isset($getUserAuthData)){
			require_once(PATH."config/getSess.php");
			/*get $sess*/
			
			if($sess){
				
				require_once(PATH."config/getSessUid.php"); /* get $uid */
				
				/* когда в БД не удалось найти пользователя по сессии */
				if(!$uid){
				
					/* она мешает и её нужно удалить */
					$_SESSION['hsessid'] = '';
					session_destroy();
					$_COOKIE['csessid'] = '';
					//setcookie('csessid');
					setcookie("csessid", "", time() - 3600);
					
					if(isset($sessAccesser) && $sessAccesser){
						$resolution = false;
					}else{
						$uid = true;
						$resolution = true;
					}
					
				}else{
					
					$user = getUserData($db,$uid,"r.id=$uid");
					
					if((is_string($user)) && (strpos($user,"error") !== false)){
						$user = array();
						// header("HTTP/1.1 500 SQL Error: $user"); exit;
						/* то есть не приводить к ошибке если авторизация пользователя гараньтированно не требуется */
					}
					
					/* если обязаны допускать только авторизованных пользователей */
					if(isset($sessAccesser) && $sessAccesser){
						/* авторизация пользователя гараньтированно требуется */
						if(!is_array($user)){
							$resolution = false;
							header("HTTP/1.1 401 Unauthorized"); exit;
						}else{
							$resolution = true;
						}
					}
					
				}
				
			}elseif(isset($sessAccesser) && $sessAccesser){
				$resolution = false;
				// echo "401: sesstion not found";
				
			}else{
				$uid = true; /* при отсутвтсии sess, допускать запросы к БД через ajax, если не установлена переменная sessAccesser */
				$resolution = true;
			}
		}
		// при отсутствии getUserAuthData, $resolution может установиться в зн-е false только при несоответствии селектору
		
		
		$select = false;
		if(isset($selector) && ($resolution === true)){
			if(isset($_GET["select"]) or isset($_POST["select"])){
				$select = (isset($_GET["select"])?$_GET["select"]:$_POST["select"]);
			}else $resolution = false;
			
			if($select != $selector){
				/* Bad request */
				// echo "no select";
				$resolution = false;
			}
		}
	}catch(ErrorException $ex){
		$description = exc_handler_report($ex);
		print $description;
	}
	
	/* + используется */
	function getJsonFromFirst($result = []){
		
		$arr = false;
		$strJson = '';
		
		if(is_array($result) && (count($result)>0)){
			$strJson = '';
		
			$j = 0;
			foreach($result as $key => $value){
				$j++;
				
				
				if(!(is_array($key) || is_array($value))){
					$strJson .= '"'.str_replace("\"","'",$key).'":';
					
					$value = str_replace("\r\n","\\r\\n",$value);
					$value = str_replace("\n","\\n",$value);
					$value = str_replace("\t","  ",$value);
					
					$strJson .= '"'.str_replace("\"","'",$value).'"';
					
				}else{
					$arr = true;
					$strJson .= getJsonFromFirst($value);
				}
				
				if($j < count($result)){ $strJson .= ','; }
			}
			
			if($arr){
				$strJson = '['.$strJson.']';
			}else{
				$strJson = '{'.$strJson.'}';
			}
			
		}
		return $strJson;
	}
	
	/* ВНИМАНИЕ - НЕ ТЕРПИТ ТАБОВ */
	
	/* getJsonFromAssoc */
	function toJson($result = [],$assoc = false){
		
		$strJson = '{"success":';
		if(is_array($result)){
			
			if(count($result)>0){
				if($assoc){
					$k=0;
					$strJson .= '1,"listData":[';
					foreach($result as $item){
					    
					    if(!is_array($item)){
                		    $item = $item->toArray();
                		}
					    
						$strJson .= getJsonFromFirst($item);
						$k++;
						
						if(($k < count($result)) && is_array($item) && (count($item)>0)){ $strJson .= ','; }
					}
					$strJson .= ']';
				}else{
					/* from fetchFirst - если это не множество элементов массива */
					$strJson .= '1,"data":'. getJsonFromFirst($result);
				}
			}else{
				if($assoc){
					$strJson .= '1,"listData":[]';
				}else{
					$strJson .= '1,"data":{}';
				}
				
			}
			
		}else{ $strJson .= '0,"description":"Данные не найдены"'; }
		$strJson .= '}';
		
		return $strJson;
	}
	
?>