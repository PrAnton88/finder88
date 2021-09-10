<?php
	

    // Функция для чтения ответа сервера. Выбрасывает исключение в случае ошибки
    function read_smtp_answer($socket) {
		
        $read = socket_read($socket, 1024);
       
        if ($read[0] != '2' && $read[0] != '3') {
            if (!empty($read)) {
                throw new Exception('SMTP failed: '.$read."\n");
            } else {
                throw new Exception('Unknown error'."\n");
            }
        }
    }
    
    // Функция для отправки запроса серверу
    function write_smtp_response($socket, $msg) {
        $msg = $msg."\r\n";
        socket_write($socket, $msg, strlen($msg));
    }

  class Mail 
  {
  // создаем переменные, в которых хранится содержимое заголовков
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
  // проверяет допустимость почтовых адресов
  var $rigorous_email_check = true; 
  // проверяет допустимость доменных имен по записям DNS
  var $allow_empty_subject = false; 
  // допустимость пустого поля subject
  var $allow_empty_msg = false; 
  // допустимость пустого поля msg
    
  var $headers = array();   
  /* массив $headers содержит все поля заголовка, кроме to и subject*/
    
  function check_fields()
    /* метод, проверяющий, переданы ли все значения заголовков
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
    // проверяем допустимость почтового адреса      
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
  /* метод отправки сообщения */
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
   
    	$login   = 'helpdesk@nkng.vrn.ru';    // логин к ящику
    	$pwd     = 'NhM1aIlt';    // пароль к ящику
   
    	$from    = 'helpdesk@nkng.vrn.ru';  // адрес отправителя
    	$to      =  $this -> to;  // адрес получателя
   
    	$subject = htmlspecialchars( stripslashes(trim($this -> subject)));       // тема сообщения
    	$message = stripslashes(trim($this -> msg));          // текст сообщения
    	
    	try {
       
			if (!extension_loaded('sockets')) {
				throw new ErrorException('The sockets extension is not loaded.');
			}
	   
			// Создаем сокет
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if ($socket < 0) {
				throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
			}

			// Соединяем сокет к серверу
			//echo 'Connect to \''.$address.':'.$port.'\' ... ';
			$result = socket_connect($socket, $address, $port);
			if ($result === false) {
				throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
			} else {
				//echo "OK\n";
			}
		   
			// Читаем информацию о сервере
			read_smtp_answer($socket);
		   
			// Приветствуем сервер
			write_smtp_response($socket, 'EHLO '.$login);
			read_smtp_answer($socket); // ответ сервера
		   
			//echo 'Authentication ... ';
			   
			// Делаем запрос авторизации
			write_smtp_response($socket, 'AUTH LOGIN');
			read_smtp_answer($socket); // ответ сервера
		   
			// Отравляем логин
			write_smtp_response($socket, base64_encode($login));
			read_smtp_answer($socket); // ответ сервера
		   
			// Отравляем пароль
			write_smtp_response($socket, base64_encode($pwd));
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo "Check sender address ... ";
		   
			// Задаем адрес отправителя
			write_smtp_response($socket, 'MAIL FROM:<'.$from.'>');
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo "Check recipient address ... ";
		   
			// Задаем адрес получателя
			write_smtp_response($socket, 'RCPT TO:<'.$to.'>');
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo "Send message text ... ";
		   
			// Готовим сервер к приему данных
			write_smtp_response($socket, 'DATA');
			read_smtp_answer($socket); // ответ сервера
		   
			// Отправляем данные
			if(($this -> to_))
			$message = "To: $to\r\n\r\n".$message; // добавляем заголовок сообщения "адрес получателя"
			if(($this -> contenttype))
			$message = "Content-type: text/html; charset=windows-1251\r\n".$message; // заголовок Content-type
			$message = "Subject: $subject\r\n".$message; // заголовок "тема сообщения"
			$message = "From: $from\r\n".$message; // заголовок "адрес отправителя"
			//echo $message;
			write_smtp_response($socket, $message."\r\n.");
			read_smtp_answer($socket); // ответ сервера
		   
			//echo "OK\n";
			//echo 'Close connection ... ';
		   
			// Отсоединяемся от сервера
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