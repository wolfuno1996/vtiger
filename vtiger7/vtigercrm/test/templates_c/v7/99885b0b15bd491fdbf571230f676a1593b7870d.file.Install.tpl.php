<?php /* Smarty version Smarty-3.1.7, created on 2018-04-04 02:10:06
         compiled from "C:\xampp\htdocs\vtiger7\vtigercrm\includes\runtime/../../layouts/v7\modules\QuotingTool\Install.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11027135025ac433fe8b5998-31946341%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '99885b0b15bd491fdbf571230f676a1593b7870d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\vtiger7\\vtigercrm\\includes\\runtime/../../layouts/v7\\modules\\QuotingTool\\Install.tpl',
      1 => 1522807477,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11027135025ac433fe8b5998-31946341',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'PDF_LIB_LINK' => 0,
    'PDF_LIB_SOURCE' => 0,
    'MB_STRING_EXISTS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ac433fea79c4',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ac433fea79c4')) {function content_5ac433fea79c4($_smarty_tpl) {?>
<div class="contentsDiv marginLeftZero"><div class="padding1per"><div class="editContainer" style="padding-left: 3%; padding-right: 3%"><br><h3><?php echo vtranslate('LBL_MODULE_NAME',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate('LBL_INSTALL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h3><hr><form name="install" id="editLicense" method="POST" action="index.php" class="form-horizontal"><input type="hidden" name="module" value="PDFMaker"/><input type="hidden" name="view" value="List"/><div id="step1" class="padding1per" style="border:1px solid #ccc; padding-left: 10px; padding-bottom: 10px"><input type="hidden" name="installtype" value="download_src"/><div class="controls"><div><strong><?php echo vtranslate('LBL_DOWNLOAD_SRC',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></div><br><div class="clearfix"></div></div><div class="controls"><div><p><?php echo vtranslate('LBL_DOWNLOAD_SRC_DESC1',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p><input type="url" value="<?php echo $_smarty_tpl->tpl_vars['PDF_LIB_LINK']->value;?>
" disabled="disabled" style="width: 30%;"/><p style="padding-top: 5px"><?php echo vtranslate('LBL_DOWNLOAD_SRC_DESC2',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p><input type="text" value="<?php echo $_smarty_tpl->tpl_vars['PDF_LIB_SOURCE']->value;?>
" disabled="disabled" style="width: 30%"/><?php if ($_smarty_tpl->tpl_vars['MB_STRING_EXISTS']->value=='false'){?><br><?php echo vtranslate('LBL_MB_STRING_ERROR',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></div><br><div class="clearfix"></div></div><div class="controls"><span style="display: none; color: red;"class="quoting_tool-processing"><?php echo vtranslate('Downloading...',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span><br><button type="button" id="download_button" class="btn btn-success quoting_tool-downloadLib"><strong><?php echo vtranslate('LBL_DOWNLOAD',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button>&nbsp;&nbsp;</div></div></form></div></div></div><?php }} ?>