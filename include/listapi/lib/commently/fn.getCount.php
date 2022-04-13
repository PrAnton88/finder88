<?php

function getCount(&$id,$admin = false){
	
	$c = 1;
	global $db;
	
	$comments = $db->fetchAssoc("SELECT id, user_link, hidden, parent FROM comments WHERE hidden<".($admin?2:1)." AND parent=$id",true);
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
			$c += getCount($item['id'],$admin);
		}
	}
	
	return $c;
}
?>