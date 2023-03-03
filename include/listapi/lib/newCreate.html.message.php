<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	$path = '../';
	require_once "$path../start.php";


/* глуповатый файл */
function attemptHtml(&$list){
	
	$listNote = array("theme","measure","date","time","applicant","fio","devices");

	/* пройти по полям, экранировать */
	foreach($list as $key => &$value){

		if(!is_array($value)){
			$value = str_replace('\'','"',$value);
			$value = str_replace("\\","/",$value);
			
			if(in_array($key,$listNote)){
				
				$value = '<b>'.$value.'</b>';
			}elseif($key == "nrequest"){
				/* символ & заменять на %26 */
				$value = "<a href='http://".$_SERVER['HTTP_HOST']."/index.php?id=29%26tiket=".$value."'>этой ссылке</a>";
				// $value = "<a href='http://".$_SERVER['HTTP_HOST']."/index.php?id=29&tiket=".$value."'>этой ссылке</a>";
				
			}elseif($key == "email"){
				/* символ & заменять на %26 */
				$value = "<a href='mailto:$value'>$value</a>";
				
			}
			
			
			
			
		}
		
	}
}

$listPos = array("theme","measure","description","message","date","time","datetime","applicant","fio","ashtml","devices","email","user","note","nrequest");
$listAlias = array("Тема: ","","","","На: ","На: ","На: ","Инициатор: ","Инициатор: ","Представление html","Необходимое оборудование: ","Почта инициатора: ",
	"Пользователь: ",
	"",
	"Для просмотра заявки нажмите по "
);
	

function getPos($el){
	$k=0;
	global $listPos;
	foreach($listPos as $item){
		
		if($item == $el){
			return $k;
		}
		$k++;
	}
	return null;
}

function defaultSort(&$list){
	$k = null;
	global $listPos;
	$newList = array();
	$ofprocess = array();
	foreach($list as $key => $value){

		if(!is_array($value)){
			
			if(in_array($key,$listPos)){
				
				if($value != ""){
				
					if(($i = getPos($key)) || ($i === 0)){
						$k = $i;
						$newList[] = array("i"=>$i,"field"=>$key,"value"=>$value);
					}else{
						$ofprocess[] = array("i"=>$k,"field"=>$key,"value"=>$value);
					}
					
				}
			}
		}
	}
	
	sort($newList);
	
	if(count($ofprocess)>0){
		foreach($ofprocess as $item){
			$newList[] = array("i"=>++$k,"field"=>$item["field"],"value"=>$item["value"]);
		}
	}
	
	return $newList;
}

function defaultAlias(&$list){
	$k=0;
	global $listAlias;
	foreach($list as &$item){
		
		if($item["i"] < count($listAlias)){
			$item["alias"] = $listAlias[$item["i"]];
		}else{
			$item["alias"] = "";
		}
	}
}

if($resolution){
	
	try{
		
		
		/* for availabla for use $dataRecord */
		// include "$path../headerBase.php"; // ломает отправку сообщений.
		// тексты сообщений не прогонять через encodeURIComponent,
		// что бы не пришлось использовать это "подключение", иначе, почему то обрезается $dataRecord - на выходе только верстка об инифиаторе.
		
		
		$dataRecord = false;
		if(!isset($_POST['dataRecord'])){
			
			throw new ErrorException("request data is invalid. record for new data is not found ");
		}
		
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
		
		
		
		/*
		if(!(isset($dataRecord['id']) && isset($dataRecord['nrequest']))){
			throw new ErrorException("Недостаточно данных");
		}*/
		
		/* добавим в dataRecord["email"] = $user["email"] */
		
		
		
		$dataRecord["email"] = $user["email"];
		
		attemptHtml($dataRecord);
		
		$dataRecord = defaultSort($dataRecord);
		
		defaultAlias($dataRecord);
		
	
	

	
	
	
		//$display = 'Заголовок: <b>Значение</b><br />Другое значение ';
		//$display = "Здравствуйте!<br />";
		$display = '';
		/* интересуют alias и value */
		foreach($dataRecord as $item){
			
			
			if($item["field"] != "ashtml"){ 
			
				if($item["field"] == "note"){ 
					$display .= '<span class="forNotes">/* '; 
				}else{
					$display .= '<span class="italicBoldNote">'.$item["alias"];
					
				}
				
				$display .= ($item["value"]);
				
				if($item["field"] == "note"){ 
					$display .= ' */'; 
				}
				
				$display .= '</span>';
				
				/* if(($item["field"] != "nrequest") && ($item["field"] != "email")){ */
					$display .= '<br />';
				/* } */
				
			}else{
				
				$display .= ($item["value"]);
			}
		}
	
		echo $display;
		
	
		header("HTTP/1.1 200 Ok");
		
		
	}catch(ErrorException $ex){
		
		echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	}


}else{ 

	header("HTTP/1.1 403 Forbidden");
}
?>