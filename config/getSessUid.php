<?php
	if($sess){
		
		$query = "SELECT uid FROM sessions WHERE sid = '$sess' or id = '$sess'";
		$result = $db->fetchFirst($query);
		
		if(is_array($result)){
			$uid = $result['uid'];
			/*$uid - роль*/	
		}else{
			$uid = false;
			/* echo "result is not array"; */
		}
		
		
	}else{
		echo "sess is not found";
	}
?>