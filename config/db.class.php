<?php

class DB
{
	var $connection;
	var $queryCounter;
	var $userid;
	var $queryTime = 0;
	var $newFileName = "";
	public function __construct()
	{
		
		// global $db_host, $db_name, $db_login, $db_pass;
		$this->queryCounter = 0;
		$conn = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASS) or die("Информационный портал временно недоступен. Попробуйте позже.");

		mysqli_select_db($conn,DB_NAME) or die ('Can\'t use : ' . mysqli_error($conn));
		//mysql_query('SET CHARSET cp1251');
		//mysql_query('SET CHARSET utf8');
		
		mysqli_query($conn,"SET NAMES 'utf8'",MYSQLI_STORE_RESULT); 
		mysqli_query($conn,"SET CHARACTER SET 'utf8'",MYSQLI_STORE_RESULT);
		mysqli_query($conn,"SET SESSION collation_connection = 'utf8_general_ci'",MYSQLI_STORE_RESULT);
		
		$this->connection = $conn;
	}
	
	function closeConnection(){
		mysql_close(@$this->connection);
	}
	
	function reConnection(){
		mysqli_free_result();
		$this->closeConnection();
		
		
		//$this->DB();
	}
	
	/*просто в случае когда через ajax то передать вторым параметром $uid пользователя*/
	/*именно так определим что запрос из ajax*/
	function query($query,$ajaxUid=false,$returnErr=true)
	{
		//if((strpos($query,"iVersion"))!== false) echo $query."<br />";
		
		/*$returnErr - возвращать ошибку*/
		/*не возвращать если запрос был вызван из ajax, что бы 
			ошибка базы не вылезла пользователю
		*/
		/*точнее не выдавать пользователю сообщ об ошитбке если оно в запросе на запись уже об ошибке,
			что бы не получилось нечто вроде замкнутого круга
		*/
		global $smarty;
		
		if(function_exists("getmicrotime"))
			$start_time = getmicrotime();
		$result = mysqli_query($this->connection,$query);
		//if(DEBUG && !preg_match("/events/",$query)) debug(FILE,27,"MySQL запрос $query",2);
		if(function_exists("getmicrotime"))
			$this->queryTime += getmicrotime()-$start_time;
		$this->queryCounter++;
		
		
		if(mysqli_errno($this->connection)){
			if($ajaxUid && $returnErr){
				/*ошибку записать в базу данных*/
			
				/*убрать все одинарные кавыйки из текста запроса*/
				$qr = str_replace("'","",(mysqli_error($this->connection)." in $query"));
				//$qr .= " .file= ".$_SERVER['SCRIPT_NAME'].". ";
				//$qr = "INSERT INTO `web_err_js_main` (role,messerr) VALUES ($ajaxUid,'$qr')";
				
				/*но не выдывать пользователю*/
				//return ((($this->query($qr,$ajaxUid,false) === true)?" line DB: ".mysql_insert_id():"")." this error from query to DataBase");
			
				return "error: $qr";
			
			
			}elseif($returnErr){
				trigger_error(mysqli_error($this->connection)." inee ". $query);
			}
			//trigger_error(mysql_error()." inee ". $query);
			//echo " printError $query";
			$result = false;
		}
		
		return $result;
	}
	
	function fetchAssoc($query,$ajaxUid=false)
	{
		//echo $query."<br />";
		$res = array();
		$result = $this->query($query,$ajaxUid);
		
		//echo is_string($result)." \n";
		if((is_string($result)) and (strpos($result,"error") !== false)) return $result;
		if(!$result || mysqli_affected_rows($this->connection)<1) return $res;
		
		
		while($row = mysqli_fetch_assoc($result)){
			$res[] = $row;
		}

		return $res;
	}
	
	function fetchFirst($query,$ajaxUid=false)
	{
		$result = $this->query($query,$ajaxUid);
		if((is_string($result)) and (strpos($result,"error") !== false)) return $result;
		if(!$result || mysqli_affected_rows($this->connection)<1) {return array();}
		
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	
	function fetchArrayByFieldName($query, $field_name, $ajaxUid=false)
	{
		if($data = $this->fetchAssoc($query, $ajaxUid)){
			if(strpos($data,"error") !== false) return $data;
			$fields = array();
			foreach ($data as $row){
				$fields[] = $row[$field_name];
			}
			return $fields;
		}
		return false;
	}
	
	function fetchFirstByFieldName($query, $field_name, $ajaxUid=false)
	{
		/*например*/
		/*$count = $db->fetchFirstByFieldName("select count(*) 'c' from `web_err` WHERE id<>99999999999 ","c");*/
		if($data = $this->fetchFirst($query, $ajaxUid)){
			
			/*
			print_r($data);
			
			if(strpos($data,"error") !== false) return $data;
			else return $data[$field_name];
			*/
			return $data[$field_name];
		}
		return false;
	}
	
	/*
		$db->insert("request", array('user_link'=>'NULL', ... 'user_apdate'=>$uid), array('header'=>"когда необходимо экранирование спецсимволов"))
		addslashes() - экранирование спецсимволов
	*/
	
	function insert($table_name, $iparams, $sparams, $ajaxUid=false)
	{
		$query = "insert into `$table_name` ";
		$names = array();
		$values = array();
		if($iparams){
			foreach($iparams as $key=>$value){
				$names[] = "`".$key."`";
				$values[] = $value;
			}
		}
		if($sparams){
			foreach($sparams as $key=>$value){
				$names[] = "`".$key."`";
				//print $value;
				$values[] = "'".addslashes($value)."'";
			}
		}
		$query .= "(".join(",",$names).")values(".join(",",$values).")";
		
		if($result = $this->query($query, $ajaxUid)){
			
			if((is_string($result)) and (strpos($result,"error") !== false)) return $result;
		}
		
		return mysqli_insert_id($this->connection);
	}

	function myInsert($table_name, $iparams, $sparams, $ajaxUid=false){/*old*/
		$newsparams = array();
		for($i=0; $i<count($sparams); $i++){
			$newsparams[$i] = "'".$sparams[$i]."'";
		}
		$query = "INSERT INTO `$table_name` ";
		$query .= "(".join(",",$iparams).") VALUES (".join(",",$newsparams).")";
	
		/*echo "$query <br />";*/
	
		if($query = $this->query($query, $ajaxUid)){
			
			if($ajaxUid && (strpos($query,"error") !== false)) return $query;
		}
		
		return mysqli_insert_id($this->connection);
	}
	
	function myNewInsert($table_name, $sparams, $ajaxUid=false){
		$names = array();
		$values = array();
		foreach($sparams as $key=>$value){
			$names[] = "`".$key."`";
			$values[] = "'".addslashes($value)."'";
		}
		$query = "insert into $table_name ";
		$query .= "(".join(",",$names).")values(".join(",",$values).")";
		
		/*echo $query;*/
		if($query = $this->query($query, $ajaxUid)){
			if($ajaxUid && (strpos($query,"error") !== false)) return $query;
		}
		
		return mysqli_insert_id($this->connection);
	}
	
	function update($table_name, $iparams, $sparams = array(), $cond = "")
	{
		/*Если обращаемся к базе через точку, то имя База.Таблица должно быть не в кавычках а если просто Таблица, то вопрос - обязательно ли в кавычках, как `Таблица`*/
		$expl = explode(".",$table_name);
		
		//echo "<br />";
		//print_r($expl);
		//echo "<br />".count($expl);
		if(count($expl)==1){
			$table_name = "`$table_name`";
		}
		
		$query = "update $table_name set ";
		
		
		$names_vals = array();
		foreach($iparams as $key=>$value){
			$names_vals[] = "`$key`"."=".$value;
		}
		foreach($sparams as $key=>$value){
			$names_vals[] = "`$key`"."="."'".addslashes($value)."'";
		}
		$query .= join(",",$names_vals);
		if($cond){$query .= " where $cond";}
		$this->query($query);
	}
	
	function update1($table_name, $sparams = array(), $cond = "")
	{
		$query = "update `$table_name` set ";
		$names_vals = array();

		foreach($sparams as $key=>$value){
			$names_vals[] = "`$key`"."="."'".addslashes($value)."'";
		}
		$query .= join(",",$names_vals);
		if($cond){$query .= " where $cond";}
		$this->query($query);
	}
	
	function myUpdate($table_name, $iparams, $sparams, $cond){
		
		$query = "UPDATE $table_name set ";
		for($i=0; $i<count($iparams); $i++){
			$query .= " $iparams[$i]='$sparams[$i]'";
			if ($i<(count($iparams)-1)){$query .= ", ";}
		}
		$query .= " WHERE $cond";
		/*echo "<br /> $query <br />";*/
		return ($this->query($query));
	}
	
	function dangerCode($textNote){
		$maskDangerCod = array('script', '<?', '<?php', '<?PHP', '?php', '?PHP', '?>', '<', '>');
		foreach($maskDangerCod as $it){
			$textNote = str_replace($it, ' ', $textNote);
		}
		return $textNote;
	}
	
	function dangerCodeSm($textNote){
		$maskDangerCod = array('script', '<?', '<?php', '<?PHP', '?php', '?PHP', '?>');
		foreach($maskDangerCod as $it){
			$textNote = str_replace($it, ' ', $textNote);
		}
		return $textNote;
	}

	
}
?>