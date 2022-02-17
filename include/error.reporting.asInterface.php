<?php

	define('DEBUG', true);
    define('LINEBREAK', "\r\n");
	
	error_reporting(E_ALL);
	
	/* отсюда мы распечатываем и прекращаем дальнейшее выполнение */
	/*function exc_handler_report($exception) {
		$log = $exception->getMessage() . "\n" . $exception->getTraceAsString() . LINEBREAK;
		if ( ini_get('log_errors') )
			error_log($log, 0);
		print("Unhandled Exception" . (DEBUG ? " - $log" : ''));
	}*/

	function exc_handler_report($exception) {
		/* лучше не из sonn, так как внутри getTraceAsString() есть переносы строк */
		return $exception->getMessage() . " " . $exception->getTraceAsString();
	}

	/* отсюда мы выводим информацию об ошибке на место где catch(ErrorException $ex){ и делаем что то с информацией из exc_handler($ex); }*/
	function exc_handler($ex){
		
		$file = $ex->getFile();
		
		
		if(strpos($file,'.php') !== false){
			$file = (explode('.php',$file)[0]);
		}
		
		$file = str_replace('\\','/',$file );
		
		if(strpos($file,'include/listapi/') !== false){
			$file = (explode('include/listapi/',$file)[1]);
		}elseif(strpos($file,'config/') !== false){
			$file = (explode('config/',$file)[1]);
		}
		
		$resp = $file;
		
		$resp .= ', line '. $ex->getLine() .': ';
		
		$resp .= str_replace("\t"," ",str_replace("\r\n"," ",str_replace("\n"," ",str_replace('\\','/',$ex->getMessage() ) ) ) );
		
		return $resp;
	}
	
	/* for multiply exception catch */
	interface GroupException {}
	
	abstract class Merror implements GroupException {
       
        private function __construct() {}
	
		public static function err_handler($errno, $errstr, $errfile, $errline, $errcontext) {
			$l = error_reporting();
			
			if ( $l & $errno ) {
			   
				$fatal = false;
				switch ( $errno ) {
					case E_USER_ERROR:
						$type = 'Fatal Error';
						$fatal = true;
					break;
					case E_USER_WARNING:
					case E_WARNING:
						$type = 'Warning';
						$fatal = true;
					break;
					case E_USER_NOTICE:
					case E_NOTICE:
					case @E_STRICT:
						$type = 'Notice';
					break;
					case @E_RECOVERABLE_ERROR:
						$type = 'Catchable';
					break;
					default:
						$type = 'Unknown Error';
					break;
				}
			   
				/*
				if($fatal){
					throw new \ErrorException($type.': '.$errstr, 0, $errno, $errfile, $errline);
				}
				throw new \WarnException($type.': '.$errstr, 0, $errno, $errfile, $errline);
				*/
				
				throw new \ErrorException($type.': '.$errstr, 0, $errno, $errfile, $errline);
				
			}
			return false;
		}
    }
	
	set_error_handler( 'Merror::err_handler' );
	
	
    // set_exception_handler( 'exc_handler' );
    
	
	
	
	/*
		trigger_error("This event WILL fire", E_USER_NOTICE);
	*/
	
?>