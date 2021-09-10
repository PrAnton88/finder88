<?php
/* Smarty version 3.1.36, created on 2021-05-25 14:03:19
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\formDataIsNotFound.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60ace78745e3e6_84816677',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f81a1d0b89e752905c58857bc286f63add096dbd' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\formDataIsNotFound.tpl',
      1 => 1621944192,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60ace78745e3e6_84816677 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>
	new UserException(<?php if ($_smarty_tpl->tpl_vars['message']->value != '') {?>"<?php echo $_smarty_tpl->tpl_vars['message']->value;?>
"<?php } else { ?>"Недостаточно данных"<?php }?>).log();
<?php echo '</script'; ?>
>
<?php }
}
