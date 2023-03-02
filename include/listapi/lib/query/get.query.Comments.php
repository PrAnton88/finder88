<?php

	$queryGetComment = "Select c.*, ic.ncontext, UNIX_TIMESTAMP(c.date_reg) as date_reg, u.id as uid, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', i.image as avatar, i.path as avatarPath From `comments` as c  LEFT JOIN `users` as u ON u.role = c.user_link LEFT JOIN images_context as ic ON ic.ncontext = u.id LEFT JOIN images as i ON i.id = ic.nimage ";

	$queryGetChangeInfoComment = "Select id,request,user_link, UNIX_TIMESTAMP(date_reg) as date_reg, date_reg as date_reg_timestump, UNIX_TIMESTAMP(date_remove) as date_remove, date_remove as date_remove_timestump, hidden,parent From `comments` ";

?>