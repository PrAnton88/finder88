<?php
/* Smarty version 3.1.36, created on 2021-09-10 13:12:05
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\selectorDiapazoneDate.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_613b2f754fe4d5_27841534',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7bde7b6b8b6064a96f7bd8ad9bdfb890a7a521e4' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\selectorDiapazoneDate.tpl',
      1 => 1628232070,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_613b2f754fe4d5_27841534 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>
/* должен быть подключен moduleTypeDate */

if(typeof(oSelectorDiapazoneDate) === "undefined"){

	var oSelectorDiapazoneDate = null;
}

thrower(function(cException){
	oSelectorDiapazoneDate = (function(){
	
		let dateSt = fget("dateSt");
		let dateEnd = fget("dateEnd");
		
		let nDate = oDateConvert.reverse(fnewDate(),'.')/*формат как '06.06.2021'*/;
		
		if(dateSt){
			dateSt = {dom:dateSt,border:nDate};
		}
		
		nDate = nDate.split('.');
		
		if(dateEnd){
			dateEnd = {dom:dateEnd,border:'28.'+nDate[1]+'.'+ ++nDate[2]};
		}
		
		
		
		function handCalendarIcon(icon,field){
			
			icon.addEventListener('click',function(e){
				
				thrower(function(cException){
					
					fCalendar(false,"toDiapazoneDate",1100,false,function(e,oDom){
						
						/*в текстовое поле которое рядом с иконкой*/
						field.value = (fzerro(oDom.day) + "." + fzerro(oDom.month)  + "." +  oDom.year);
						
					},
					/* мин и макс даты в базе (установка начальной и конечной даты на календаре) */
						dateSt.border,
						dateEnd.border/*,
						
						// 8 - для выпадающих списков
						function(oCalend){
							oCalend.setMonthNew(0);
							oCalend.getListVariantMonth();
							oCalend.getListVariantYear();
							
						}*/
						
					);
				
				});/* << thrower */
				
			});
			
		}
		
		
		
		if(dateSt && dateEnd){
			
			moduleTypeDate(dateSt.dom.querySelector('input'),
				dateSt.border,
				dateEnd.border
			);
			
			handCalendarIcon(
				dateSt.dom.querySelector(".dateicon"),
				dateSt.dom.querySelector('input')
			);
			

			moduleTypeDate(dateEnd.dom.querySelector('input'),
				dateSt.border,
				dateEnd.border
			);
		
			handCalendarIcon(
				dateEnd.dom.querySelector(".dateicon"),
				dateEnd.dom.querySelector('input')
			);
			
		}
		
		/* oSelectorDiapazoneDate */
		return {
			data:{
				set:function(startDate,endDate){
					/* проверять валидность даты */
					/* проверка валидности дат */
					/* ru new Date("2021.08.05") */
					
					/* в случае не валидности - "Invalid Date" */
					if(startDate && ( (new Date(startDate) != 'Invalid Date') )){
						dateSt.dom.querySelector('input').value = startDate;
					}
					
					if(!endDate){ endDate = startDate; }
					
					if(endDate && ( (new Date(endDate) != 'Invalid Date') )){
						dateEnd.dom.querySelector('input').value = endDate;
					}
					
				}
			},
			getDateSt:function(){
				return dateSt.dom.querySelector('input').value;
			},
			getDateEnd:function(){
				return dateEnd.dom.querySelector('input').value;
			}
		}
		
	})();

});/* << thrower */

<?php echo '</script'; ?>
>



<div class="posRel r6 p8 noSelect noneBoxSizing op0 tr02 newGradient left crDef m5" style="margin-right: 2%; width: 145px; opacity: 1;">
	<div class="left w100 m5" style="width: 133px;">
		<div class="left w100">День выдачи</div>
		<div class="left mWAuto m5 bGWhite crTxt bordGray h23" id="dateSt">
			<div class="left">
				<input class="fs14" type="text" value="16.06.2021" style="width: 105px; color: red;" />
			</div>
			<div class="dateicon left crPoint m2">
			</div>
		</div>
		<div class="left w100">День возврата</div>
		<div class="left mWAuto m5 bGWhite crTxt bordGray h23" id="dateEnd">
			<div class="left">
				<input class="fs14" type="text" value="16.06.2021" style="width: 105px; color: red;" />
			</div>
			<div class="dateicon left crPoint m2"></div>
		</div>
		<div class="left w100 h23">
			<input class="right m5" type="submit" value="Применить" id="applyDiapazoneDate" />
		</div>
	</div>
</div><?php }
}
