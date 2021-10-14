<?php
    // ‘ункци€ дл€ чтени€ ответа сервера. ¬ыбрасывает исключение в случае ошибки
    function read_smtp_answer($socket) {
        $read = socket_read($socket, 1024);
       
        if ($read{0} != '2' && $read{0} != '3') {
            if (!empty($read)) {
                throw new Exception('SMTP failed: '.str_replace("\r\n"," ",$read));
            } else {
                throw new Exception('Unknown error');
            }
        }
    }
    
    // ‘ункци€ дл€ отправки запроса серверу
    function write_smtp_response($socket, $msg) {
        $msg = $msg."\r\n";
        socket_write($socket, $msg, strlen($msg));
    }

  class Mail 
  {
  // создаем переменные, в которых хранитс€ содержимое заголовков
  var $to = '';
  var $from = '';
  var $reply_to = '';
  var $cc = '';
  //var $to_ = true;
  //var $contenttype = true;
  var $bcc = '';
  var $subject = '';
  var $msg = '';
  var $validate_email = true; 
  // провер€ет допустимость почтовых адресов
  var $rigorous_email_check = true; 
  // провер€ет допустимость доменных имен по запис€м DNS
  var $allow_empty_subject = false; 
  // допустимость пустого пол€ subject
  var $allow_empty_msg = false; 
  // допустимость пустого пол€ msg
    
  var $headers = array();   
  /* массив $headers содержит все пол€ заголовка, кроме to и subject*/
    
  function check_fields()
    /* метод, провер€ющий, переданы ли все значени€ заголовков
    и проверку допустимости почтовых адресов */
  {
    if(empty($this -> to))
    {
      return false;       
    }
    if(!$this -> allow_empty_subject && empty($this -> subject))
    {
      return false;       
    }
    if(!$this -> allow_empty_msg && empty($this -> msg))
    {
      return false;       
    }
    /* если есть дополнительные заголовки, помещаем их в массив $headers*/
    if(!empty($this -> from))
    {
      $this->headers[] = "From: $this -> from";
    }
    if(!empty($this -> reply_to))
    {
      $this -> headers[] = "Reply_to: $this -> reply_to";
    } 
    // провер€ем допустимость почтового адреса      
    if ($this -> validate_email)
    {
       if (!preg_match("/[0-9a-z_]+@[0-9a-z-_^\.]+\.[a-z]{2,3}/i", $this -> to))
       {
          return false;
       }
       return true;
    }
  }

  function send()
  /* метод отправки сообщени€ */
  {
	
     if(!$this -> check_fields()) return true;
     if (mail($this -> to, htmlspecialchars( stripslashes(trim($this -> subject))),
        stripslashes(trim($this -> msg)), htmlspecialchars(trim($this -> headers))))
     {
        return true;
     }else{
        return false;
     }
	
  }
  
  function adv_send()
    {
		
    	if(!$this -> check_fields()) return true;
     
    	$address = 'mail.cosmos.local'; // адрес smtp-сервера
    	$port    = 25;          // порт (стандартный smtp - 25)
   
   
    	$login   = 'helpdesk@nkng.vrn.ru';    // логин к €щику
    	$pwd     = 'NhM1aIlt';    // пароль к €щику
		
		
		$from    = 'helpdesk@nkng.vrn.ru';  // адрес отправител€
    	$to      =  $this -> to;  // адрес получател€
   
		/*
		$login   = 'prudnikov@kng.vrn.ru';    // логин к €щику
    	$pwd     = '';    // пароль к €щику
   
    	$from    = 'prudnikov@kng.vrn.ru';  // адрес отправител€
    	$to      =  $this -> to;  // адрес получател€
		*/
		
   
    	$subject = htmlspecialchars( stripslashes(trim($this -> subject)));       // тема сообщени€
    	$message = stripslashes(trim($this -> msg));          // текст сообщени€
    	
    	try {
       
			// —оздаем сокет
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if ($socket < 0) {
				throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
			}

			// —оедин€ем сокет к серверу
			//echo 'Connect to \''.$address.':'.$port.'\' ... ';
			$result = socket_connect($socket, $address, $port);
			if ($result === false) {
				throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
			} else {
				//echo "OK\n";
			}
		   
			// „итаем информацию о сервере
			read_smtp_answer($socket);
		   
			// ѕриветствуем сервер
			write_smtp_response($socket, 'EHLO '.$login);
			read_smtp_answer($socket); // ответ сервера
		   
			//echo 'Authentication ... ';
			   
			// ƒелаем запрос авторизации
			write_smtp_response($socket, 'AUTH LOGIN');
			read_smtp_answer($socket); // ответ сервера
		   
			// ќтравл€ем логин
			write_smtp_response($socket, base64_encode($login));
			read_smtp_answer($socket); // ответ сервера
		   
			// ќтравл€ем пароль
			write_smtp_response($socket, base64_encode($pwd));
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo "Check sender address ... ";
		   
			// «адаем адрес отправител€
			write_smtp_response($socket, 'MAIL FROM:<'.$from.'>');
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo "Check recipient address ... ";
		   
			// «адаем адрес получател€
			write_smtp_response($socket, 'RCPT TO:<'.$to.'>');
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo "Send message text ... ";
		   
			// √отовим сервер к приему данных
			write_smtp_response($socket, 'DATA');
			read_smtp_answer($socket); // ответ сервера
		   
			// ќтправл€ем данные
			if(($this -> to_))
			$message = "To: $to\r\n\r\n".$message; // добавл€ем заголовок сообщени€ "адрес получател€"
			if(($this -> contenttype))
			$message = "Content-type: text/html; charset=windows-1251\r\n".$message; // заголовок Content-type
			$message = "Subject: $subject\r\n".$message; // заголовок "тема сообщени€"
			$message = "From: $from\r\n".$message; // заголовок "адрес отправител€"
			//echo $message;
			write_smtp_response($socket, $message."\r\n.");
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo 'Close connection ... ';
		   
			// ќтсоедин€емс€ от сервера
			write_smtp_response($socket, 'QUIT');
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
       
		} catch (Exception $e) {
			//echo "\nError: ".$e->getMessage();
			throw new Exception("\nError: ".$e->getMessage());
		}
   
		if (isset($socket)) {
			socket_close($socket);
		}
		
		/* спровоцировать вызов ошибки */
		//return true;
	}
}
?>