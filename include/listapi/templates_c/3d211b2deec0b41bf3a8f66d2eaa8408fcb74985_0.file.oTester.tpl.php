<?php
/* Smarty version 3.1.36, created on 2021-07-22 09:54:45
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\oTester.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60f92445405164_31537171',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3d211b2deec0b41bf3a8f66d2eaa8408fcb74985' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\oTester.tpl',
      1 => 1626937119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60f92445405164_31537171 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>

if(typeof(oTester) === "undefined"){

	var oTester = null;
}

/* пример использования 

	// getTester получает форму, и после инициализации объекта oTester, 
	// в функцию которая передана аргементом, передаёт сам oTester, который хранится в скриптах в файле-контейнере для тестов

getTester(function(oTester){

	oTester.set.pause(true); // тогда каждая итерация теста запускается кнопкой на форме
	oTester.set.timepause(70);

	oTester.set.listRunner([
		{
			name:'некоторые операции 1',
			func:function(funcNext){
				
				// некоторые операции
				funcNext();
			}
		},
		{
			name:'некоторые операции 2',
			func:function(funcNext){
				
				// некоторые операции
				funcNext();
			}
		}
	]);

	oTester.run();
});

*/


thrower(function(cException){
	oTester = (function(){
	
		let htmlForm = fget('formtoTester')[0];
		let placeOperations = htmlForm.querySelector('div.flaglistOperation');
		let timepause = false;
		let pause = false;
	
		let k = 0;
		let item = null;
		
		let successSpan = null;
		
		function setItemStat(classStatus,note){
			
			thrower(function(uException){
				item = placeOperations.querySelectorAll('p');
				item = item[k-1];
				
				console.log(item.textContent + ' ' + note);
				
				item.classList.toggle(classStatus);
				item.classList.toggle('m2');
				item.classList.toggle('text-white');
				
				successSpan = fcr('text','right '+(timepause?'op0':''));
				successSpan.textContent = note;
				
				setTimeout(function(){
					successSpan.style.opacity = 1;
				},0);
				
				item.appendChild(successSpan);
			});
		}
		
		function printDanger(ex,funcNext){
			/* операцию на которой произошла ошибка отметим красным */

			thrower(function(uException){
				setItemStat('bg-danger','Провал');
				
				fhelper(item,ex.message);
				
				/*
				thrower(function(uException){
					throw new uException(ex);
				});
				*/
				printLog(ex);
			
				lastSuccess = false;
				if(issetFunc(funcNext)){
					funcNext();
				}
			
			
			});
		}
		
		lastSuccess = true;
		function iterator(){
			try{
				if(k < listRunner.length){
					
					if(k>0){
						/* отметим предыдущую строку как успешно пройденную */
						if(lastSuccess){
							setItemStat('bg-success','Успех');
						}
					}
					
					lastSuccess = true;
					
					if(pause){
						/* добавим к предыдущей записи "кнопку" ">" */
						
						item = placeOperations.querySelectorAll('p');
						item = item[k];
					
						pause = getBtn(">",function(){
							/* для выполнения и перехода к следующему шагу */
							
							if(timepause){
								setTimeout(function(){ 
									try{
										pause = pause.remove();
										pause = true;
										item.func(iterator);
									}catch(ex){
										printDanger(ex,iterator);
									}
									
								},timepause);
							}else{
								pause = pause.remove();
								pause = true;
								item.func(iterator);
							}
						
						
						});
						item.appendChild(pause);
					}
					
					item = listRunner[k++];
					
					if(!pause){
						if(timepause){
							setTimeout(function(){ 
								try{
									item.func(iterator);
								
								}catch(ex){
									printDanger(ex,iterator);
								}
								
							},timepause);
						}else{
							item.func(iterator);
						}
					}
					
					
				}else{
					if(lastSuccess){
						setItemStat('bg-success','Успех');
					}
					
					item = fcr('p','tr p2 m2 '+(timepause?'op0':''));
					item.textContent = 'Тест пройден';
					placeOperations.appendChild(item);
					setTimeout(function(){
						item.style.opacity = 1;
					},0);
					
				}
				
				
			}catch(ex){
				printDanger(ex,iterator);
			}
		}
	
	
		let listRunner = [];
		
		
		/* oTester. */
		return {
			htmlForm:htmlForm,
			close:function(){
				if(this.htmlForm){ this.htmlForm.remove();
				}
			},
			set:{
				listRunner:function(newlistRunner){
					listRunner = newlistRunner;
					
					/* открываем форму, и записываем строки о том какие операции */
					/* будем совершать */
					
					listRunner.map(item => {
						successSpan = fcr('p','tr p2 ');
						successSpan.textContent = item.name+' ... ';
						successSpan.style.marginLeft = "2px";
						placeOperations.appendChild(successSpan);
					});
					
					
				},
				timepause:function(pause){
					
					timepause = parseInt(pause);
					
					if(timepause < 1){
						customMess('timepause установлен неверно');
						timepause = false;
					}
				},
				pause:function(p){
					pause = p;
				}
			},
			run:function(){
			
				console.log(listRunner);
			
				if(!(is_array(listRunner) && (listRunner.length>0))){
					this.close();
					throw new Error('listRunner is invalid');
				}
				iterator();
			}
		}
		
	})();

});/* << thrower */



<?php echo '</script'; ?>
>

<div class="mAuto r6 fs14 Tahoma tr formtoTester brBox z55" style="width: 655px;">
	<div class="left bgWhite m5 p5 r3 block flaglistOperation" style="width: 350px;">
		
	</div>
</div>

<?php }
}
