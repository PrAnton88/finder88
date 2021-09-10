<?php
/* Smarty version 3.1.36, created on 2021-07-21 13:35:24
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\formBronDevices.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60f8067c2d8629_60054356',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5271df566f55b18880fad578ac0d27889b133def' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\formBronDevices.tpl',
      1 => 1626337481,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60f8067c2d8629_60054356 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>

if(typeof(oFormtoBroneDevices) === "undefined"){

	var oFormtoBroneDevices = null;
}

thrower(function(cException){
	oFormtoBroneDevices = (function(){
	
		let htmlForm = fget('formtoBroneDevice');
		let sectionMain = fget('sectionMain');
		/* let listAllDevices = new oListUniq(); */
	
		/* да, можно сделать покороче если ввести дополнительные переменные */
		function getElementDevice(device,funcCheckDEvice){
			/* device.name,device.count,device.description */
		
			let el = fd('w100 oneDev posRel r3 tr left crPoint');
				let dot = fd("left"); dot.style.width = "20px"; dot.style.height = "19px";
				el.appendChild(dot);
				
				dot = fcr('input');
				dot.type = 'checkbox';
				dot.addEventListener('click',function(){
					device.count = inp.value;
					funcCheckDEvice(device);
				});
				el.appendChild(dot);
				
				/* потому что пусть публичное будет, выбор устройств - не приватная функция */
				device.check = function(с){
					/* test */
					inp.value = с;
					
					/* пусть пока так... не надёжная операция */
					el.querySelector('input').click();
				}
				
				
				dot = fcr('text');
				dot.textContent = ' '+device.name;
				fhelper(dot,device.description,true);
				
				
				el.appendChild(dot);
				
				dot = fd("right");
				el.appendChild(dot);
				
				dot = el.querySelector('div.right');
				dot.appendChild(fd("right m3"));
				dot = dot.querySelector('div.right.m3'); dot.style.marginTop = '0px';
				dot.textContent = ' из '+device.count;
				
				dot = fgetParent(dot);
				let inp = fcr('input','m3 right tr r3 count');
				inp.type = 'text';
				inp.value='1'; inp.size='2';
				inp.style.backgroundColor = 'rgb(200, 226, 201)'; 
				inp.style.height = '19px'; inp.style.marginTop = '0px';
				
				let max = device.count;
				inp.addEventListener('keyup',function(){
					/* исп max потому что device.count уже может иметь другое значение (переписанное) */
					keyInt(this,max,1);
					
					funcCheckDEvice(device,this.value);
				});
				blurInt(inp);
				
				dot.appendChild(inp);
				
			return el;
		}
	
	
		/* funcNext - что бы выполнять дальнейшие действия гарантированно после загрузки данных о оборудовании */
		function toggleOpenType(item,funcNext){
			console.log(item);
				
			checkLoadDevices(item,function(item){
				
				if(item.opened){
					item.dom.off();
					item.opened = false;
					item.dom.querySelector('text').textContent = '+';
				}else{
					item.dom.on();
					item.opened = true;
					item.dom.querySelector('text').textContent = '-';
				}
				
				item.dom.querySelector('text').classList.toggle('res');
				item.dom.querySelector('text').classList.toggle('p1_7');
				item.dom.querySelector('text').classList.toggle('add');
				item.dom.querySelector('text').classList.toggle('p1_5');
				
				if(issetFunc(funcNext)){ funcNext(); }
			});
			
		}
	
		function checkLoadDevices(item,funcLoaded){
			if(item.listDevices == 0){
				/* сначала загружаем устройства */
				// item.draw(/* список устройств */);
				
				/*
				// testData
				item.draw([
					{id:1,name:'test 1',count:2},
					{id:2,name:'test 2',count:3},
					{id:3,name:'test 3',count:1}
				]);
				
				// а потом вызываем funcLoaded
				funcLoaded(item);
				*/
				
				dataReqNext({file:urlServerSide+'get.data.ListDevices.php',args:'type='+item.id,type:'json'},
				function(responseDevices){

					if(responseDevices){
						/*
						listAllDevices.toggle(responseDevices.listData);
						*/
						item.draw(responseDevices.listData);
						funcLoaded(item);
					}
				});
				
				
				
				
			}else{
				funcLoaded(item);
			}
		}
	
		function checkBusyDevicesInForm(){
			/*
			'<div class="posRel left" style="margin-left: 7px; margin-bottom: 5px;">\
					<input type="checkbox">Отображать недоступные\
				</div>';
			*/
			let dot = fd('posRel left');
			dot.style.marginLeft = '7px'; dot.style.marginBottom = '5px';
		
			
			dot.appendChild(fcr('input'));
			dot = dot.querySelector('input');
			dot.type = 'checkbox';
			dot.addEventListener('click',function(){
				
				/* пройтись по всем открытым типам */
				/* и добавить в них блоки с информацией о занятых устройствах */
				/* но прежде, необходимо сначала выбрать занятые устсройства каждого типа */
				
			});
			dot = fgetParent(dot);
			dot.appendChild(ftextNode(' Отображать недоступные'));
			
			return dot;
		}
	
		let everyType = Array.from(sectionMain.querySelectorAll('.left.dType'));
		
		var listEveryTypeRecords = new oListUniq();
		listEveryTypeRecords.update(<?php echo $_smarty_tpl->tpl_vars['messagesJson']->value;?>
);
		
		let i = 0;
		listEveryTypeRecords.get().map(item => {
			item.dom = everyType[i];
			
			let container = null;
			
			item.dom.off = function(){
				
				fgetParent(this).querySelector('.left.dModel').style.display = "none";
			};
			item.dom.on = function(){
				fgetParent(this).querySelector('.left.dModel').style.display = "";
			}
			
			item.opened = false;
			item.listDevices = [];
			item.checkedDevices = new oListUniq();/* .toggle({id:4,name:'sfasfsfd',count:5}); */
			
			item.draw = function(listDevices){
				/* 1 */
				this.listDevices = this.listDevices.concat(listDevices);
				
				/* 2 */
				/* добавим в fgetParent(this).querySelector('.left.dModel') - элементы */
				
				container = fgetParent(this.dom).querySelector('.left.dModel');
				
				for(let device of this.listDevices){
				
					/* container.innerHTML += getHtmlDevice(device.name,device.count,device.id); */
					
					/* у каждого device должен быть метод check */
					
					
					container.appendChild(
						getElementDevice(device,function(checkDevice,newCount){
						
							
							if(!newCount){
								item.checkedDevices.toggle(checkDevice);
							}else{
							
								/* но при изменении количества .update(obj) */
								/* проверим есть ли отмечанная такая запись */
								
								let k = item.checkedDevices.get().filter(el => el.id == checkDevice.id );
								
								if(k.length > 0){
								
									checkDevice.count = newCount;
									item.checkedDevices.update(checkDevice);
								
									
								}
								
								console.log(item.checkedDevices.get());
								
							}
							
							
						})
					);
					
				}
				
				if(this.listDevices.length == 0){
					container.innerHTML = '<div class="w100 posRel left" style="color: rgb(44, 187, 15);">Свободных устройств этого типа нет, возможно они заняты</div>';
				}
				
				
				
			}
			
			
			item.dom.querySelector('text').addEventListener('click',function(){
				toggleOpenType(item);
			});
			
			
			listEveryTypeRecords.update(item);
			
			i++;
		});
		
		
		sectionMain.appendChild(
			checkBusyDevicesInForm()
		);
		
		
		/* oFormtoBroneDevices. */
		return {
			getCheckedDevices:function(){
				
				let checkedList = []; 
				listEveryTypeRecords.get().map(item => {
					if(item.listDevices.length > 0){
						checkedList.push({
							id:item.id,name:item.name,checkedDevices:item.checkedDevices.get()
						});
					}
				});
				
				return checkedList;
			},
			load:function(listLoadDevices){
				
				/*
					.load([
						{id: "73", name: "Аккумулятор", checkedDevices:[{id: "282", name: "типоразмер ААА", count: "5", description: ""}]},
						{id: "74", name: "Зарядное устр-во", checkedDevices:[{id: "305", name: "с Micro-USB разъемом", count: "3"}]}
					]);
				*/
				
				/* 1. - раскрыть выбранные типы */
				/* 2. - раскрыть выбранные типы */
			
			
				listLoadDevices.map(loadDevice => {
				
					let types = listEveryTypeRecords.get(loadDevice.id);
					types.map(type => {
						
						toggleOpenType(type,function(){
							
							/* идём по type.listDevices */
							for(let d of type.listDevices){
								/* ищём совпадения с элементами loadDevice.checkedDevices */
								for(let l of loadDevice.checkedDevices){
									if(l.id == d.id){
										d.check(l.count);
									}
								}
								
							}
					
						});
						
					});
					
				});
			
			},
			listRecords:listEveryTypeRecords.get(),
			htmlForm:htmlForm,
			close:function(){
				if(htmlForm){ htmlForm.remove();
				}
			}
		}
		
	})();

});/* << thrower */



