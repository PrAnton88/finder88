<?php
/* Smarty version 3.1.36, created on 2021-07-12 15:34:54
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\tableBronConversation.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60ec44fe770659_85376634',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7ddd33d2be03dd2d2197ee84b625916a143e3299' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\tableBronConversation.tpl',
      1 => 1625746507,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60ec44fe770659_85376634 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('splDate', 555);?>
<div class="posRel w-100 tr p5 mB3 left bordRadius block">
<?php if (count($_smarty_tpl->tpl_vars['messages']->value) > 0) {?>
	<table class="sAdmT tCenter w100 fs12 mB3" cellpadding="1" cellspacing="0">
		<thead>
			<tr>
			<th class="d-none d-sm-none d-md-table-cell d-lg-table-cell">N</th>
			<th class="d-table-cell d-sm-table-cell d-md-table-cell d-lg-none" style="width:100px;">Ответственный</th>
			<th class="d-none d-sm-none d-md-none d-lg-table-cell" style="width:200px;">Ответственный</th>
			
			<th style="width:90px;">Дата, Время</th>
			
			<th class="d-table-cell d-sm-table-cell d-md-table-cell d-lg-none" style="width:180px;">Мероприятие</th>
			<th class="d-none d-sm-none d-md-none d-lg-table-cell" style="width:220px;">Мероприятие</th>
			
			<th class="w15">Необходимое оборудование</th>
			<th class="d-none d-sm-none d-md-none d-lg-table-cell">Примечания</th>
			<th class="d-none d-sm-none d-md-none d-lg-table-cell">Изменить</th>
			<th class="d-none d-sm-none d-md-none d-lg-table-cell">Удалить</th>
			<th class="d-table-cell d-sm-table-cell d-md-table-cell d-lg-none">Действия</th>
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
			
			<?php $_smarty_tpl->_assignInScope('splDate', explode('-',$_smarty_tpl->tpl_vars['message']->value['date']));?>
						
				<?php if (($_smarty_tpl->tpl_vars['message']->value['hidd'] != 1)) {?>
			
					<tr class="tr" style="background-color:#ffffff;">
						<td class="hdtbl p6 d-none d-sm-none d-md-table-cell d-lg-table-cell"><?php echo $_smarty_tpl->tpl_vars['message']->value['id'];?>
</td>
						<td class="hdtbl p6"
							<?php if ($_smarty_tpl->tpl_vars['message']->value['tooltip'] != '') {?>
								onmouseover="toolTipS(`<?php echo $_smarty_tpl->tpl_vars['message']->value['tooltip'];?>
`)" onmouseout="toolTip()"
							<?php }?>
						>
						
							<a 
								<?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
									class="aUnderline"
									target="_blank"
									href="index.php?id=32&userv=<?php echo $_smarty_tpl->tpl_vars['message']->value['userId'];?>
"
								<?php }?>
							><?php echo $_smarty_tpl->tpl_vars['message']->value['fio'];?>
</a>
							
						</td>
						<td class="hdtbl p6 ">
							<?php echo $_smarty_tpl->tpl_vars['splDate']->value[2];?>
-<?php echo $_smarty_tpl->tpl_vars['splDate']->value[1];?>
-<?php echo $_smarty_tpl->tpl_vars['splDate']->value[0];?>

							<br />
							<b><?php echo $_smarty_tpl->tpl_vars['message']->value['time'];?>
</b>
						</td>
						<td class="hdtbl p6 maxW300"><?php echo $_smarty_tpl->tpl_vars['message']->value['measure'];?>
</td>
						<td class="hdtbl p6"><?php echo $_smarty_tpl->tpl_vars['message']->value['devices'];?>
</td>
						<td class="hdtbl p6 d-none d-sm-none d-md-none d-lg-table-cell"><?php echo $_smarty_tpl->tpl_vars['message']->value['note'];?>
</td>
						<?php if ($_smarty_tpl->tpl_vars['admin']->value || ($_smarty_tpl->tpl_vars['user']->value['uid'] == $_smarty_tpl->tpl_vars['message']->value['userId'])) {?>
							<td class="hdtbl p6 d-none d-sm-none d-md-none d-lg-table-cell">
								<span class="aUnderline crPoint" onclick="iniBronСonversation.editRecord(<?php echo $_smarty_tpl->tpl_vars['message']->value['id'];?>
);">
							
									<img class="crPoint" src="/assets/images/icons/edit.png" onmouseover="toolTip(`Пожалуйста, при изменении заявки опишите причину в примечании.`);" onmouseout="toolTip();">
								</span>
							</td>
							<td class="hdtbl p6 d-none d-sm-none d-md-none d-lg-table-cell">
							
								<span onclick="iniBronСonversation.itemRemove(<?php echo $_smarty_tpl->tpl_vars['message']->value['id'];?>
,event);">
								<img class="crPoint" src="/assets/images/icons/close.png">
								</span>
							</td>
						<?php } else { ?>
							<td colspan=2 class="hdtbl p6 d-none d-sm-none d-md-none d-lg-table-cell">
								Недостаточно прав
							</td>
						<?php }?>
						
						<?php if ($_smarty_tpl->tpl_vars['admin']->value || ($_smarty_tpl->tpl_vars['user']->value['uid'] == $_smarty_tpl->tpl_vars['message']->value['userId'])) {?>
							<td class="hdtbl p6 d-table-cell d-sm-table-cell d-md-table-cell d-lg-none">
								<span class="aUnderline crPoint" onclick="iniBronСonversation.editRecord(<?php echo $_smarty_tpl->tpl_vars['message']->value['id'];?>
);">
							
									<img class="crPoint" src="/assets/images/icons/edit.png" onmouseover="toolTip(`Пожалуйста, при изменении заявки опишите причину в примечании.`);" onmouseout="toolTip();">
								</span>
								<span onclick="iniBronСonversation.itemRemove(<?php echo $_smarty_tpl->tpl_vars['message']->value['id'];?>
,event);">
								<img class="crPoint" src="/assets/images/icons/close.png">
								</span>
							</td>
						<?php } else { ?>
							<td class="hdtbl p6 d-table-cell d-sm-table-cell d-md-table-cell d-lg-none">
								Недостаточно прав
							</td>
						<?php }?>
						
					</tr>
				<?php }?>
			
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</tbody>
	</table>
<?php } else { ?>
	<p>Переговорная полностью свободна</p>
<?php }?>
</div><?php }
}
