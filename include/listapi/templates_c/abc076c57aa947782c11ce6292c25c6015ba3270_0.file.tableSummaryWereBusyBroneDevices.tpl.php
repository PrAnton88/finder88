<?php
/* Smarty version 3.1.36, created on 2021-07-21 13:30:18
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\tableSummaryWereBusyBroneDevices.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60f8054ad2cea7_46211293',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'abc076c57aa947782c11ce6292c25c6015ba3270' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\tableSummaryWereBusyBroneDevices.tpl',
      1 => 1624359664,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60f8054ad2cea7_46211293 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>
/* должен быть подключен файл free.js */
/* запрашивается из файла-страницы page.broneDevices.js */

if(typeof(summaryTableWereBusyBroneDevices) === "undefined"){
	/* если бы определили как let - то переменная не была бы доступна снаружи скобок */
	var summaryTableWereBusyBroneDevices = null;

}


thrower(function(cException){
	summaryTableWereBusyBroneDevices = (function(){
	
		/* в данном случае listBusyRecords - не изменяется со временем, это записи уже такого оборудования,
		которое вернули в оборот */
		/**/
		
		/* (необходимо обновлять если толькооборудование вернули в то время пока кто то открыл эту результирующую таблицу ) */
		
		var listBusyRecords = new oListUniq();
		listBusyRecords.update(<?php echo $_smarty_tpl->tpl_vars['messagesJson']->value;?>
);
		
		/* один раз */
		if(typeof(drawer) === "undefined"){
			
			var table = null;
			
			let deviceSortoList = new oMultiSorto(listBusyRecords.get());
			/* уже отсортировано по id */
			deviceSortoList.last="id";
			
			/* пере-рисователь tbody */
			var drawer = new function(){
				this.toggle = function(field){
				
					let newList = deviceSortoList.applay(field);
					
					// console.log(newList);
					
					if(table = fget('tableSummaryBroneDevices')){
						el = table.querySelector('tbody');
					
						el.textContent = '';
					
						newList.map(item => {
							el.appendChild(item.tr);
						});
						
					}
				}
			};
			
			function getField(textContent){
		
				if(textContent == 'N'){ return 'id'; }
				if(textContent == 'Начало'){ return 'datest'; }
				if(textContent == 'Окончание'){ return 'dateend'; }
				if(textContent == 'Ф.И.О.'){ return 'fio'; }
				if(textContent == 'Список устройств'){ return 'listdevice'; }
				return false;
			}
		}
		
		
		/* при каждом открытии формы - снова */
		if(table = fget('tableSummaryBroneDevices')){
			el = table.querySelector('tbody');
			el = el.querySelectorAll('tr');
			
			let i = 0;
			listBusyRecords.get().map(item => {
				item.tr = el[i];
				
				/* это глуповато, но это для старого filtrumTable */
					item.tr.off = function(){
						this.style.display = "none";
					};
					item.tr.on = function(){
						this.style.display = "";
					}
				/* end */
				
				listBusyRecords.update(item);
				
				i++;
			});
			/* в результате были добавлены ссылки на строки в listBusyRecords */
		
			el = table.querySelector('thead');
			el = Array.from(el.querySelectorAll('th'));
			
			el.map(item => {
				
				item.addEventListener('click',function(e){
					thrower(function(cException){
						drawer.toggle(getField(e.target.textContent));
					});
				});
				
			});
			/* в результате были добавлены ссылки на строки в listBusyRecords */
			
			
			
			/* было прнято решение фильтры добавлять в модуле который вызывает таблицу фильтры */
			
		}
		
		return {
			getBusyRecords: function(){
				return listBusyRecords;
			},
			table:table
		}
		
	})();
});/* << thrower */

<?php echo '</script'; ?>
>



<table class="sAdmT tCenter w99 m2 fs12" cellpadding="1" cellspacing="0" id="tableSummaryBroneDevices">
	<thead>
		<tr>
			<!-- th>
				<span class="crPoint">N</span>
			</th -->
			<th>
				<span class="crPoint">Начало</span>
				
			</th>
			<th>
				<span class="crPoint">Окончание</span>
			</th>
			<th>
				<span class="crPoint">Ф.И.О.</span>
			</th>
			<th>
				<span class="crPoint">Список устройств</span>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'message', false, NULL, 'cy', array (
));
$_smarty_tpl->tpl_vars['message']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
$_smarty_tpl->tpl_vars['message']->do_else = false;
?>
		<tr>
			<!-- td class="ttd" -->
						<!-- /td -->
			<td class="ttd"><?php echo $_smarty_tpl->tpl_vars['message']->value['datestViewility'];?>
</td>
			<td class="ttd"><?php echo $_smarty_tpl->tpl_vars['message']->value['dateendViewility'];?>
</td>
			<td class="ttd"
				<?php if ($_smarty_tpl->tpl_vars['message']->value['tooltip'] != '') {?>
					onmouseover="toolTipS(`<?php echo $_smarty_tpl->tpl_vars['message']->value['tooltip'];?>
`)" onmouseout="toolTip()"
				<?php }?>
				><a
					<?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
						class="aUnderline"
						target="_blank"
						href="index.php?id=32&userv=<?php echo $_smarty_tpl->tpl_vars['message']->value['userid'];?>
"
					<?php }?>
				><?php echo $_smarty_tpl->tpl_vars['message']->value['fio'];?>
</a>
			</td>
			<td class="ttd"><?php echo $_smarty_tpl->tpl_vars['message']->value['listdevice'];?>
</td>
		</tr>
		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</tbody>
</table><?php }
}
