<?php
function createCookieFromUserData(&$usr,&$passh,$removePass = false){

	$time = getdate();
	$time = getdate($time[0]+86400);
	$cookexp = mktime(0,0,0,$time['mon'],$time['mday'],$time['year']);

	$ip=$_SERVER['REMOTE_ADDR'];
	
	global $db;
	$query = "SELECT * FROM role WHERE login='$usr' ";
	if(!$removePass){ $query .= " AND password='$passh'";}
	
	
	$user = $db->fetchFirst($query);
	// создаем идентификаторы кук и сессии.
	$cookid = md5($usr.$passh.$time[0]);
	$sessid = md5($cookid.$time[0]);
		
		
	// «десь проходит процедура авторизации, если в базе с хешами нет записи дл¤ пользовател¤, то она создаетс¤
	// «атем она заполн¤етс¤ данными сессии и куки. “акже кука создаетс¤ дл¤ пользовател¤ в браузере.
	if(!($sessdata = $db->fetchFirst("select a.* from `sessions` as a, `role` as b where a.uid=b.id AND b.login='".$usr."'")))
	{
		$db->insert("sessions", array(), array('uid'=>$user['id'], 'id'=>$cookid, 'sid'=>$sessid, 'ip'=>$ip, 'Date_reg'=>date("Y-m-d H:i:s")));
	}
	else
	{
		/*Date_reg обновл¤ть не будем, пускай стоит дата-верм¤ утренней авторизации, самой первой за день*/
		$db->update("sessions", array(), array('id'=>$cookid, 'sid'=>$sessid, 'ip'=>$ip, 'Date_reg'=>date("Y-m-d H:i:s")), "uid=".$user['id']);
	}
	
	// јктиваци¤ кук дл¤ локального браузера и сессии
	$sc = setcookie('csessid', $cookid, $cookexp, '/');
	setcookie("TestCookie", "valueTestCookie", time()+300, "/custom/", "localhost");
	
	$_SESSION['hsessid'] = $sessid;
	
	return $db->fetchFirst("SELECT a.id, a.post as prof, a.place, concat_ws(' ',a.last_name, a.first_name, a.patronymic) as fio, a.role as ws_link FROM users as a, role as b WHERE b.id=a.role AND b.login='".$usr."'");
	
}
?>