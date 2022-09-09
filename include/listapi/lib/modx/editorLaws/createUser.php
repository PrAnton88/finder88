<?php

	/* пользователь, который попадёт сюда, по умолчанию не будет иметь никаких прав, кроме того что сможет авторизоваться в modx - больше ничего он не сможет */
	/* потому что попадает только в группу 'Default' */

	if($count > 0){
		// throw new ErrorException(" Пользователь с таким именем уже существует");
		
		
		// $user = $modx->getUser($dataUser['login']);
		
		$user = $modx->getObject('modUser', array('username' => $login));
	}else{
		
		// $anyPassword = 'password';// который отправится пользователю по почте
		$anyPassword = $login;// который отправится пользователю по почте
		
		$user = $modx->newObject('modUser');
		// задаем имя пользователя и пароль
		$user->set('username', $login);
		// $user->set('password', '1234567890');
		$user->set('password', $anyPassword);
		// сохраняем
		$user->save();

		// создаем профиль
		$profile = $modx->newObject('modUserProfile');
		// инициализируем поля
		$profile->set('fullname', $dataUser['fi']);
		$profile->set('email', $dataUser['email']);
		// добавляем профиль к пользователю
		$user->addOne($profile);

		// сохраняем
		$profile->save();
		$user->save();
		
		
		// и в Default группу
		$groupsList = array('Default');
		$groups = array();
		foreach($groupsList as $groupName){
		  // получаем группу по имени
		  $group = $modx->getObject('modUserGroup', array('name' => $groupName));
		  // создаем объект типа modUserGroupMember
		  $groupMember = $modx->newObject('modUserGroupMember');
		  $groupMember->set('user_group', $group->get('id'));
		  $groupMember->set('role', 2); // 1 - это членство с ролью Member
		  $groups[] = $groupMember;
		}

		// добавляем пользователя в группы
		$user->addMany($groups);
		$user->save();
		
	}
?>