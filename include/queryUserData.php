<?php
	
	function getUserDataWithoutOptions(&$db,&$uid,$where){
		/* если пользователя нет в таблице user_options - то получать данные именно так */
		$query = "SELECT u.id as uid, r.id, r.priority, u.post 'prof', concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', concat_ws(' ', u.last_name, u.first_name) 'fi', u.int_phone, u.ext_phone, u.role, u.hidden 'dis', 
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
	$queryUserData = "SELECT u.id as uid, r.id, u.role, r.priority, u.post 'prof', u.patronymic, u.description, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', concat_ws(' ', u.last_name, u.first_name) 'fi', u.int_phone, u.ext_phone, u.role, u.hidden 'dis', c.id as 'ncomp', c.name as 'company', p.dept 'otdel', d.name as 'dept', k.name as 'nroom', r.login, r.login as 'slogin', r.email,
opt.mop, opt.uop, opt.pop, opt.updateTechnicalSupport as 'updateTechnicalPage', i.id as nimage, i.image as avatar, i.path as avatarPath 
FROM users as u
LEFT JOIN images_context as ic ON ic.ncontext = u.id 
LEFT JOIN images as i ON i.id = ic.nimage, 
role as r, user_options as opt,
		place1 as p LEFT OUTER JOIN dept d on d.id = p.dept
		LEFT OUTER JOIN cabinet as k on k.id = p.cabinet 
		LEFT OUTER JOIN companies as c on c.id = p.comp 
		WHERE r.id=u.role AND u.hidden<1 AND u.place=p.id AND opt.id=r.id ";
		
	$queryDisabledUser = "SELECT u.id, u.patronymic, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', concat_ws(' ', u.last_name, u.first_name) 'fi', u.post as 'prof', u.int_phone, u.ext_phone, u.hidden as 'dis', u.description, c.name as 'company', p.dept 'otdel', d.name as 'dept', k.name as 'nroom' FROM users as u, place1 as p LEFT OUTER JOIN dept d on d.id = p.dept LEFT OUTER JOIN cabinet as k on k.id = p.cabinet LEFT OUTER JOIN companies as c on c.id = p.comp WHERE u.place=p.id ";
		
	/*
	$query = "SELECT u.id, u.role 'ws_link', u.last_name, u.first_name, u.patronymic, u.post as 'prof', u.int_phone, u.ext_phone, u.hidden as 'dis', u.description, comp.name, d.name as 'deptname', cab.name as 'nroom' FROM cabinet as cab, users as u LEFT JOIN place1 as p ON u.place=p.id LEFT JOIN companies as comp ON p.comp=comp.id LEFT JOIN dept as d ON d.id = p.dept WHERE cab.id = p.cabinet AND u.decret<>1 AND u.hidden=0 ".$filterstring1.$filterstring2.$ffilterstring." ORDER BY last_name LIMIT ".($p*$uop).", ".$uop;
	*/
		
	$queryTicketsAssd = "SELECT tick.id, u.id as uid, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.hidden 'dis',
r.login, r.email
FROM request as tick LEFT JOIN users as u ON u.id = tick.user_link RIGHT JOIN role as r ON r.id = u.role
		WHERE u.hidden<1 AND tick.id = ";
		
		
	$queryApplicantOfTicket = "SELECT tick.id, tick.type as 'ctype', u.id as uid, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.hidden 'dis',
r.login, r.email
FROM request as tick LEFT JOIN users as u ON u.id = tick.applicant LEFT JOIN role as r ON r.id = u.role
		WHERE u.hidden<1 AND tick.id = ";
		
	/* мой вариант, когда выбирали из 'frontend' */
	$queryAdminsCkeckeds = "SELECT u.id as uid, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.hidden 'dis',
r.login, r.email
FROM users as u RIGHT JOIN frontend as f ON f.id = u.id, role as r
		WHERE r.id=u.role AND u.hidden<1";
		
	/* вариант Алексея Сеченых (модифицированный), он не использует таблдицу 'frontend' */
	$SechenuchQueryCheckedsON = "SELECT s.User_id as role, u.id as uid, CONCAT_WS(' ',u.last_name, u.first_name, u.patronymic) fio, u.hidden 'dis',
r.login,  r.email
from role r LEFT OUTER JOIN send_options s on s.User_id = r.id
LEFT OUTER JOIN users as u on u.role = r.id
		WHERE s.SendON_1 = 1 or s.SendON_2 = 2";
	
	
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
		
		$query .= "AND ". $where;
		
		return $db->fetchFirst($query,$uid);
	}
	
	function getUserDataForInsideSnipetHelpdesk(){
		
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
		
		return "SELECT $listLaw, u.id as uid, r.id, r.email, r.login, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.post 'prof' FROM law as l LEFT JOIN users as u ON u.role = l.uid LEFT JOIN role as r ON r.id = u.role";
		
		/*  .= " WHERE u.role = ".$uid; */
		
		/* например уогда нужно узнать кто оброадает тем или иным правом */
		
		/*  .= " WHERE l.nameField = ".1 */
	}
	
	$queryListlaw = 'SELECT id,nameField,nameLaw,nameLawShort,messHelp FROM listlaw';
	function getQueryUserLaws(&$db){
		
		/* помним, тут l.uid это user.role */
		
		
		$listLaw = ["l.id as lawid","l.uid"];
		$resplistLaw = $db->fetchAssoc(queryListlaw,true);
		if(is_string($resplistLaw) && (strpos($resplistLaw,'error')!== false)){
			throw new ErrorException('SQL Error');
		}
		
		foreach($resplistLaw as &$item){
			$listLaw[] = "l.".$item['nameField'];
		}
		$listLaw = implode(", ", $listLaw);
		
		
		return getQueryToGetUserLaws($listLaw);
		
	}
	
	/* что бы были доступны глобально, а не только из снипета который подключает этот файл */
	define('queryUserData',$queryUserData);
	define('queryListlaw',$queryListlaw);
	define('queryDisabledUser',$queryDisabledUser);
	/* когда 'queryUserData' без кавычек, то файлы генерируют Warning, который
	несмотря на подключаемый error.reporting.php обработать неудаётся */
?>