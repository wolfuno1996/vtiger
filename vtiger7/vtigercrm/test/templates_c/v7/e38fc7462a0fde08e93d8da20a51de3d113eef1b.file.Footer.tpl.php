<?php /* Smarty version Smarty-3.1.7, created on 2018-03-27 11:07:29
         compiled from "C:\xampp\htdocs\vtiger7\vtigercrm\includes\runtime/../../layouts/v7\modules\Vtiger\Footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18618220725aba25f104d5f8-03789554%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e38fc7462a0fde08e93d8da20a51de3d113eef1b' => 
    array (
      0 => 'C:\\xampp\\htdocs\\vtiger7\\vtigercrm\\includes\\runtime/../../layouts/v7\\modules\\Vtiger\\Footer.tpl',
      1 => 1520586669,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18618220725aba25f104d5f8-03789554',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'VTIGER_VERSION' => 0,
    'LANGUAGE_STRINGS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5aba25f105c2f',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5aba25f105c2f')) {function content_5aba25f105c2f($_smarty_tpl) {?>

<footer class="app-footer">
	<p>
		Powered by vtiger CRM - <?php echo $_smarty_tpl->tpl_vars['VTIGER_VERSION']->value;?>
&nbsp;&nbsp;© 2004 - <?php echo date('Y');?>
&nbsp;&nbsp;
		<a href="//www.vtiger.com" target="_blank">Vtiger</a>&nbsp;|&nbsp;
		<a href="https://www.vtiger.com/privacy-policy" target="_blank">Privacy Policy</a>
	</p>
</footer>
</div>
<div id='overlayPage'>
	<!-- arrow is added to point arrow to the clicked element (Ex:- TaskManagement), 
	any one can use this by adding "show" class to it -->
	<div class='arrow'></div>
	<div class='data'>
	</div>
</div>
<div id='helpPageOverlay'></div>
<div id="js_strings" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['LANGUAGE_STRINGS']->value);?>
</div>
<div class="modal myModal fade"></div>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('JSResources.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>

</html><?php }} ?>