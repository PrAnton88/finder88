<?php
	
	$deptsQuery = "SELECT DISTINCT d.id, d.name as deptname FROM dept as d LEFT JOIN place1 as p ON p.dept = d.id WHERE d.hidden=0 AND p.hidden=0 ORDER By d.name";
	
	$companiseQuery = "SELECT * FROM companies WHERE hidden=0 AND name NOT LIKE '%Все организации%' ORDER By name";
	
	$adminsQuery = "SELECT a.id, a.post as prof, a.last_name, a.first_name, a.patronymic, concat_ws(' ',a.last_name, a.first_name, a.patronymic) as fio, a.role as ws_link, a.date_role, a.place, a.int_phone, a.ext_phone, a.date_reg FROM users as a LEFT JOIN place1 as b ON a.place = b.id WHERE b.group1 = 36 AND a.id<>0 ";
	
	/* админы */
	$queryNowAdmins = $adminsQuery ." AND a.hidden=0 ORDER by first_name";
	
	/* бывшие админы */
	$adminsPassQuery = $adminsQuery ." AND a.hidden=1 ORDER by first_name";
	
	
	
	$roomsQuery = "SELECT id, name FROM cabinet";
	
	
	/* unknown use */
	$userfields = array('fio', 'otdel', 'company', 'dept', 'prof', 'net_name', 'invent_num', 'ip', 'mac', 'login', 'email', 'regdate', 'nroom', 'int_phone', 'ext_phone', 'icq');

	/* use */
	$states = array("Открыта", "Принята", "Выполнена");
	$priors = array('Низкий', 'Средний', 'Высокий', 'Критично');
	$months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря', );
	$smonth = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь', );
	
?>