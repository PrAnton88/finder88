<?php
	
	function getUserDataWithoutOptions(&$db,&$uid,$where){
		/* если пользователя нет в таблице user_options - то получать данные именно так */
		$query = "SELECT u.id as uid, r.id, r.priority, u.post 'prof', concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.int_phone, u.ext_phone, u.role, u.hidden 'dis', 
		c.name as 'company', 
		p.group1 'otdel', 
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
	function getUserData(&$db,&$uid,$where){
		$query = "SELECT u.id as uid, r.id, r.priority, u.post 'prof', concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.int_phone, u.ext_phone, u.role, u.hidden 'dis', c.name as 'company', p.group1 'otdel', d.name as 'dept', k.name as 'nroom', r.login, r.email,
opt.mop, opt.uop, opt.pop, opt.updateTechnicalSupport as 'updateTechnicalPage'
FROM users as u, role as r, user_options as opt,
		place1 as p LEFT OUTER JOIN dept d on d.id = p.dept
		LEFT OUTER JOIN cabinet as k on k.id = p.cabinet 
		LEFT OUTER JOIN companies as c on c.id = p.comp 
		WHERE r.id=u.role AND u.hidden<1 AND u.place=p.id AND opt.id=u.id AND ";
		
		
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
	
?>