<?php echo '</script'; ?>
>

<div class="posRel r6 p8 noSelect z55 op0 tr02 newGradient left m5" id="formtoBroneDevice"
	style="background: -webkit-linear-gradient(top, rgb(232, 236, 243), rgb(54, 128, 173));">
	<div class="mAuto r6 fs14 Tahoma tr formBrone brBox z55" style="width: 655px;">
		<div class="left bgWhite m5 p5 r3 block" style="width: 350px;">
			<div class="w99_5 left" id="idHeadertoBroneDevice">
				<div class="w50 left">
					<p class="center fw600">Тип оборудования</p>
				</div>
				<div class="w50 left">
					<p class="center fw600">Количество</p>
				</div>
			</div>
			<div class="w100 left" id="sectionMain">
				<div class="left mT3 mB3">
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'message', false, NULL, 'cy', array (
));
$_smarty_tpl->tpl_vars['message']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
$_smarty_tpl->tpl_vars['message']->do_else = false;
?>
				
					<div class="w100 left p10 lightBlue brBox contTypeModel">
						<div class="left dType">
							<text class="btnDef left gradient crPoint tahoma fs11 dtoShow posRel add p1_5">+</text>
							<div class="posRel left ml3p"><?php echo $_smarty_tpl->tpl_vars['message']->value['name'];?>
</div>
						</div>
						<div class="w100 left dModel tr"></div>
					</div>
					<hr class="m0 right w100">
					<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				
				</div>
			</div>
		</div>
		<div class="left bgWhite m5 p5 r3 block" style="width: 260px;">
			<div class="w100 left">
				<p class="center fw600">Комментарий</p>
			</div>
			<div class="w100 left">
				<textarea class="h100 w100" placeholder="Примечание" style="max-width: 100%; height: 352px;"></textarea>
			</div>
		</div>
		<div class="left" style="width: 260px;">
			<div class="left" id="sectionHandComplete">
				<text class="btnDef left m5 gradient crPoint p5 tahoma fs11 ">Очистить всё</text>
				<text class="btnDef left m5 gradient crPoint p5 tahoma fs11 ">Сохранить</text>
				<text class="btnDef left m5 gradient crPoint p5 tahoma fs11 " style="width: 58px;">Отмена</text>
			</div>
		</div>
	</div>
</div>
<?php }
}
