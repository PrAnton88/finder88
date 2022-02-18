<?php

	function getQueryToGetTickets(){
		
		return "SELECT a.id, a.applicant, unix_timestamp(a.opened) as 'opened', a.closed, a.time_sub 'sred',
    	a.header, a.message, a.state, a.priority 'prior', a.user_link as 'assd', a.type 'ctype',
    	a.device 'cid', a.auto, a.act, r.email, ra.priority as 'applicantPrior', 
    	concat_ws(' ', b.last_name, b.first_name, b.patronymic) 'ass',
    	b.id 'assid', cabAppl.name 'aroom', b.int_phone 'ainp', b.ext_phone 'aexp',
    	concat_ws(' ', c.last_name, c.first_name, c.patronymic) 'req', c.id 'reqid',
    	cabAssd.name 'room', dpt.name as dept, c.int_phone 'inp', c.ext_phone 'exp', deptAssd.name as deptAssd
    	FROM request as a
    	LEFT JOIN users as b ON a.user_link=b.id 
    	LEFT JOIN users as c ON c.id=a.applicant
    	LEFT JOIN place1 as d ON b.place=d.id 
    	LEFT JOIN cabinet as cabAppl ON cabAppl.id=d.cabinet 
    	LEFT JOIN place1 as e ON e.id=c.place 
    	LEFT JOIN cabinet as cabAssd ON cabAssd.id=e.cabinet 
    	LEFT JOIN dept as dpt ON dpt.id=e.dept 
    	LEFT JOIN dept as deptAssd ON deptAssd.id=a.deciv_dept
    	LEFT JOIN role as r ON r.id=a.user_link 
		LEFT JOIN role as ra ON ra.id=a.applicant ";
	}
	
	/* request.user_link = user.id ответственного */
	/* ass - его фио  */
	
	
	
	/* request.applicant = user.id заявителя */
	/* req - его фио */
?>