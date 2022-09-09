<?php

	$group = $modx->getObject('modUserGroup', array('name' => $groupName));
	// echo $group->id."<br />";

	$query = " SELECT * FROM modx_member_groups WHERE user_group=".$group->id." AND member = ".$user->id;
	$check = $db->fetchFirst($query);
	if(is_string($check) && (strpos($check,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	if(count($check) == 0){
		$check = false;
	}else{
		$check = true;
	}
	
	if($check){
		/* убираем, и возможно сообщение о дизактивации права на редактирование новостей */
		$user->leaveGroup($groupName);
		
		$description = 'Удален из группы '.$groupName;
	}else{
		/* добавляем в группу на редактирование новостей, и возможно - оповещаем пользщователя */
		
		$groups = array();
		
		  // получаем группу по имени
		  $group = $modx->getObject('modUserGroup', array('name' => $groupName));
		  // создаем объект типа modUserGroupMember
		  $groupMember = $modx->newObject('modUserGroupMember');
		  $groupMember->set('user_group', $group->get('id'));
		  $groupMember->set('role', 2); // 1 - это членство с ролью Member, 2 - Super User
		  $groups[] = $groupMember;
		
		// добавляем пользователя в группы
		$user->addMany($groups);
		
		$description = 'Добавлен в группу '.$groupName;
	}
	
	$user->save();
	
?>