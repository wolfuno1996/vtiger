<?php /* Smarty version Smarty-3.1.7, created on 2018-05-16 12:03:54
         compiled from "C:\vTiger\vtigercrm6\vtigercrm\includes\runtime/../../layouts/vlayout\modules\QuotingTool\EmailPreviewTemplate.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12435acc778eebc047-71014739%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '00b3365962fecc7c70adca2dca5a4620764f6546' => 
    array (
      0 => 'C:\\vTiger\\vtigercrm6\\vtigercrm\\includes\\runtime/../../layouts/vlayout\\modules\\QuotingTool\\EmailPreviewTemplate.tpl',
      1 => 1523417898,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12435acc778eebc047-71014739',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5acc778f1568e',
  'variables' => 
  array (
    'MODULE' => 0,
    'TRANSACTION_ID' => 0,
    'RECORDID' => 0,
    'TEMPLATEID' => 0,
    'MULTI_RECORD' => 0,
    'EMAIL_SUBJECT' => 0,
    'EMAIL_FIELD_LIST' => 0,
    'EMAIL_FIELD_LABEL' => 0,
    'i' => 0,
    'EMAIL_FIELD_NAME' => 0,
    'allEmailArr' => 0,
    'EMAIL_CONTENT' => 0,
    'CUSTOM_PROPOSAL_LINK' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5acc778f1568e')) {function content_5acc778f1568e($_smarty_tpl) {?>
<div id="massEditContainer" class='modelContainer'><div id="massEdit"><div class="modal-header contentsBackground"><button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button><h3 id="massEditHeader">Preview & Send Email</h3></div><form class="form-horizontal" action="index.php" id="quotingtool_emailtemplate"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
"/><input type="hidden" name="action" value="PDFHandler"/><input type="hidden" name="mode" value="preview_and_send_email"/><input type="hidden" name="transaction_id" value='<?php echo $_smarty_tpl->tpl_vars['TRANSACTION_ID']->value;?>
'/><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORDID']->value;?>
"/><input type="hidden" name="template_id" value='<?php echo $_smarty_tpl->tpl_vars['TEMPLATEID']->value;?>
'/><input type="hidden" name="multi_record" value='<?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['MULTI_RECORD']->value);?>
'/><div name='massEditContent' class="row-fluid"><div class="modal-body"><div class="row-fluid" style="margin: 5px;"><div class="span12"><input type="text" style="width: 98%;" class="input-large" id="email_subject" name="email_subject"placeholder="Email Subject" value="<?php echo $_smarty_tpl->tpl_vars['EMAIL_SUBJECT']->value;?>
"/></div></div><div id="multiEmailContainer"><?php if ($_smarty_tpl->tpl_vars['EMAIL_FIELD_LIST']->value){?><?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(0, null, 0);?><?php $_smarty_tpl->tpl_vars['allEmailArr'] = new Smarty_variable(array(), null, 0);?><?php  $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->_loop = false;
 $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['EMAIL_FIELD_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->key => $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value){
$_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->_loop = true;
 $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME']->value = $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->key;
?><?php $_smarty_tpl->createLocalArrayVariable('allEmailArr', null, 0);
$_smarty_tpl->tpl_vars['allEmailArr']->value[$_smarty_tpl->tpl_vars['i']->value] = $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value;?><div class="control-group"><label class="checkbox"><input type="checkbox" class="emailField" name="selectedEmail[<?php echo $_smarty_tpl->tpl_vars['i']->value++;?>
]" value='<?php echo $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME']->value;?>
' /><span><?php echo $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value;?>
</span></label></div><?php } ?><div class="control-group"><div class="pull-left"><input type="hidden" class="span4 select2 select2-tags"name="ccValues" data-tags='<?php echo json_encode($_smarty_tpl->tpl_vars['allEmailArr']->value);?>
'placeholder="<?php echo vtranslate('CC',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" /></div><div class="pull-left"><input type="hidden" class="span4 select2 select2-tags"name="bccValues" data-tags='<?php echo json_encode($_smarty_tpl->tpl_vars['allEmailArr']->value);?>
'placeholder="<?php echo vtranslate('BCC',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" /></div></div><?php }else{ ?><?php echo vtranslate('Does not have any email to select.',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></div><div class="row-fluid" style="margin: 5px;"><div class="span12"><textarea placeholder="Email Content" id="email_content" name="email_content" rows="5"><?php echo $_smarty_tpl->tpl_vars['EMAIL_CONTENT']->value;?>
</textarea></div></div></div></div><div class="modal-footer"><div class="pull-left custom_proposal_link"><label class="checkbox check_attach_file"><input type="checkbox" name="check_attach_file" /><span><?php echo vtranslate('EMAIL_ATTACH_DOCUMENT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></label><a href="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_PROPOSAL_LINK']->value;?>
" target="_blank"><?php echo vtranslate('EMAIL_DOCUMENT_PREVIEW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div><div class="pull-right cancelLinkContainer" style="margin-top:0;"><a class="cancelLink" type="reset" data-dismiss="modal"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div><button class="btn addButton" type="submit" name="saveButton"><strong><?php echo vtranslate('LBL_SEND',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></div></form></div></div><?php }} ?>