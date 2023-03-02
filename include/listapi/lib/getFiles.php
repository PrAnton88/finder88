<?php

try{
	
	function getFiles($modx,$db,$ncontext){
	
		$query = "SELECT i.id, i.image, i.path as imagePath FROM images_context as ic LEFT JOIN images as i ON i.id = ic.nimage WHERE ic.ncontext =".$ncontext;
		$images = $db->fetchAssoc($query,true);
		if((is_string($images)) && (strpos($images,"error") !== false)){
			throw new Error("SQL Error when checking images");
		}
		
		$listFiles = array();
		$type = "";
		
		if(count($images) > 0){
		
			$modx->resource = $modx->getObject('modResource',45);
			
			$i = 0;
			while($i < count($images)){
				
				$file = $images[$i];
				
				 /* выбор миниатюры из кеша если есть, иначе создаёт миниатюрюру если это изображение */
				/* $icon = $modx->runSnippet('phpthumbof',
					array(
						'input'=>$_SERVER['DOCUMENT_ROOT'].$file['imagePath'].$file['image'],
						'options'=>'&w=50&h=50&zc=1'
					)
				);*/
				
				$file['imagePath'] = substr($file['imagePath'],1);
				// $icon = false;
				// if(!$icon){
					/* не картинка */
					
					$pos = strrpos($file['image'],'.');
					$fileExtension = substr($file['image'],$pos+1);
					
					if(in_array($fileExtension, array('gif','jpg','jpeg','png'))){
						$type = 'image';
					}else{
						$type = 'file';
					}
					
					/*
					$icon = 'assets/images/icons/Wildcoder.files/'.$fileExtension.'.png';
					if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/icons/Wildcoder.files/'.$fileExtension.'.png')){
						$icon = 'assets/images/icons/Wildcoder.files/Other.png';
					}
					*/
					$icon = 'assets/images/icons/arbuzova.files/'.$fileExtension.'.tpl';
					if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/icons/arbuzova.files/'.$fileExtension.'.tpl')){
						$icon = 'assets/images/icons/arbuzova.files/Other.tpl';
					}

					/* в файл $icon поместить содержимое само же $icon */
					$icon = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$icon);
					if(!$icon){
						throw new ErrorException('file_get_contents is empty');
					}

					$name = $file['image'];
					$tmp = explode('.',$name);
					if(count($tmp) > 2){
						$name = $tmp[0].'.'.$fileExtension;
					}

				
					$listFiles[] = array('id'=>$file['id'],'icon'=>$icon,'type'=>$type,'name'=>$name,'link'=>$file['imagePath'].$file['image']);
					
				
				
				$i++;
			}
			
			
		}
		
		return $listFiles;
	}
		
}catch(Error $ex){
	
	throw $ex;
}