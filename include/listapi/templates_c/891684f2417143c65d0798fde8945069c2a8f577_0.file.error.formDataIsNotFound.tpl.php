<?php
/* Smarty version 3.1.36, created on 2021-05-25 14:53:24
  from 'C:\Users\oto016\singleProjects\modx-info\core\Smarty\fromapi\error.formDataIsNotFound.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_60acf344756be0_14275565',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '891684f2417143c65d0798fde8945069c2a8f577' => 
    array (
      0 => 'C:\\Users\\oto016\\singleProjects\\modx-info\\core\\Smarty\\fromapi\\error.formDataIsNotFound.tpl',
      1 => 1621944192,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60acf344756be0_14275565 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>
	new UserException(<?php if ($_smarty_tpl->tpl_vars['message']->value != '') {?>"<?php echo $_smarty_tpl->tpl_vars['message']->value;?>
"<?php } else { ?>"Недостаточно данных"<?php }?>).log();
<?php echo '</script'; ?>
>
<?php }
}
