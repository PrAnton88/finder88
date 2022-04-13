<?php

function computeCount(&$parent,$admin = false){
	
	$c = 1;
	global $db;
	
	$comments = $db->fetchAssoc("SELECT id, hidden, parent FROM comments WHERE hidden<".($admin?2:1)." AND parent=$parent",true);
	if( is_string($comments) && (strpos($comments,"error") !== false) ){
		throw new ErrorException("SQL for get count comments is invalid");
	}
	
	/*
	if(count($comments) == 0){ 
		return ++$c; 
	}
	*/
	
	foreach($comments as $item){
		
		if($admin || ($item['hidden'] == 0)){
			$c += computeCount($item['id'],$admin);
		}
	}
	
	return $c;
}


function getCount($nRecord,$admin){
	
	$countComment = 0;
	global $db;
	
	$queryComment = "SELECT id,hidden FROM comments WHERE (parent=0 OR (parent IS NULL)) AND request = $nRecord ";
		
	if($admin){
		$queryComment .= " AND hidden < 2 ";
	}else{
		$queryComment .= " AND hidden < 1 ";
	}
	
	$comments = $db->fetchAssoc($queryComment,true);
	if( is_string($comments) && (strpos($comments,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	foreach($comments as $item){
		
		if($admin || ($item['hidden'] == 0)){
			$countComment += computeCount($item['id'],$admin);
		}
	}
	
	return $countComment;
}

?>