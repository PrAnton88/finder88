<?php


/* id - номер заявки */
 
$type = 'tickets';
$guest = false;
if(count($user) == 0){
    $guest = true;
    $user["fio"] = 'guest';
    $user["id"] = 0;
}
 
	include $path."/lib/query/get.query.Comments.php";
	$q = $queryGetComment;
	if($newComment === false){
		$q .= " WHERE c.request = ".$id;
	}
if($admin){
	$q .= " AND c.hidden<2 ";
}else{
	$q .= " AND c.hidden<1 ";
}

$queryGetComment = $q;
// $display = '';

/* выбор прикрепленных файлов */
require_once $path."/lib/getFiles.php";

/* те комментарии которые уже добавленны */
function getComments($type, $data, &$config, $newComment = false){
	
	$user = $config['user'];
	$admin = ($user['priority'] == 3);
	$db = $config['db'];
	$queryGetComment = $config['queryGetComment'];
	$smarty = $config['smarty'];
	
	/* $data = array('id'=>$id,'nparent'=>$item["ncommon"],'repliedly'=>$repliedly); */
	$id = $data['id'];
	$repliedly = $data['repliedly'];
	$editable = $data['editable'];
	
	$ashiddenControls = $data['ashiddenControls'];
	
	$nparent = false;
	if(isset($data['nparent'])){
		$nparent = $data['nparent'];
	}
	
	
	$query = $queryGetComment;
	
	if($newComment !== false){
		/* идентификатор вставленного комментария */
		$query .= " WHERE c.id =".$newComment;
	}
	
	if($nparent && ( ((int) $nparent) != 0)){
		$query .= " AND c.parent =".$nparent;
	}else{
		$query .= " AND ((c.parent IS NULL) OR (c.parent = '') OR (c.parent = 0)) ";
	}
	
	$query .= " ORDER BY c.date_reg ";


	//echo $query;

	$comments = $db->fetchAssoc($query ,true);
	if( is_string($comments) && (strpos($comments,"error") !== false)){
		throw new ErrorException("SQL Error when getting list comments ");
	}

	
	// print_r($comments);
		
	foreach($comments as &$item){
		
		if($type=='tickets'){
			/* когда заявка */
			$item["nrole"] = $item["user_link"];
			$item["comment"] = $item["text"];
			$item["ncommon"] = $item["id"];
			
		}elseif($type=='posts'){
			$item['hidden'] = 0;
			$item["head"] = $item["title"];
		}
		
		/*$chunk = $modx->getObject('modChunk',array(
			'name' => 'commentItem'
		));*/
		
	
		$dateReg = getDateStr($item["date_reg"]);
		$dateReg = $dateReg['fdatestr']." ".$dateReg['ftimestr'];
	
		
		/* $nparent = $item["parent"];
		if(($nparent == 0) || ($nparent == '0') || (empty($nparent))){
			
			$nparent = ''; 
		}*/
		
		
		if($item["nrole"] == 0){ $item["fio"] = "guest";  }
		
		
		/*
		$display .= '<!-- #comment-## -->';
		if($nparent && ( ((int) $nparent) != 0)){
			$display .= '<ol class="children tr">';
		}
		$display .= '<li class="comment odd alt thread-even depth-1 tr">';
		*/
		
		$nparent = (($nparent && ( ((int) $nparent) != 0))?$nparent:0);
		
		
		
		
		
		$smarty->assign("nparent",$nparent);
		$smarty->display('../chunks/comments/commentItem.withoutModx.top.tpl');
		
		
		$content = $item["comment"];
		$content = str_replace("<br>","\n",$content);
		$content = str_replace("<","&#60;",$content);
		$content = str_replace(";>",";&#62;",$content);
		$content = str_replace("\n","<br/>",$content);
		
		$edt = ( ($editable && (((!$guest) && ($user["id"] == $item["nrole"])) || ( ($item["nrole"] == 0) && $admin )))?1:0 );
		
		
		$smarty->assign("id",$id);
		$smarty->assign("nparent",$nparent);
		$smarty->assign("author",$item["fio"]);
		$smarty->assign("datetime",$dateReg);
		$smarty->assign("content",$content);
		$smarty->assign("title",$item["head"]);
		$smarty->assign("hidden",$item["hidden"]);
		$smarty->assign("ncommon",$item["ncommon"]);
		$smarty->assign("type",$type);
		$smarty->assign("editable",$edt);
		$smarty->assign("repliedly",$repliedly);
		
		$smarty->assign("ashiddenControls",$ashiddenControls);
		
		$avatar = $item["avatar"];
		$avatarPath = $item["avatarPath"];
		if($avatar == ''){
			$avatar = '/assets/uploads/avatars/avatar.png';
		}else{
			$avatar = $avatarPath.$avatar;
		}

		$smarty->assign("file",array('link'=>$avatar));
		
		$smarty->assign("files", getFiles($config['modx'],$db,$item["id"]));
		
		$smarty->assign("newComment",$newComment);
		$smarty->display('../chunks/comments/commentItem.withoutModx.tpl');
		
		if($newComment === false){
			getComments($type, array('id'=>$id,'nparent'=>$item["ncommon"],'repliedly'=>$repliedly,'editable'=>$editable,'ashiddenControls'=>$ashiddenControls), $config);
		}
		
		$smarty->assign("nparent",$nparent);
		$smarty->display('../chunks/comments/commentItem.withoutModx.bottom.tpl');
		
		/*
		$display .= '</li>';
		if($nparent && ( ((int) $nparent) != 0)){
			$display .= '</ol>';
		}
		*/
		
	}
	//return $display;
}

/* тут $id это номер заявки */
$config = array('user'=>$user,'db'=>$db,'queryGetComment'=>$queryGetComment,'smarty'=>$smarty,'modx'=>$modx);
echo getComments($type, array('id'=>$id,'repliedly'=>$repliedly,'editable'=>$editable,'ashiddenControls'=>$ashiddenControls,'nparent'=>$nparent), $config, $newComment);

/* 
	если заявка выполнена то передавать сюда $editable = false;
 */