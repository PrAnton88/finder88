<?php
    // ������� ��� ������ ������ �������. ����������� ���������� � ������ ������
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
    
    // ������� ��� �������� ������� �������
    function write_smtp_response($socket, $msg) {
        $msg = $msg."\r\n";
        socket_write($socket, $msg, strlen($msg));
    }

  class Mail 
  {
  // ������� ����������, � ������� �������� ���������� ����������
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
  // ��������� ������������ �������� �������
  var $rigorous_email_check = true; 
  // ��������� ������������ �������� ���� �� ������� DNS
  var $allow_empty_subject = false; 
  // ������������ ������� ���� subject
  var $allow_empty_msg = false; 
  // ������������ ������� ���� msg
    
  var $headers = array();   
  /* ������ $headers �������� ��� ���� ���������, ����� to � subject*/
    
  function check_fields()
    /* �����, �����������, �������� �� ��� �������� ����������
    � �������� ������������ �������� ������� */
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
    /* ���� ���� �������������� ���������, �������� �� � ������ $headers*/
    if(!empty($this -> from))
    {
      $this->headers[] = "From: $this -> from";
    }
    if(!empty($this -> reply_to))
    {
      $this -> headers[] = "Reply_to: $this -> reply_to";
    } 
    // ��������� ������������ ��������� ������      
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
  /* ����� �������� ��������� */
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
     
    	$address = 'mail.cosmos.local'; // ����� smtp-�������
    	$port    = 25;          // ���� (����������� smtp - 25)
   
   
    	$login   = 'helpdesk@nkng.vrn.ru';    // ����� � �����
    	$pwd     = 'NhM1aIlt';    // ������ � �����
		
		
		$from    = 'helpdesk@nkng.vrn.ru';  // ����� �����������
    	$to      =  $this -> to;  // ����� ����������
   
		/*
		$login   = 'prudnikov@kng.vrn.ru';    // ����� � �����
    	$pwd     = '';    // ������ � �����
   
    	$from    = 'prudnikov@kng.vrn.ru';  // ����� �����������
    	$to      =  $this -> to;  // ����� ����������
		*/
		
   
    	$subject = htmlspecialchars( stripslashes(trim($this -> subject)));       // ���� ���������
    	$message = stripslashes(trim($this -> msg));          // ����� ���������
    	
    	try {
       
			// ������� �����
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if ($socket < 0) {
				throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
			}

			// ��������� ����� � �������
			//echo 'Connect to \''.$address.':'.$port.'\' ... ';
			$result = socket_connect($socket, $address, $port);
			if ($result === false) {
				throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
			} else {
				//echo "OK\n";
			}
		   
			// ������ ���������� � �������
			read_smtp_answer($socket);
		   
			// ������������ ������
			write_smtp_response($socket, 'EHLO '.$login);
			read_smtp_answer($socket); // ����� �������
		   
			//echo 'Authentication ... ';
			   
			// ������ ������ �����������
			write_smtp_response($socket, 'AUTH LOGIN');
			read_smtp_answer($socket); // ����� �������
		   
			// ��������� �����
			write_smtp_response($socket, base64_encode($login));
			read_smtp_answer($socket); // ����� �������
		   
			// ��������� ������
			write_smtp_response($socket, base64_encode($pwd));
			read_smtp_answer($socket); // ����� �������
		   
			//echo "OK\n";
			//echo "Check sender address ... ";
		   
			// ������ ����� �����������
			write_smtp_response($socket, 'MAIL FROM:<'.$from.'>');
			read_smtp_answer($socket); // ����� �������
		   
			//echo "OK\n";
			//echo "Check recipient address ... ";
		   
			// ������ ����� ����������
			write_smtp_response($socket, 'RCPT TO:<'.$to.'>');
			read_smtp_answer($socket); // ����� �������
		   
			//echo "OK\n";
			//echo "Send message text ... ";
		   
			// ������� ������ � ������ ������
			write_smtp_response($socket, 'DATA');
			read_smtp_answer($socket); // ����� �������
		   
			// ���������� ������
			if(($this -> to_))
			$message = "To: $to\r\n\r\n".$message; // ��������� ��������� ��������� "����� ����������"
			if(($this -> contenttype))
			$message = "Content-type: text/html; charset=windows-1251\r\n".$message; // ��������� Content-type
			$message = "Subject: $subject\r\n".$message; // ��������� "���� ���������"
			$message = "From: $from\r\n".$message; // ��������� "����� �����������"
			//echo $message;
			write_smtp_response($socket, $message."\r\n.");
			read_smtp_answer($socket); // ����� �������
		   
			//echo "OK\n";
			//echo 'Close connection ... ';
		   
			// ������������� �� �������
			write_smtp_response($socket, 'QUIT');
			read_smtp_answer($socket); // ����� �������
		   
			//echo "OK\n";
       
		} catch (Exception $e) {
			//echo "\nError: ".$e->getMessage();
			throw new Exception("\nError: ".$e->getMessage());
		}
   
		if (isset($socket)) {
			socket_close($socket);
		}
		
		/* �������������� ����� ������ */
		//return true;
	}
}
?>