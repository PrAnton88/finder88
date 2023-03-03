<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;

try{
	
	/* useExam
		dataReqNext({file:urlServerSide+'helpdesk/ticketInit.php',type:'text',
		args:'dataRecord='+JSON.stringify({nticket:4665,unsingleticket:true})},
			function(){}
		); 
	*/
	
	$path = '../';
	require_once "$path../start.php";
	
	function getDateStr($dateval){
		
    	$months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
  
    	if (($dateval == "") or ($dateval == "now")){
			$datetime = getdate();
    	}else{
			$datetime = getdate($dateval);
    	}
    	$mp="";
    	$sp="";
    	$dp="";
    	$dp_="";
    	if (intval($datetime['minutes']) < 10 ) $mp = "0";
    	if (intval($datetime['seconds']) < 10 ) $sp = "0";
    	if (intval($datetime['mon']) < 10 ) $dp = "0";
    	if (intval($datetime['mday']) < 10 ) $dp_ = "0";
    	$fdatestr = $datetime['mday']." ".$months[$datetime['mon']-1]." ".$datetime['year'];
    	$ftimestr = $datetime['hours'].":".$mp.$datetime['minutes'].":".$sp.$datetime['seconds'];
    	$fshortdate = $dp_.$datetime['mday'].".".$dp.$datetime['mon'].".".$datetime['year'];
    	$fsshortdate = $dp_.$datetime['mday'].".".$dp.$datetime['mon'].".".substr($datetime['year'],strlen($datetime['year'])-2,2);
    	return array ("fdatestr"=>$fdatestr, "ftimestr"=>$ftimestr, "shortdate"=>$fshortdate, "sshortdate"=>$fsshortdate);
    }
	
	
	function printWarn($message){
		
		return '<div><script>oBaseUIApi.print.message.warn("'.$message.'")</script></div>';
	}


	if(!$resolution){
		if(count($user) == 0){
			
			echo printWarn('401 Unauthorized'); exit;
			// throw new ErrorException('401 Unauthorized');
		}
		// header("HTTP/1.1 403 Forbidden");
		// throw new ErrorException('403 Forbidden');
		echo printWarn('403 Forbidden'); exit;
		// exit;
	}


	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;


	require_once $path."../preDataQuery.php";
	/* инициализировать админов и бывших админов */
	if(!($admins = $db->fetchAssoc($queryNowAdmins))){
		throw new ErrorException('Информация об админах не найдена');
	}
	$smarty->assign("admins",$admins);
	
	// бывшие админы
	if(!($passadmins = $db->fetchAssoc($adminsPassQuery))){
		throw new ErrorException('Информация о бывших админах не найдена');
	}
	
	$smarty->assign("passadmins",$passadmins);
	
	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	$nticket = false;
	if(!isset($dataRecord['nticket'])){
		throw new ErrorException("arg nticket is not found");
	}
	$nticket = (int) $dataRecord['nticket'];
	if($nticket <= 0){
		throw new ErrorException("arg nticket is invalid");
	}
	
	$unsingleticket = false;
	if(isset($dataRecord['unsingleticket'])){
		$unsingleticket = (boolean) $dataRecord['unsingleticket'];
	}


	$admin = ($user['priority'] == 3);
		
	require_once $path."/lib/query/get.query.ListTickets.php";
	
	
	$query = getQueryToGetTickets()." WHERE a.id=".$nticket."";
	
	
	$uid = $user['uid'];
	
	if(!($message = $db->fetchFirst($query))){
		/*когда не найдена инфа о заявке с заявителем*/
		
		if(!($message = $db->fetchFirst("SELECT a.id, a.applicant 'req_id', unix_timestamp(a.opened) as 'opened', 'Склад' as ass, a.act,
	a.closed, a.time_sub 'sred', a.header, a.message, a.state, a.priority 'prior', a.user_link 'assd', a.type 'ctype', a.device 'cid',
	concat_ws(' ', b.last_name, b.first_name, b.patronymic) 'req'
	FROM request as a LEFT JOIN users as b ON a.user_link=b.id
	WHERE a.hidden<2 AND a.id=$nticket"))){

			/*когда не найдена инфа даже о заявке без заявителя*/
			$smarty->assign("message","no");
			
			$smarty->assign("admin",$admin);
			$smarty->assign("pn","Helpdesk - сообщения");
			return $smarty->display('../chunks/page.helpdeskTicket-Init.tpl');
			
		}else{
		
			//$smarty->assign("message","automaticalRquest");
			
			
			if ((!isset($message['req'])) or ($message['req']==''))	$message['req'] = 'не назначен';
			//$dtstring = new SpliterTime($message['opened']);
			//$smarty->assign("opendate",$dtstring->date." ".$dtstring->time);
			if (($message['act']=='') or (!(isset($message['act'])))) $message['act']='нет';
			if(isset($message['req'])){
				$assarr = explode(' ',$message['req']);
				$message['req_']=$assarr[0].' '.$assarr[1];
			}
			
			
			
			$smarty->assign("message",$message);
			$smarty->assign("admin",$admin);
			$smarty->assign("pn","Helpdesk - сообщения");
			return $smarty->display('../chunks/page.helpdeskTicket-Init.tpl');
			
		}
		
	}else{
		
		$message['header']=str_replace("/", "\\",$message['header']);
		
		$temp = null; $temp = $message['message'];
		if (strrpos($temp, ":/") === false) { $temp=str_replace("/", "\\",$temp); }
		
		$temp = str_replace("<","&#60;",$temp);
		$temp = str_replace(";>",";&#62;",$temp);
		
		
		
		
		$temp = str_replace("\r\n","<br />",$temp);
		$temp = str_replace("\n", " <br />", $temp);
		
		//$temp = str_replace("&#60;","<",$temp);
		//$temp = str_replace(";&#62;",";>",$temp);
		
		
		$temp = str_replace("\t", "&nbsp;&nbsp;&nbsp;", $temp);
		$message['message'] = $temp;
		
		
		$smarty->assign("uid",$uid);
		
		/*дублируется тоже что и в automaticalRquest*/
		if ((!isset($message['req'])) or ($message['req']==''))	$message['req'] = 'не назначен';
		//$dtstring = new SpliterTime($message['opened']);
		//$smarty->assign("opendate",$dtstring->date." ".$dtstring->time);
		$assarr = explode(' ',$message['req']);
		$message['req_']=$assarr[0].' '.$assarr[1];
		
		
		/* getComments */
		include $path."/lib/query/get.query.Comments.php";
	    $q = $queryGetComment." WHERE c.request = ".$message['id']." AND c.hidden<1 AND (c.parent IS NULL OR c.parent=0) ";
		
	    $issetComments = $db->fetchFirst($q,true);
		if((is_string($issetComments)) && (strpos($issetComments,"error") !== false)){
    		throw new Error("SQL Error");
    	}
		
	    /* возможно тут нужна информация о наличии комментариев */
		if(count($issetComments) > 0){
            $issetComments = true;
		}else{
			$issetComments = false;
		}
		
		$smarty->assign("hidden",$message['hidden']);

		if ($message['act']=='') $message['act']='нет';
		
		
		/* check dispatchRequest */
		$query = $queryCheckDispatchRequest." AND id = ".$user['id'];
		$dispatchRequest = $db->fetchFirst($query,true);
		if((is_string($dispatchRequest)) && (strpos($dispatchRequest,"error") !== false)){
			throw new Error("SQL Error when checking dispatchRequest.");
		}
		if(is_array($dispatchRequest) && (count($dispatchRequest)>0)){
			$smarty->assign("dispatchRequest",true);
		}else{
			$smarty->assign("dispatchRequest",false);
		}
		/* complete check dispatchRequest */
		
		
		
		/* выборка картинок к заявке - i.image, i.path as imagePath */
		/* 
			i.image, i.path as imagePath  
		FROM request as a
		LEFT JOIN images_context as ic ON ic.ncontext = a.id 
		LEFT JOIN images as i ON i.id = ic.nimage
		*/
		// $message['images'] = array();
		
		
		require_once $path."/lib/getFiles.php";
		$listImages = getFiles($modx,$db,$message['id']);
		if(count($listImages)>0){
			$smarty->assign("hasimage",true);
			$message['images'] = $listImages;
		}else{
			$smarty->assign("hasimage",false);
		}
		

		$smarty->assign("pathAppChunk",$_SERVER['DOCUMENT_ROOT'].'/core/Smarty/chunks');
		$smarty->assign("outside",true);
		
		$smarty->assign("message",$message);
		/*
		echo 'count admins = '.count($admins);
		print_r($admins);
		*/
		
		$display = '';
		if($unsingleticket){
			
			$smarty->assign("singleticket",false);
		}else{
			$smarty->assign("singleticket",true);
			
			$display .= $smarty->display('../chunks/commentForm.lib-hand.tpl');
		}
		
		$smarty->assign("admin",$admin);
		
		$display .= $smarty->display('../chunks/page.helpdeskTicket-min-Init.tpl');
		
		echo $display;
		
		$id = $message['id'];
		//$repliedly = ( (($admin != 0) or ($uid == $message['applicant']))?1:0 );
		$repliedly = true;
		
		$editable = true;/* только о том, выполнена ли заявка */
		if($message['state'] == 2){
			/* когда заявка выполнена - не repliedly */
			$repliedly = false;
			$editable = false;
		}
		
		
		$ashiddenControls = $message['hidden'];
		
		echo '<div id="comments_'.$message['id'].'" class="comments my-3" style="background: unset;">';

		/* if($issetComments){ */
			echo '<strong class="titlePage">Комментарии:</strong>';
		/* } */
			
			echo '<ol class="comment-list tLeft" style="margin-bottom:0px; padding-bottom: 0px;">';
				
				$nparent = 0;
				$newComment = false;
				include 'getCommentItems.php';
				//echo $disp;
		
			echo '</ol>';
			
			
			if(($admin != 0) or ($uid == $message['applicant'])){
				
				$smarty->assign("nres",$message['id']);
				
				if(($message['state'] < 2) && ($message['hidden'] != 1)){
					
					
					$smarty->assign("admin",$admin?1:0);
					$smarty->assign("recovery",0);
					
					echo $smarty->display('../chunks/commentForm.tpl');
				}
				
				$smarty->assign("nreshand","'listapi/commently/tickets/set.record.php'");
				$smarty->assign("type",'tickets');

				echo $smarty->display('../chunks/commentForm.hand.tpl');
				
			}
			
		echo '</div>';
		
		
		return;
	}
	
}catch(ErrorException $ex){
	/* тут заворачиваем в <div> только потому что в loadArcicleContent скрипты добавляются не как скрипты заголовка страницы
	а как часть элементов тела документа (к сожалению пока так) */
	echo '<div><script>new UserException("'.exc_handler($ex).'").log();</script></div>';
}catch(Error $ex){
	
	echo '<div><script>new UserException("'.exc_handler($ex).'").log();</script></div>';
}