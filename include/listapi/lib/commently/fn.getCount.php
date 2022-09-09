<?php


class oCommentsHand
{
	var $db;
	
	public function __construct($_db){
		$this->db = $_db;
	}
	
	private function computeCount(&$parent,$admin = false){
		
		$c = 1;
		
		
		$comments = $this->db->fetchAssoc("SELECT id, hidden, parent FROM comments WHERE hidden<".($admin?2:1)." AND parent=$parent",true);
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
				$c += $this->computeCount($item['id'],$admin);
			}
		}
		
		return $c;
	}


	public function getCount($nRecord,$admin){
		
		$countComment = 0;
		
		$queryComment = "SELECT id,hidden FROM comments WHERE (parent=0 OR (parent IS NULL)) AND request = $nRecord ";
			
		if($admin){
			$queryComment .= " AND hidden < 2 ";
		}else{
			$queryComment .= " AND hidden < 1 ";
		}
		
		$comments = $this->db->fetchAssoc($queryComment,true);
		if( is_string($comments) && (strpos($comments,"error") !== false)){
			throw new ErrorException("SQL Error");
		}
		
		foreach($comments as $item){
			
			if($admin || ($item['hidden'] == 0)){
				$countComment += $this->computeCount($item['id'],$admin);
			}
		}
		
		return $countComment;
	}
	
	public function getLastComment($nRecord,$admin){
		
		$lastComment = "";
		
		$queryComment = "SELECT c.id, c.text, c.user_link, c.hidden, concat_ws(' ', u.last_name, u.first_name) 'fi' FROM comments as c LEFT JOIN users as u ON u.id = c.user_link WHERE (c.parent=0 OR (c.parent IS NULL)) AND c.request = $nRecord ";
			
		if($admin){
			$queryComment .= " AND c.hidden < 2 ";
		}else{
			$queryComment .= " AND c.hidden < 1 ";
		}
		
		$comments = $this->db->fetchAssoc($queryComment,true);
		if( is_string($comments) && (strpos($comments,"error") !== false)){
			throw new ErrorException("SQL Error");
		}
		
		foreach($comments as $item){
			
			$lastComment = $item['fi'].': '.$item['text'];
		}
		
		return $lastComment;
	}

}
?>