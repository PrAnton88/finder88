<?php

header('Content-type:application/json');
// header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
class SpliterTime{
	var $uTime = false;
	var $date = false;
	var $time = false;
	var $list = false;
	/* "Y-m-d H:i:s" */
	function SpliterTime($t){
		$t = explode(" ",$t);
		$this->date = explode("-",$t[0]);
		$this->time = explode(":",$t[1]);
		$this->uTime = Array ("date" => $this->date, "time" => $this->time); 
	}
	function split(){
		$this->list = Array("Y"=>$this->date[0],"m"=>$this->date[1],"d"=>$this->date[2],"H"=>$this->time[0],"i"=>$this->time[1],"s"=>$this->time[2]);
	}
	function get($par = false){
		if(!$this->list) $this->split();
		if($par && $this->list[$par]) return $this->list[$par];
		// return as array(Y,m,d,H,i,s)
		return $this->list;
	}
	/*
	function tomktime(){
		// return as array(h,i,s,m,d,y)
		return Array("H"=>$this->time[0],"i"=>$this->time[1],"s"=>$this->time[2],"m"=>$this->date[1],"d"=>$this->date[2],"Y"=>$this->date[0]);
	}*/
	function mktime(){
							/* так как принимает только в формате (h,i,s,m,d,y) */
		return mktime(/*"H"=>*/$this->time[0],/*"i"=>*/$this->time[1],/*"s"=>*/$this->time[2],
					   /*"m"=>*/$this->date[1],/*"d"=>*/$this->date[2],/*"Y"=>*/$this->date[0]);
	}
}

try{
	require_once "../start.php";
	if($resolution){
	
		$description = "";
		
		if(isset($_POST['dataRecord'])){
			
			$dataOutput = array();
			$id = $user["uid"];
			
			$description .= 'Получено сообщение. ';
			
			$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
			$dataRecord = json_decode($dataRecord,true);
			
			$title = false;
			if(isset($dataRecord['title'])){
				$title = str_replace('\'','"',$dataRecord['title']);
				$title = str_replace("\\","/",$title);
			}
			
			$body = false;
			if(isset($dataRecord['body'])){
				$body = str_replace('\'','"',$dataRecord['body']);
				$body = str_replace("\\","/",$body);
			}
			
			$comment = false;
			if(isset($dataRecord['comment'])){
				$comment = str_replace('\'','"',$dataRecord['comment']);
				$comment = str_replace("\\","/",$comment);
			}
			$hidden = false;
			if(isset($dataRecord['hidden'])){
				$hidden = $dataRecord['hidden'];
				if(($hidden !== "false") && ($hidden != 0)){
					$hidden = true;
				}else{ $hidden = false; }
			}
			
			$type = 0;
			if(isset($dataRecord['type'])){
				$type = (int) $dataRecord['type'];
			}
			
			$link = 0;
			if(isset($dataRecord['link'])){
				$link = (int) $dataRecord['link'];
			}
			/* if($link == 0){ $link = NULL; } */
			
			
			$nrequest = false;
			if(isset($dataRecord['nrequest'])){
				$nrequest = (int)$dataRecord['nrequest'];
			}
			
			/* если $nrequest - то редактирование или коммент */
				/* если $nrequest и не $comment то редактированрие $title и $body */
			
			
			if($nrequest === false){
				/* то новая заявка */
			
				/* в заявках поле .applicant это users.id, а в $userid хранится users.role */
				
				$query = "SELECT * FROM request WHERE header='".$title."' AND message='".$body."' AND (state is null OR state=0 OR state=1) AND applicant=$id";
				$existmess = $db->fetchFirst($query);
				
				if((is_string($existmess)) && (strpos($existmess,"error") !== false)){
					throw new ErrorException("500 SQL Error: $existmess");
				}
				
				$dtime = false;
				if($existmess){
					
					$dtime = $existmess['opened'];/*Вид 2021-06-08 16:39:21*/
				
					/* конвертировать в формат, пригодный для конветрации в юникс формат, а именно в h,i,s,m,d,y */
					$dtime = new SpliterTime($dtime);
					
					
					
					/* если один из элементов лож или оба */
					/* то есть, если такой заявки открытой нет, или если есть но она была создана давно (10 мин назад или больше) */
					/* что равнозначно - есзи НЕ(заявка открыта И сзднана недавно) */
					
					
					// echo ($dtime->mktime()).' '.(time() - 600);
				}
				
				
				if (!(($existmess = $db->fetchFirst($query)) and (/*если не дубирует созданные*/($dtime !== false) && (($dtime->mktime()) > (time() - 600)) ))){
					
					$date = date("Y-m-d H:i:s");
					
					$respInsert = $db->insert("request", array('type'=>$type, 'user_link'=>'NULL', 'applicant'=>$id, 'state'=>0, 'priority'=>0, 'opened'=>"'$date'", 'user_apdate'=>$id, 'link'=>($link == 0?'NULL':$link)), array('header'=>$title, 'message'=>$body));
					
					if(!$respInsert){
						$description .= " По невыясненной причине заявка от пользователя $id - $title - $body, не создана. ";
						
						
					}else{
						$description = "Заявка создана";
						
						$dataOutput["id"] = $respInsert;
						$dataOutput["new"] = true;
					}
					
					
				}else{
					
					$description = 'Ранее вы уже создали точно эту же заявку. См заявку '.$existmess['id'];
					
					$dataOutput["id"] = $existmess['id'];
					$dataOutput["new"] = false;
				}
				
			}else{
				if($nrequest <= 0){
					throw new ErrorException("Нет поля nrequest");
				}
				
				/* передан $nrequest значит редактирование или вставка комментария */
				if($comment !== false){
					if($hidden !== false){
						$hidden = 1;
					}else{
						$hidden = 0;
					}
					
					$respInsert = $db->insert("comments", array('request'=>$nrequest, 'user_link'=>$user["id"], 'hidden'=>$hidden), array('text'=>$comment, 'date_reg'=>date("Y-m-d H:i:s")));
					if( is_string($respInsert) && (strpos($respInsert,"error") !== false)){
						throw new ErrorException("SQL Error. comments push has failed.");
					}
					
					$dataOutput["context"] = 'conmment';
				}else{
					/* редактирование  */
					/* обновление $title и $body */
					$query = "SELECT * FROM request WHERE id=$nrequest";
					$existmess = $db->fetchFirst($query);
					if((is_string($existmess)) && (strpos($existmess,"error") !== false)){
						throw new ErrorException("SQL Error. get request data has failed.");
					}
					
					if(! (($existmess["applicant"] == $user["uid"]) || ($user['priority'] == 3))){
						throw new ErrorException("Вы не имете право на изменение записи .");
					}
					
					
					$query = "UPDATE request set header='".addslashes($title)."', message='".addslashes($body)."' WHERE id=$nrequest";
					
					$resultUpd = $db->query($query, $uid);
					if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
						throw new ErrorException("SQL Error. updating request data has failed. $resultUpd");
						// header("HTTP/1.1 500 SQL Error: $resultUpd");
						// exit;
					}
					
					$dataOutput["context"] = 'content';
				}
				
				$dataOutput["id"] = $nrequest;
				$dataOutput["update"] = true;
				
			}
			
			
			
			
			$dataOutput = json_encode($dataOutput);
			
			$display = '{"success":1,"description":"'.$description.'","data":'.$dataOutput.'}';
			echo $display;
			
			header("HTTP/1.1 200 Ok");
			
			
		}else{
			throw new ErrorException("Нет данных для записи");
		}
	
	}else{ 

		header("HTTP/1.1 403 Forbidden");
	}

}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}
?>