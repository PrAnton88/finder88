<?php
/* Smarty version 3.1.36, created on 2021-09-10 16:39:13
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\formBronConversation.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_613b6001333f56_73143863',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '86349d9014cb408f8ffeb8bda5a95670ff7ee757' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\formBronConversation.tpl',
      1 => 1631280369,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_613b6001333f56_73143863 (Smarty_Internal_Template $_smarty_tpl) {
?> 
<?php echo '<script'; ?>
>


if(typeof(oIniFormBroneConversation) === "undefined"){

	var oIniFormBroneConversation = null;
}

thrower(function(userError){
	oIniFormBroneConversation = (function(){
	
		let htmlForm = fget('formBroneConversation');
	
		let openRecord = null;
		openRecord = <?php if ($_smarty_tpl->tpl_vars['openRecord']->value) {
echo $_smarty_tpl->tpl_vars['openRecord']->value;
} else { ?>false<?php }?>;
		
		let dateApply = <?php if ($_smarty_tpl->tpl_vars['dateApply']->value) {?>"<?php echo $_smarty_tpl->tpl_vars['dateApply']->value;?>
"<?php } else { ?>false<?php }?>;
		/* формат 2021-06-10 */
		
		let listDevices = (<?php echo $_smarty_tpl->tpl_vars['listDevices']->value;?>
);
		
		let timeoptions = {
		  year: 'numeric',
		  month: 'numeric',
		  day: 'numeric',
		  timezone: 'UTC'
		};
		
		let today = new Date().toLocaleString("ru", timeoptions);
		today = today.split(".");
		today = today[2]+'-'+today[1]+'-'+today[0];
		
		
		if(openRecord){/* то что редактируем */
			console.log('openRecord');
			console.log(openRecord);
			
			
			fget('idMeasure').value = openRecord.measure;
			fget('idNote').value = openRecord.note;
			
			
			/* в этом случае редактировать может не только авторизованный, но и администратор */
			/* есть в записи поле .userId */
			/* если редактирует не он, то только админ - проверять */
			
			/* admin || (user.uid == openRecord.userId) */
			
		}else{
			/* новая запись по user.uid авторизованному */
			
		}
		
		
		
		let rangeTimeBusy = <?php if ($_smarty_tpl->tpl_vars['stepsTimeBusy']->value) {
echo $_smarty_tpl->tpl_vars['stepsTimeBusy']->value;
} else { ?>false<?php }?>;
		if(rangeTimeBusy.length == 0){ 
			rangeTimeBusy = false;
		}else{
			let d = [];
			/* помним, что из rangeTimeBusy отняли интервал openRecord, его там нету */
			/* поэтому, смело заодно соединим концы интервалов - если начало следующего равно конку предыдущего */
			/* это позволит сократить количество интервалов (это важно), что бы конец интервала означал либо наличие свободного 
				промежутка либо конец интервала
			*/
			for(let item of rangeTimeBusy){
				item = item.split('-');
				item = {left:item[0],right:item[1]};
				d.push(item);
			}
			
			rangeTimeBusy = d;
			/* то что занято */
			
			
			/* соединим то что начинается на одно и то же значение, что и конец какого то интервала,
			что и было описано выше */
			
			rangeTimeBusy = rangeJoin(rangeTimeBusy);
			
			
		}
		
		
		let acttimer = {left:fget("timerLeft"),right:fget("timerRight"),
			setting:{start:"08:00",stop:"18:00",step:20,rangeTimeBusy:rangeTimeBusy}
		};
		

		ofDivision.setting = {step:acttimer.setting.step};
		/* formBrone.timer */
		
		
		
		
		if(dateApply == today){
			
			console.log('бронирование на текущую дату');
			
			acttimer.setting.current = acttimer.setting.start;
			ofDivision.setting = {step:acttimer.setting.step};
			
			
			today = new oTimeSlider(acttimer.setting);
			ofDivision.getDelta(
				(function(){ 
					let test = new Date();
					test += "";
					return test.substr((test.indexOf(":")-2),5);
				})(),
				today
			); 
			today.next();
			
			today = today.mTime.get();
			
			console.log('стартовое время '+today);
			acttimer.setting.start = today;
			
		}
		
		
		if((!openRecord) && rangeTimeBusy){
			
			/*
			console.log('acttimer.setting.start');
			console.log(acttimer.setting.start);
			*/
			/* acttimer.setting.start находится ли в пределах занятого интервала rangeTimeBusy */
			
			if(acttimer.setting.start >= (rangeTimeBusy[0].left)){
				rangeTimeBusy.forEach(item => {
					
					if((acttimer.setting.start >= item.left) && (acttimer.setting.start < item.right)){
						acttimer.setting.start = item.right;
					}
					
				});
			}
			
			if(acttimer.setting.start == acttimer.setting.stop){
				
				let mess = "Свебодного времени для бронирования на эту дату нет";
				
				let messForm = customMessForm(mess,function(textBtn){
					
					console.log(textBtn);
					messForm.close();
						
				});
				
				messForm.style.maxWidth = "300px";
				
				document.body.appendChild(
					messForm
				);
				
			}
			
		}
		
		
		/* для надписи на форме */
		dateApply = dateApply.split("-");
		dateApply = parseInt(dateApply[2])+' '+nMonth[parseInt(dateApply[1])-1]+' '+dateApply[0];
		
		
		let el = null;
		if(el = fget('dateApply')){
			
			el.innerHTML = dateApply;
		}
		
		
		let formBrone = new oFormBroneConversation(
			{timer:acttimer},
			openRecord
		);
		
		
		/* если дата сегодняшняя, и времени сейчас больше чем 08:00 */
		/* то установите начальное время через */
		/* 
		
		
		let acttimer = {setting:{start:"08:00",stop:"18:00",step:20}};
		acttimer.setting.current = acttimer.setting.start;
		
		ofDivision.setting = {step:acttimer.setting.step};
		
		let today = new oTimeSlider(acttimer.setting);
		
		
		
		ofDivision.getDelta(
			(function(){ 
				let test = new Date();
				test += "";
				return test.substr((test.indexOf(":")-2),5);
			})(),
			today
		); 
		today.next();
		
		today.mTime.get();
		
		*/
		
		
		acttimer.left.querySelectorAll(".act.left")[0].addEventListener("click",function(e){
			try{
				
				formBrone.timer.left.back(e);
			}catch(ex){ printLog(ex); }
		});
		acttimer.left.querySelectorAll(".act.right")[0].addEventListener("click",function(e){
			try{
				
				formBrone.timer.left.next(e);
			}catch(ex){ printLog(ex); }
		});


		acttimer.right.querySelectorAll(".act.left")[0].addEventListener("click",function(e){
			try{
				
				formBrone.timer.right.back(e);
			}catch(ex){ printLog(ex); }
		});
		acttimer.right.querySelectorAll(".act.right")[0].addEventListener("click",function(e){
			try{
				
				formBrone.timer.right.next(e);
			}catch(ex){ printLog(ex); }
		});
		
		/* что бы любые не валидные изменения пальцами в текстовых полях на установку времени - стирались */
		let currentTime = null;
		acttimer.left.querySelector("input").addEventListener('keydown',function(e){
			
			currentTime = this.value.substr(0,2)+':'+this.value.substr(3,2);
		});
		acttimer.right.querySelector("input").addEventListener('keydown',function(e){
			
			currentTime = this.value.substr(0,2)+':'+this.value.substr(3,2);
		});
		acttimer.left.querySelector("input").addEventListener('keyup',function(e){
			
			let valid = ofDivision.getDelta(this.value,formBrone.timer.left);
			if(!valid){
				this.value = currentTime;
			}else{
				
				/* проверить не стали ли поля равны - что нельзя допустить */
				if(formBrone.timer.left.get() == formBrone.timer.right.get()){
					formBrone.timer.right.next();
				}
			}
			
		});
		acttimer.right.querySelector("input").addEventListener('keyup',function(e){
			
			
			let valid = ofDivision.getDelta(this.value,formBrone.timer.right);
			if(!valid){
				this.value = currentTime;
			}else{
				
				/* проверить не стали ли поля равны - что нельзя допустить */
				if(formBrone.timer.right.get() == formBrone.timer.left.get()){
					formBrone.timer.right.next();
				}
			}
			
		});
		
		function saveRecord(funcNextForTesting){
			oIniPageBronСonversation.saveRecord(formBrone.dataForm.get("<?php echo $_smarty_tpl->tpl_vars['dateApply']->value;?>
"),(openRecord?openRecord.id:false),funcNextForTesting);
		}
		
		let el2;
		if(el2 = fget("submitEdBrone")){
			el2.addEventListener('click',function(e){
			
				saveRecord();
				
			});
		}
		
		/* выбранные устройства */
		checkedDevices = new oListUniq();
		
		function getElementDevice(device){
		
			let el = fd();
			let dot = fcr('input'); dot.type = 'checkbox';
			if(device.check == 1){ dot.checked = true; }
			
			dot.addEventListener('click',function(){
			
				checkedDevices.toggle(device);
			});
			el.appendChild(dot);
			
			dot = fcr('label');
			dot.textContent = device.name;
			el.appendChild(dot);
			
			return el;
		}
	
	
		function drawDevices(newListCheckedDevices/* выбранные как checked = true */){
			let container = htmlForm.querySelector('div.flaglistDevices');
		
			if(newListCheckedDevices && (!is_array(newListCheckedDevices))){
				throw new Error('newListCheckedDevices is not array');
			}
			
			container.innerHTML = '<b>Оборудование</b>';
		
			listDevices.map(device => {
				
				if(!device.name){ throw new Error('format data item device is invalid'); }
			
				if(!newListCheckedDevices){
					if(device.check == 1){
					
						checkedDevices.toggle(device);
					}
				}else{
					if(device.check == 1){
						/* убрать */
						device.check = 0;
					}
				
					/* найти в массиве объектов newListCheckedDevices - текущий device */
					
					newListCheckedDevices.map(itemChecked => {
						
						if(device.id == itemChecked.id){
							device.check = 1;
							checkedDevices.toggle(device);
						}
					});
					
				}
				
				container.appendChild(
					getElementDevice(device)
				)
			});
			
		}
	
		drawDevices();
	
		function setTimer(BroneTimer/*formBrone.timer.left || formBrone.timer.right*/,newTime/*"11:20"*/){
		
			ofDivision.getDelta(
				newTime,
				BroneTimer
			); 
			
			if(BroneTimer.timeSlider.mTime.less(newTime)){
				/* если установленное время будет меньше чем на величину шага, то тут будет истина (число) */
				BroneTimer.next();
			}
			/*
			BroneTimer = BroneTimer.timeSlider.mTime.get();
			customMess(BroneTimer);
			*/
		}
	
	
		/* oIniFormBroneConversation. */
		return {
			htmlForm:htmlForm,
			saveRecord:saveRecord,
			setData:function(data,funcNextForTesting){
			
				/* oIniFormBroneConversation.setData(
						{
							note:"lala note",measure:"lala measure gsdfggf",time:"11:21-14:59",
							checkedDevices:[{id:56},{id:59}]
						}
					) */
				thrower(function(){
				
					if(data.time && fs(data.time,'-')){
						data.time = data.time.split('-');
					
						/* нужно проверить на валидность промежутки времени, что бы это точно было время */
						setTimer(formBrone.timer.left,data.time[0]);
						setTimer(formBrone.timer.right,data.time[1]);
						
					}
				
					if(data.checkedDevices){
						checkedDevices = new oListUniq();
						/* дабы затереть прежде отмеченные (выбранные) */
						
						drawDevices(data.checkedDevices);
					}
					
				
				
					/* там set note и set measure */
					formBrone.dataForm.set(data,funcNextForTesting);
				});
			},
			getData:function(){
				/* только для тестирования */
				/* так как formBrone.dataForm.get используется только внутри этого объекта */
				/* для записи используется лишь .saveRecord() */
			
				return formBrone.dataForm.get(dateApply);
			},
			close:function(){
				if(htmlForm){ fgetParent(htmlForm).remove();
					blockScreenClose();
				}
			}
		}
	
	})();
	
});/* << thrower */


