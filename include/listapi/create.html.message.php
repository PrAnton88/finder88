<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";


/* луповатый файл. (не начинайте изучение АПИ с этого файла) */
function attemptHtml(&$list){
	
	$listNote = array("theme","subject","measure","date","time","applicant","fio","devices");

	/* пройти по полям, экранировать */
	foreach($list as $key => &$value){

		if(!is_array($value)){
			$value = str_replace('\'','"',$value);
			$value = str_replace("\\","/",$value);
			
			if(in_array($key,$listNote)){
				
				$value = '<b>'.$value.'</b>';
			}elseif($key == "nrequest"){
				/* символ & заменять на %26 */
				$value = "<a href='http://localhost/index.php?id=29%26tiket=".$value."'>этой ссылке</a>";
				
			}elseif($key == "email"){
				/* символ & заменять на %26 */
				$value = "<a href='mailto:$value'>$value</a>";
				
			}
			
			
			
			
		}
		
	}
}

$listPos = array("description","date","time","datetime","applicant","fio","email","theme","subject","measure","note","devices","nrequest","user");
$listAlias = array("","На: ","На: ","На: ","Инициатор: ","Инициатор: ","Почта инициатора: ","Тема: ","Тема: ","Тема: ","","Необходимое оборудование: ",
	"<br /><br />Что бы перейти к заявке пройдите, пожалуйста, по ","Пользователь: "
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
				
				if(($i = getPos($key)) || ($i === 0)){
					$k = $i;
					$newList[] = array("i"=>$i,"field"=>$key,"value"=>$value);
				}else{
					$ofprocess[] = array("i"=>$k,"field"=>$key,"value"=>$value);
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
		$display = "Здравствуйте!<br />";
		/* интересуют alias и value */
		foreach($dataRecord as $item){
			$display .= ($item["alias"].$item["value"]."<br />");
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