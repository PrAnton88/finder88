<?php
	
	function getUserDataWithoutOptions(&$db,&$uid,$where){
		/* если пользователя нет в таблице user_options - то получать данные именно так */
		$query = "SELECT u.id as uid, r.id, r.priority, u.post 'prof', concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.int_phone, u.ext_phone, u.role, u.hidden 'dis', 
		c.name as 'company', 
		p.dept 'otdel', 
		d.name as 'dept', 
		k.name as 'nroom', 
		r.login, r.email 
FROM users as u, role as r, 
		place1 as p LEFT OUTER JOIN dept d on d.id = p.dept
		LEFT OUTER JOIN cabinet as k on k.id = p.cabinet 
		LEFT OUTER JOIN companies as c on c.id = p.comp 
		WHERE r.id=u.role AND u.hidden<1 AND u.place=p.id AND ";
		
		$query .= $where;
		return $db->fetchFirst($query,$uid);
	}
	/* можно ли так делать, ведь uid должен быть ролью а id = user.id */
	/* возможны последствия при переделывании */
	$queryUserData = "SELECT u.id as uid, r.id, u.role, r.priority, u.post 'prof', concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', concat_ws(' ', u.last_name, u.first_name) 'fi', u.int_phone, u.ext_phone, u.role, u.hidden 'dis', c.name as 'company', p.dept 'otdel', d.name as 'dept', k.name as 'nroom', r.login, r.login as 'slogin', r.email,
opt.mop, opt.uop, opt.pop, opt.updateTechnicalSupport as 'updateTechnicalPage'
FROM users as u, role as r, user_options as opt,
		place1 as p LEFT OUTER JOIN dept d on d.id = p.dept
		LEFT OUTER JOIN cabinet as k on k.id = p.cabinet 
		LEFT OUTER JOIN companies as c on c.id = p.comp 
		WHERE r.id=u.role AND u.hidden<1 AND u.place=p.id AND opt.id=r.id AND ";
		
	$queryTicketsAssd = "SELECT tick.id, u.id as uid, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.hidden 'dis',
r.login, r.email
FROM request as tick LEFT JOIN users as u ON u.id = tick.user_link RIGHT JOIN role as r ON r.id = u.role
		WHERE u.hidden<1 AND tick.id = ";
		
	$queryAdminsCkeckeds = "SELECT u.id as uid, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.hidden 'dis',
r.login, r.email
FROM users as u RIGHT JOIN frontend as f ON f.id = u.id, role as r
		WHERE r.id=u.role AND u.hidden<1";
	
	function getUserData(&$db,&$uid,$where){
		
		global $queryUserData;
		$query = $queryUserData;
		
		
		/* вместо where часть запроса
			if($login){
				$query .= " r.login='$login' ";
			}elseif($userid){
				$query .= " u.id=$userid ";
			}else{
				$query .= " r.id=$uid ";
			}
		*/
		
		$query .= $where;
		return $db->fetchFirst($query,$uid);
		
	}
	
	function getUserDataForInsideSnipetHelpdesk(){
		
		/* потому что нельзя брать из сниппетов modx ни переменные $queryUserData так же недоступно глобальные переменные $queryUserData */
		/* возможно нужно делать через return. Но хотя бы доступны эти функции. */
		/* итого $queryUserData : */
		
		return "SELECT u.id as uid, r.id, u.role, r.priority, u.post 'prof', concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.int_phone, u.ext_phone, u.role, u.hidden 'dis', c.name as 'company', p.dept 'otdel', d.name as 'dept', k.name as 'nroom', r.login, r.email,
opt.mop, opt.uop, opt.pop, opt.updateTechnicalSupport as 'updateTechnicalPage'
FROM users as u, role as r, user_options as opt,
		place1 as p LEFT OUTER JOIN dept d on d.id = p.dept
		LEFT OUTER JOIN cabinet as k on k.id = p.cabinet 
		LEFT OUTER JOIN companies as c on c.id = p.comp 
		WHERE r.id=u.role AND u.hidden<1 AND u.place=p.id AND opt.id=r.id ";
	}
	
	
	/* ещё почту, для отправки им сообщений */
	function getQueryToGetUserLaws($listLaw){
		
		return "SELECT $listLaw, u.id, r.email, r.login, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.post 'prof' FROM law as l LEFT JOIN users as u ON u.role = l.uid LEFT JOIN role as r ON r.id = u.role";
		
		/*  .= " WHERE u.role = ".$uid; */
		
		/* например уогда нужно узнать кто оброадает тем или иным правом */
		
		/*  .= " WHERE l.nameField = ".1 */
	}
	
	function getQueryUserLaws(&$db){
		
		$listLaw = ["l.id as lawid","l.uid"];
		$query = 'SELECT id,nameField,nameLaw,messHelp FROM listlaw';
		$resplistLaw = $db->fetchAssoc($query,true);
		if(is_string($resplistLaw) && (strpos($resplistLaw,'error')!== false)){
			throw new ErrorException('SQL Error');
		}
		
		foreach($resplistLaw as &$item){
			$listLaw[] = "l.".$item['nameField'];
		}
		$listLaw = implode(", ", $listLaw);
		
		
		return getQueryToGetUserLaws($listLaw);
		
	}
	
	
?>