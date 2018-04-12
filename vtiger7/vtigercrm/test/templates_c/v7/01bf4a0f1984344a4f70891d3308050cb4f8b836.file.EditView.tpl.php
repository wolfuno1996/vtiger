<?php /* Smarty version Smarty-3.1.7, created on 2018-04-04 02:15:46
         compiled from "C:\xampp\htdocs\vtiger7\vtigercrm\includes\runtime/../../layouts/v7\modules\QuotingTool\EditView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5229056535ac4355297b7d4-13404393%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '01bf4a0f1984344a4f70891d3308050cb4f8b836' => 
    array (
      0 => 'C:\\xampp\\htdocs\\vtiger7\\vtigercrm\\includes\\runtime/../../layouts/v7\\modules\\QuotingTool\\EditView.tpl',
      1 => 1522807477,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5229056535ac4355297b7d4-13404393',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'USER_PROFILE' => 0,
    'CONFIG' => 0,
    'MODULES' => 0,
    'CUSTOM_FUNCTIONS' => 0,
    'CUSTOM_FIELDS' => 0,
    'COMPANY_FIELDS' => 0,
    'QUOTER_SETTINGS' => 0,
    'RECORD_ID' => 0,
    'MODULE' => 0,
    'TEMPLATE' => 0,
    'SETTINGS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ac4355430713',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ac4355430713')) {function content_5ac4355430713($_smarty_tpl) {?>
<div class="editViewContainer container-fluid" ng-app="app" id="quoting_tool-app"><div id="js_currentUser" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['USER_PROFILE']->value);?>
</div><div id="js_config" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['CONFIG']->value);?>
</div><div id="js_modules" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['MODULES']->value);?>
</div><div id="js_custom_functions" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['CUSTOM_FUNCTIONS']->value);?>
</div><div id="js_custom_fields" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['CUSTOM_FIELDS']->value);?>
</div><div id="js_company_fields" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['COMPANY_FIELDS']->value);?>
</div><?php if ((isset($_smarty_tpl->tpl_vars['QUOTER_SETTINGS']->value))){?><div id="js_quoter_settings" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['QUOTER_SETTINGS']->value);?>
</div><?php }?><div id="quoting_tool-body" ng-controller="CtrlApp"><form action="index.php" id="EditView" name="EditView" method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="Save"/><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
"/><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
"/><input type="hidden" name="primary_module" value="<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('module') : '';?>
<?php $_tmp1=ob_get_clean();?><?php echo $_tmp1;?>
"/><textarea name="body" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('body') : '';?>
<?php $_tmp2=ob_get_clean();?><?php echo $_tmp2;?>
</textarea><textarea name="header" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('header') : '';?>
<?php $_tmp3=ob_get_clean();?><?php echo $_tmp3;?>
</textarea><textarea name="content" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('content') : '';?>
<?php $_tmp4=ob_get_clean();?><?php echo $_tmp4;?>
</textarea><textarea name="footer" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('footer') : '';?>
<?php $_tmp5=ob_get_clean();?><?php echo $_tmp5;?>
</textarea><input type="hidden" name="email_subject" value="<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('email_subject') : '';?>
<?php $_tmp6=ob_get_clean();?><?php echo $_tmp6;?>
"><textarea name="email_content" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('email_content') : '';?>
<?php $_tmp7=ob_get_clean();?><?php echo $_tmp7;?>
</textarea><textarea name="description" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('description') : '';?>
<?php $_tmp8=ob_get_clean();?><?php echo $_tmp8;?>
</textarea><textarea name="anwidget" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('anwidget') : '';?>
<?php $_tmp9=ob_get_clean();?><?php echo $_tmp9;?>
</textarea><textarea name="createnewrecords" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('createnewrecords') : '';?>
<?php $_tmp10=ob_get_clean();?><?php echo $_tmp10;?>
</textarea><textarea name="linkproposal" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('linkproposal') : '';?>
<?php $_tmp11=ob_get_clean();?><?php echo $_tmp11;?>
</textarea><textarea name="mapping_fields" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('mapping_fields') : '';?>
<?php $_tmp12=ob_get_clean();?><?php echo $_tmp12;?>
</textarea><textarea name="settings" class="hide"><?php echo $_smarty_tpl->tpl_vars['SETTINGS']->value;?>
</textarea><textarea name="attachments" class="hide"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('attachments') : '';?>
<?php $_tmp13=ob_get_clean();?><?php echo $_tmp13;?>
</textarea><input type="hidden" name="is_active" class="hide" value="<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('is_active')!='' ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('is_active') : '1';?>
<?php $_tmp14=ob_get_clean();?><?php echo $_tmp14;?>
"><div id="quoting_tool-header"><div id="quoting_tool-header-actions" style="display: none;"><div class="pull-left"><input name="filename" value="<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['TEMPLATE']->value ? $_smarty_tpl->tpl_vars['TEMPLATE']->value->get('filename') : '';?>
<?php $_tmp15=ob_get_clean();?><?php echo $_tmp15;?>
"></div><button class="btn btn-primary" type="submit" ng-click="saveTemplate($event)">Save</button></div></div><div id="quoting_tool-container"><div id="quoting_tool-center" class="column" resize><div class="document__block-list quoting_tool-content"><div class="quoting_tool-content-header doc-block doc-block--header"></div><div class="quoting_tool-content-main quoting_tool-drop-component-in-content document__block-list"></div><div class="quoting_tool-content-footer doc-block doc-block--footer"></div></div><div id="quoting_tool-overlay-content" class="blockUI blockOverlay"style="display: none;"></div></div><div id="quoting_tool-left-panel" class="column"></div><div id="quoting_tool-right-panel" class="column" ng-controller="CtrlAppRightPanel"><div id="quoting_tool-tool-items"><div id="quoting_too-file-name-container"ng-include="'layouts/v7/modules/QuotingTool/resources/js/views/right_panel/basic_infomation.html'"></div><div ui-view="right_panel_tool_items"></div></div><div id="quoting_tool-tools"ng-include="'layouts/v7/modules/QuotingTool/resources/js/views/right_panel/tools.html'"></div></div><div class="clear"></div></div><div id="quoting_tool-footer"></div></form><div style="width: 0; height: 0; visibility: hidden;" id="quoting_tool-tmp"><div id="quoting_tool-tmp-content"></div></div></div></div><?php }} ?>