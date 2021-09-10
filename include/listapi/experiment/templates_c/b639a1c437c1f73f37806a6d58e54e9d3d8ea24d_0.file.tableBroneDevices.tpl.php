<?php
/* Smarty version 3.1.36, created on 2021-07-22 10:24:05
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\tableBroneDevices.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60f92b25741395_73935331',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b639a1c437c1f73f37806a6d58e54e9d3d8ea24d' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\tableBroneDevices.tpl',
      1 => 1626942182,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60f92b25741395_73935331 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>
if(typeof(oTableBroneDevices) === "undefined"){

	var oTableBroneDevices = null;
}

thrower(function(cException){
	oTableBroneDevices = (function(){

		var listJsonRecordsBusy = new oListUniq();
		listJsonRecordsBusy.update(<?php echo $_smarty_tpl->tpl_vars['listJsonRecordsBusy']->value;?>
);
		
		/* в listJsonRecordsBusy добавить ссылки */
		/* связывающие элементы интерфейса { Изменить, Выдать, Вернуть } */
		
		let flagTbody = fget('flagTableBronRecordsBusyDevices');
		flagTbody = Array.from(flagTbody.querySelectorAll('tr'));
		
		let k = 0;
		let recordsBusy = listJsonRecordsBusy.get();
		let uiFlags = null;
		flagTbody.map(item => {
			uiFlags = {};
			uiFlags.flagChangeRecord = item.querySelector('td#flagChangeRecord').querySelector('.icEdit');
			uiFlags.flagIssueDevices = item.querySelector('td#flagIssueDevices').querySelector('.icEdit');
			uiFlags.flagReturnDevices = item.querySelector('td#flagReturnDevices').querySelector('.icEdit');
			
			recordsBusy[k].ui = uiFlags;
			k++;
		});
		
		return {
			listRecords:listJsonRecordsBusy
		};
	})();

});/* << thrower */
<?php echo '</script'; ?>
>


<div class="posRel w-100 tr p5 mB3 left bordRadius block">
	<table class="sAdmT tCenter w100 fs12 mB3" cellpadding="1" cellspacing="0">
		<thead>
			<tr>
				<th class="d-none d-sm-none d-md-table-cell d-lg-table-cell">N</th>
				<th class="col-1 d-none d-sm-none d-md-table-cell d-lg-table-cell">Начало</th>
				<th class="col-1 d-none d-sm-none d-md-table-cell d-lg-table-cell">Окончание</th>
				<th class="d-table-cell d-sm-table-cell d-md-none d-lg-none">Период</th>
				<th>Ф.И.О.</th>
				<th class="d-none d-sm-none d-md-table-cell d-lg-table-cell">Список устройств</th>
				<th class="d-none d-sm-none d-md-table-cell d-lg-table-cell">Комментарий</th>
				<th class="d-table-cell d-sm-table-cell d-md-none d-lg-none">Список устройств</th>
				<th>Изменить</th>
				<th>Выдать</th>
				<th>Вернуть</th>
			</tr>
		</thead>
		<tbody id="flagTableBronRecordsBusyDevices">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['listRecordsBusy']->value, 'record', false, NULL, 'cy', array (
));
$_smarty_tpl->tpl_vars['record']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['record']->value) {
$_smarty_tpl->tpl_vars['record']->do_else = false;
?>
			<tr>
				<td class="hdtbl p6 d-none d-sm-none d-md-table-cell d-lg-table-cell"><?php echo $_smarty_tpl->tpl_vars['record']->value['id'];?>
</td>
				<td class="hdtbl p6 d-none d-sm-none d-md-table-cell d-lg-table-cell">
				<?php echo $_smarty_tpl->tpl_vars['record']->value['datest'];?>

				</td>
				<td class="hdtbl p6 d-none d-sm-none d-md-table-cell d-lg-table-cell"><?php echo $_smarty_tpl->tpl_vars['record']->value['dateend'];?>
</td>
				<td class="hdtbl p6 d-table-cell d-sm-table-cell d-md-none d-lg-none"><?php echo $_smarty_tpl->tpl_vars['record']->value['datest'];?>
<br /><?php echo $_smarty_tpl->tpl_vars['record']->value['dateend'];?>
</td>
				<td class="hdtbl p6"
					<?php if ($_smarty_tpl->tpl_vars['record']->value['tooltip'] != '') {?>
						onmouseover="toolTipS(`<?php echo $_smarty_tpl->tpl_vars['record']->value['tooltip'];?>
`)" onmouseout="toolTip()"
					<?php }?>
					><a
						<?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
							class="aUnderline"
							target="_blank"
							href="index.php?id=32&userv=<?php echo $_smarty_tpl->tpl_vars['record']->value['userId'];?>
"
						<?php }?>
					><?php echo $_smarty_tpl->tpl_vars['record']->value['fio'];?>
</a>
				</td>
				<td class="hdtbl p6 d-none d-sm-none d-md-table-cell d-lg-table-cell"><?php echo $_smarty_tpl->tpl_vars['record']->value['listdevice'];?>
</td>
				<td class="hdtbl p6 d-none d-sm-none d-md-table-cell d-lg-table-cell"><?php echo $_smarty_tpl->tpl_vars['record']->value['note'];?>
</td>
				<td class="hdtbl p6 d-table-cell d-sm-table-cell d-md-none d-lg-none"><?php echo $_smarty_tpl->tpl_vars['record']->value['listdevice'];?>
<br />Комментарий: <?php echo $_smarty_tpl->tpl_vars['record']->value['note'];?>
</td>
				
				<td class="hdtbl p6" id="flagChangeRecord">
					<?php if (($_smarty_tpl->tpl_vars['record']->value['free'] == 0)) {?>
						
						<!-- img title="Устройства не выданы" -->
						<div class="crPoint icEdit icSize mAuto"></div>
					<?php }?>
				</td>
				<td class="hdtbl p6" id="flagIssueDevices">
					<?php if (($_smarty_tpl->tpl_vars['record']->value['free'] == 0)) {?>
						
						<div class="crPoint icEdit icSize mAuto"></div>
					<?php }?>
				</td>
				<td class="hdtbl p6" id="flagReturnDevices">
					<?php if (($_smarty_tpl->tpl_vars['record']->value['free'] == 2)) {?>
						
						<div class="crPoint icEdit icSize mAuto"></div>
					<?php }?>
				</td>
			</tr>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</tbody>
	</table>
</div><?php }
}
