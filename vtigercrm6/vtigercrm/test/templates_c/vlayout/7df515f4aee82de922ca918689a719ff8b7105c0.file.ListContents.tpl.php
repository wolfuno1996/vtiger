<?php /* Smarty version Smarty-3.1.7, created on 2018-03-28 02:29:00
         compiled from "C:\xampp\htdocs\vtigercrm6\vtigercrm\includes\runtime/../../layouts/vlayout\modules\Settings\ModuleManager\ListContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8775044465abafdecaf7e49-57022357%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7df515f4aee82de922ca918689a719ff8b7105c0' => 
    array (
      0 => 'C:\\xampp\\htdocs\\vtigercrm6\\vtigercrm\\includes\\runtime/../../layouts/vlayout\\modules\\Settings\\ModuleManager\\ListContents.tpl',
      1 => 1468488064,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8775044465abafdecaf7e49-57022357',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'ALL_MODULES' => 0,
    'MODULE_MODEL' => 0,
    'COUNTER' => 0,
    'MODULE_NAME' => 0,
    'MODULE_ACTIVE' => 0,
    'RESTRICTED_MODULES_LIST' => 0,
    'SETTINGS_LINKS' => 0,
    'SETTINGS_LINK' => 0,
    'IMPORT_USER_MODULE_FROM_FILE_URL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5abafdecb9850',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5abafdecb9850')) {function content_5abafdecb9850($_smarty_tpl) {?>
<div class="container-fluid" id="moduleManagerContents"><div class="widget_header row-fluid"><div class="span6"><h3><?php echo vtranslate('LBL_MODULE_MANAGER',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h3></div><div class="span6"><span class="btn-toolbar pull-right"><span class="btn-group"><button class="btn" type="button" onclick='window.location.href="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore"'><strong><?php echo vtranslate('LBL_EXTENSION_STORE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button></span></span></div></div><hr><div class="contents"><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><table class="table table-bordered equalSplit"><tr><?php  $_smarty_tpl->tpl_vars['MODULE_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['MODULE_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['MODULE_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ALL_MODULES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['MODULE_MODEL']->key => $_smarty_tpl->tpl_vars['MODULE_MODEL']->value){
$_smarty_tpl->tpl_vars['MODULE_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['MODULE_ID']->value = $_smarty_tpl->tpl_vars['MODULE_MODEL']->key;
?><?php $_smarty_tpl->tpl_vars['MODULE_NAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->get('name'), null, 0);?><?php $_smarty_tpl->tpl_vars['MODULE_ACTIVE'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->isActive(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value==2){?></tr><tr><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><?php }?><td class="opacity"><div class="row-fluid moduleManagerBlock"><span class="span1"><input type="checkbox" value="" name="moduleStatus" data-module="<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
" data-module-translation="<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
" <?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->isActive()){?>checked<?php }?> /></span><span class="span1"><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->isExportable()){?><a href="index.php?module=ModuleManager&parent=Settings&action=ModuleExport&mode=exportModule&forModule=<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->get('name');?>
"><i class="icon icon-download"></i></a><?php }?>&nbsp;</span><span class="span2 moduleImage <?php if (!$_smarty_tpl->tpl_vars['MODULE_ACTIVE']->value){?>dull <?php }?>"><?php if (vimage_path(($_smarty_tpl->tpl_vars['MODULE_NAME']->value).('.png'))!=false){?><img class="alignMiddle" src="<?php echo vimage_path(($_smarty_tpl->tpl_vars['MODULE_NAME']->value).('.png'));?>
" alt="<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
"/><?php }else{ ?><img class="alignMiddle" src="<?php echo vimage_path('DefaultModule.png');?>
" alt="<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
"/><?php }?></span><span class="span5 moduleName <?php if (!$_smarty_tpl->tpl_vars['MODULE_ACTIVE']->value){?>dull <?php }?>"><h4><?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</h4></span><?php $_smarty_tpl->tpl_vars['SETTINGS_LINKS'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getSettingLinks(), null, 0);?><?php if (!in_array($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['RESTRICTED_MODULES_LIST']->value)&&(count($_smarty_tpl->tpl_vars['SETTINGS_LINKS']->value)>0)){?><span class="span3"><span class="btn-group pull-right actions <?php if (!$_smarty_tpl->tpl_vars['MODULE_ACTIVE']->value){?>hide<?php }?>"><button class="btn dropdown-toggle" data-toggle="dropdown"><strong><?php echo vtranslate('LBL_SETTINGS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong>&nbsp;<i class="caret"></i></button><ul class="dropdown-menu pull-right"><?php  $_smarty_tpl->tpl_vars['SETTINGS_LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SETTINGS_LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['SETTINGS_LINKS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SETTINGS_LINK']->key => $_smarty_tpl->tpl_vars['SETTINGS_LINK']->value){
$_smarty_tpl->tpl_vars['SETTINGS_LINK']->_loop = true;
?><li><a <?php if (stripos($_smarty_tpl->tpl_vars['SETTINGS_LINK']->value['linkurl'],'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['SETTINGS_LINK']->value['linkurl'],strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['SETTINGS_LINK']->value['linkurl'];?>
"'<?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['SETTINGS_LINK']->value['linklabel'],$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</a></li><?php } ?></ul></span></span><?php }?></div><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?></td><?php } ?></tr></table></div><div class="row-fluid" style="padding: 20px 0px;"><a href="<?php echo $_smarty_tpl->tpl_vars['IMPORT_USER_MODULE_FROM_FILE_URL']->value;?>
"><?php echo vtranslate('LBL_INSTALL_FROM_ZIP',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</a></div></div>
<?php }} ?>