<?php

	if(!isset($path)){ $path = ""; }
	require_once $path."fmail.php";

class mime_mail {
	var $parts;
	var $to;
	var $from;
	var $headers;
	var $subject;
	var $body;

// создаем класс
	function mime_mail() {
		$this->parts = array();
		$this->to =  "";
		$this->from =  "";
		$this->subject =  "";
		$this->body =  "";
		$this->headers =  "";
	}

// как раз сама функция добавления файлов в мыло
	function add_attachment($message, $name = "", $ctype = "application/octet-stream") {
		/*echo "получено $name <br />";*/
		/*что бы не дублировалась отправка в комагент*/
		/*$this->parts = array();*/
		
		$this->parts [] = array (
			"ctype" => $ctype,
			"message" => $message/*,
			"encode" => $encode*/,
			"name" => $name
		);
	}

// Построение сообщения (multipart)
	function build_message($part) {
		$message = $part["message"];
		$message = chunk_split(base64_encode($message));
		$encoding = "base64";
		return "Content-Type: ".$part["ctype"].($part["name"]? "; name = \"".$part["name"]."\"" : "")."\nContent-Transfer-Encoding: $encoding\n\n$message\n";
	}

	function build_multipart() {
		$boundary = "b".md5(uniqid(time()));
		$multipart = "Content-Type: multipart/mixed; boundary = $boundary\n\nThis is a MIME encoded message.\n\n--$boundary";

		//for($i = sizeof($this->parts)-1; $i>=0; $i--){
		for($i = sizeof($this->parts)-1; $i>=0; $i--){
			$multipart .= "\n".$this->build_message($this->parts[$i]). "--$boundary"; 
		}
		return $multipart.=  "--\n";
	}

// Посылка сообщения, последняя вызываемая функция класса
	public function send(){
		
		$mime = "";
		//if (!empty($this->from)) $mime .= "From: ".$this->from. "\n";
		if (!empty($this->headers)) $mime .= $this->headers. "\n";
		if (!empty($this->body)) $this->add_attachment($this->body, "", "text/html; charset=windows-1251");  
		$mime .= "MIME-Version: 1.0\n".$this->build_multipart();
		//echo $mime;
		//mail($this->to, $this->subject, "", $mime);
		$mail = new Mail();
		$mail -> to = $this->to;
		$mail -> subject = $this->subject;
		//$mail -> msg = "test";
		$mail -> rigorous_email_check = 0;
		$mail -> contenttype = false;
		$mail -> to_ = false;
		//$mail -> headers = "Content-type: text/html; charset=windows-1251 \r\n";
		$mail -> msg = $mime;
		
		//if(!$messToLog) $messToLog = $this->parts[0]["message"];
		try{
		 
			/* в случае ошибки вернёт true или сгенерирует исключение */
			if(!($mail->adv_send())){
				//echo "Успешно отправлено на ".$this->to;
				return true;
			}else{
				//echo "Ошибка отправки на ".$this->to;
				throw new Exception('level error: mattach.php. $mail->check_fields() failed. \n');
				return false;
			}
		
		} catch (Exception $e){
			throw new Exception('level error: mattach.php. $mail->check_fields() valid. Ошибка ниже к уровню сокет соединения. '.$e->getMessage());
		}
		
	}
}

?>