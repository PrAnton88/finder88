<?php
header('Content-type:text/html');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

try{

		require_once "../../error.reporting.php";


		require_once("../../../config/connDB.php");/* get $db */
		
		/* getUserData(&$db,&$uid,$where) */
		/* getUserDataWithoutOptions(&$db,&$uid,$where) */
		/*require_once "../../queryUserData.php";
		
		$uid = "842";
		$user = getUserData($db,$uid,"u.id=842");
		*/
		
		require_once "../../config.modx.php";
		/* get $modx */
		
		require_once "../../config.smarty.php";
		/* get $smarty */
		
		$modx->smarty = $smarty;
		
		
		if(empty($_POST['datakey'])){
			throw new ErrorException('datakey is empty');
		}
		
		$datakey = html_entity_decode(htmlspecialchars($_POST['datakey']));
		if($datakey !== md5('{author:"prudnikovanton88@mail.ru",keyfor:"kng"}')){
			throw new ErrorException('datakey is invalid');
		}
		
		
		/*
		$smarty->assign("admin",($user['priority'] == 3));
		$smarty->assign("user",$user);
		*/
		// $smarty->assign("messages",$resultTimeBusy);

		// print_r($smarty);
		return $smarty->display('lib.Vume.tpl');
		
	

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>