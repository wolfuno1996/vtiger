<?php /* Smarty version Smarty-3.1.7, created on 2018-04-01 12:32:34
         compiled from "C:\xampp\htdocs\vtigercrm6\vtigercrm\includes\runtime/../../layouts/vlayout\modules\Settings\Picklist\EditView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11331637515ac0d162bdf809-52677792%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f03f69394fa8b2bde3d7c72f439597f601fb5129' => 
    array (
      0 => 'C:\\xampp\\htdocs\\vtigercrm6\\vtigercrm\\includes\\runtime/../../layouts/vlayout\\modules\\Settings\\Picklist\\EditView.tpl',
      1 => 1468488064,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11331637515ac0d162bdf809-52677792',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUALIFIED_MODULE' => 0,
    'MODULE' => 0,
    'SOURCE_MODULE' => 0,
    'FIELD_MODEL' => 0,
    'SELECTED_PICKLISTFIELD_EDITABLE_VALUES' => 0,
    'PICKLIST_VALUES' => 0,
    'FIELD_VALUE' => 0,
    'PICKLIST_VALUE' => 0,
    'PICKLIST_VALUE_KEY' => 0,
    'SELECTED_PICKLISTFIELD_NON_EDITABLE_VALUES' => 0,
    'NON_EDITABLE_VALUE' => 0,
    'qualifiedName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ac0d16305c35',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ac0d16305c35')) {function content_5ac0d16305c35($_smarty_tpl) {?>
<div class='modelContainer'><div class="modal-header"><button data-dismiss="modal" class="close" title="<?php echo vtranslate('LBL_CLOSE');?>
">x</button><h3><?php echo vtranslate('LBL_RENAME_PICKLIST_ITEM',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h3></div><form id="renameItemForm" class="form-horizontal" method="post" action="index.php"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" /><input type="hidden" name="parent" value="Settings" /><input type="hidden" name="source_module" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
" /><input type="hidden" name="action" value="SaveAjax" /><input type="hidden" name="mode" value="rename" /><input type="hidden" name="picklistName" value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name');?>
" /><input type="hidden" name="pickListValues" value='<?php echo Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($_smarty_tpl->tpl_vars['SELECTED_PICKLISTFIELD_EDITABLE_VALUES']->value));?>
' /><div class="modal-body tabbable"><div class="control-group"><div class="control-label"><?php echo vtranslate('LBL_ITEM_TO_RENAME',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</div><div class="controls"><?php $_smarty_tpl->tpl_vars['PICKLIST_VALUES'] = new Smarty_variable($_smarty_tpl->tpl_vars['SELECTED_PICKLISTFIELD_EDITABLE_VALUES']->value, null, 0);?><select class="chzn-select" name="oldValue"><optgroup><?php  $_smarty_tpl->tpl_vars['PICKLIST_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_VALUE_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key => $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){
$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_VALUE_KEY']->value = $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key;
?><option <?php if ($_smarty_tpl->tpl_vars['FIELD_VALUE']->value==$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){?> selected="" <?php }?>value="<?php echo Vtiger_Util_Helper::toSafeHTML($_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value);?>
" data-id=<?php echo $_smarty_tpl->tpl_vars['PICKLIST_VALUE_KEY']->value;?>
><?php echo vtranslate($_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value,$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
</option><?php } ?></optgroup></select></div><br><div class="control-label"><span class="redColor">*</span><?php echo vtranslate('LBL_ENTER_NEW_NAME',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</div><div class="controls"><input type="text" data-validation-engine="validate[required, funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-validator=<?php echo Zend_Json::encode(array(array('name'=>'FieldLabel')));?>
 name="newValue"></div><?php if ($_smarty_tpl->tpl_vars['SELECTED_PICKLISTFIELD_NON_EDITABLE_VALUES']->value){?><br><div class="control-label"><?php echo vtranslate('LBL_NON_EDITABLE_PICKLIST_VALUES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</div><div class="controls nonEditableValuesDiv"><ul class="nonEditablePicklistValues" style="list-style-type: none;"><?php  $_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['SELECTED_PICKLISTFIELD_NON_EDITABLE_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE']->key => $_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE']->value){
$_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE_KEY']->value = $_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE']->key;
?><li><?php echo vtranslate($_smarty_tpl->tpl_vars['NON_EDITABLE_VALUE']->value,$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
</li><?php } ?></ul></div><?php }?></div></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('ModalFooter.tpl',$_smarty_tpl->tpl_vars['qualifiedName']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</form></div><?php }} ?>