<?php echo '</script'; ?>
>


<div class="posAbs block z155 r6 tr01 op0" style="background: rgb(220, 234, 249);">
	<div class="posRel icClose right crPoint" style="margin-top: -18px; margin-right: -18px;" 
		onclick="fgetParent(this).style.opacity=0;let el = fgetParent(this); setTimeout(function(){
			el.remove();
			blockScreenClose();
		},100);">
	</div>
	<form id="formBroneConversation" method="POST">
		<div class="row" style="margin:0;">
			<div class="posRel left p10 col-3 col-sm-3 col-md-3 col-lg-3" id="blockTime" >
				<div class="posRel mb10"><b id="dateApply">Установка даты..</b></div>
				
				<div class="posRel left" id="timerLeft">
					<b class="left d-none d-sm-none d-md-table-cell d-lg-table-cell" style="width:15px;">С</b>
					<div class="posRel btnSlide act left">&lt;</div>
					<input type="text" size=2>
					<div class="posRel btnSlide act right">&gt;</div>
				</div>
				<br />
				
				<div class="posRel left" id="timerRight">
					<b class="left d-none d-sm-none d-md-table-cell d-lg-table-cell" style="width:15px;">До</b>
					<div class="posRel btnSlide act left">&lt;</div>
					<input type="text" size=2>
					<div class="posRel btnSlide act right">&gt;</div>
				</div>
				
			</div>
			<div class="posRel flaglistDevices left p10 col-6 col-sm-4 col-md-4">
				<!-- b>Оборудование</b -->
				
			</div>
			
			<div class="posRel left p10 col-12 col-sm-4 col-md-5">
				<textarea class="w-100" id="idMeasure" placeholder="Наименование мероприятия" style="height: 25px;"></textarea>
				<textarea class="w-100" id="idNote" placeholder="Примечание" style="height: 100px;"></textarea>
			</div>
		</div>
		
		<div class="row" style="margin:0;">
			<div class="posRel p10 left col-3 col-md-3">
				<submit class="btn btn-sm btn-success" id="submitEdBrone">Забронировать</submit>
			</div>
			
			<?php if (is_array($_smarty_tpl->tpl_vars['dataBusy']->value) && (count($_smarty_tpl->tpl_vars['dataBusy']->value) > 0)) {?>
				<div class="posRel left p10 col-12 col-md-9">
					<table class="sAdmT tCenter fs12 tahoma w-100" cellpadding="1" cellspacing="0">
						<thead bgcolor="#EBEADB">
							<tr>
								<th>Занято</th>
								<th>Ответственный</th>
								
								<th>Изменить</th>
								<th>Удалить</th>
								
							</tr>
						</thead>
						<tbody>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['dataBusy']->value, 'record', false, 'item', 'cy', array (
));
$_smarty_tpl->tpl_vars['record']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value => $_smarty_tpl->tpl_vars['record']->value) {
$_smarty_tpl->tpl_vars['record']->do_else = false;
?>
								<tr class="tr" style="background-color:#ffffff;">
									<td>c <?php echo $_smarty_tpl->tpl_vars['record']->value['time'];?>
</td>
									<td 
										<?php if ($_smarty_tpl->tpl_vars['record']->value['tooltip'] != '') {?>
											onmouseover="toolTipS(`<?php echo $_smarty_tpl->tpl_vars['record']->value['tooltip'];?>
`)" onmouseout="toolTip()"
										<?php }?>
										>
										<a 
										<?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
											class="aUnderline" 
											target="_blank"
											href="index.php?id=32&userv=<?php echo $_smarty_tpl->tpl_vars['record']->value['userId'];?>
"
										<?php }?>
										><?php echo $_smarty_tpl->tpl_vars['record']->value['fio'];?>
</a>
										
									</td>
									<?php if ($_smarty_tpl->tpl_vars['admin']->value || ($_smarty_tpl->tpl_vars['user']->value['uid'] == $_smarty_tpl->tpl_vars['record']->value['userId'])) {?>
										<td>
											<span onclick="oIniPageBronСonversation.formEditRecord(<?php echo $_smarty_tpl->tpl_vars['record']->value['id'];?>
);">
											<img class="crPoint" src="/assets/images/icons/edit.png" onmouseover="toolTip(`Пожалуйста, при изменении заявки опишите причину в примечании.`);" onmouseout="toolTip();">
											</span>
										</td>
										<td>
											<span onclick="oIniPageBronСonversation.itemRemove(<?php echo $_smarty_tpl->tpl_vars['record']->value['id'];?>
,event);">
											<img class="crPoint" src="/assets/images/icons/close.png">
											</span>
										</td>
									<?php } else { ?>	
										<td colspan=2>Недостаточно прав</td>
									<?php }?>
								</tr>
							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						
						
						</tbody>
					</table>
				</div>
			<?php }?>
		</div>
		
	</form>	
</div>

 
<?php echo '<script'; ?>
>
	if(el = fget("formBroneConversation")){
		
		setTimeout(function(){
			el = fgetParent(el);
			el.style.opacity=0.9;
			el.style.top=getCenterY(el,window.innerHeight)+"px";
			if(parseInt(el.style.top) > 160){ el.style.top = "160px"; }
			
			el.style.left=getCenterX(el,window.innerWidth)+"px";
			blockScreen();
		},0);
		
	}
<?php echo '</script'; ?>
>
<?php }
}
