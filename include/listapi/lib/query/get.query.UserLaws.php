<?php

	/* ещё почту, для отправки им сообщений */
	function getQueryToGetUserLaws($listLaw){
		
		return "SELECT $listLaw, u.id, r.email, u.role, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.post 'prof' FROM law as l LEFT JOIN users as u ON u.role = l.uid LEFT JOIN role as r ON r.id = u.role";
		
		/*  .= " WHERE u.role = ".$uid; */
		
		/* например уогда нужно узнать кто оброадает тем или иным правом */
		
		/*  .= " WHERE l.nameField = ".1 */
	}
?>