<?php

if(isset($_COOKIE['dbtype'])){
	if($_COOKIE['dbtype'] == "prod"){
		$db = new DB("modx-text");
	}else{
		$db = new DB("modx-text");
	}
}else{
	$db = new DB("modx-text");
}

//$db = new DB("info-base-text");
?>