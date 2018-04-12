<?php /* Smarty version Smarty-3.1.7, created on 2018-04-04 02:26:00
         compiled from "C:\xampp\htdocs\vtiger7\vtigercrm\includes\runtime/../../layouts/v7\modules\QuotingTool\EmailPreviewTemplate.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5006351235ac437b822bc07-36777243%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4637246e9c4eeb801c2a2e69b981b305432ec673' => 
    array (
      0 => 'C:\\xampp\\htdocs\\vtiger7\\vtigercrm\\includes\\runtime/../../layouts/v7\\modules\\QuotingTool\\EmailPreviewTemplate.tpl',
      1 => 1522807477,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5006351235ac437b822bc07-36777243',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'TRANSACTION_ID' => 0,
    'RECORDID' => 0,
    'TEMPLATEID' => 0,
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
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ac437b856398',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ac437b856398')) {function content_5ac437b856398($_smarty_tpl) {?>
<div class="modal myModal fade in" style="display: block;" aria-hidden="false"><div class="modal-backdrop fade in"></div><div class="modal-dialog modal-lg"><div class="modal-content"><form class="form-horizontal" method="post" action="index.php" id="quotingtool_emailtemplate"><div class="modal-header"><div class="clearfix"><div class="pull-right "><button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button></div><h4 class="pull-left"><?php echo vtranslate('Preview & Send Email',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4></div></div><div class="modal-body"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
"/><input type="hidden" name="action" value="PDFHandler"/><input type="hidden" name="mode" value="preview_and_send_email"/><input type="hidden" name="transaction_id" value='<?php echo $_smarty_tpl->tpl_vars['TRANSACTION_ID']->value;?>
'/><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORDID']->value;?>
"/><input type="hidden" name="template_id" value='<?php echo $_smarty_tpl->tpl_vars['TEMPLATEID']->value;?>
'/><div class="row-fluid" style="margin: 5px;"><div class="span12"><input type="text" name="email_subject" class="input-large" id="email_subject"placeholder="Email Subject" value="<?php echo $_smarty_tpl->tpl_vars['EMAIL_SUBJECT']->value;?>
"style="width: 98%;"/></div></div><div id="multiEmailContainer"><?php if ($_smarty_tpl->tpl_vars['EMAIL_FIELD_LIST']->value){?><?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(0, null, 0);?><?php $_smarty_tpl->tpl_vars['allEmailArr'] = new Smarty_variable(array(), null, 0);?><?php  $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->_loop = false;
 $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['EMAIL_FIELD_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->key => $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value){
$_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->_loop = true;
 $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME']->value = $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->key;
?><?php $_smarty_tpl->createLocalArrayVariable('allEmailArr', null, 0);
$_smarty_tpl->tpl_vars['allEmailArr']->value[$_smarty_tpl->tpl_vars['i']->value] = $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value;?><div class="control-group" style="margin-left: 25px; margin-bottom: 10px"><label class="checkbox"><input type="checkbox" class="emailField" name="selectedEmail[<?php echo $_smarty_tpl->tpl_vars['i']->value++;?>
]" value='<?php echo $_smarty_tpl->tpl_vars['EMAIL_FIELD_NAME']->value;?>
' /><span style="padding-left: 10px"><?php echo $_smarty_tpl->tpl_vars['EMAIL_FIELD_LABEL']->value;?>
</span></label></div><?php } ?><div class="control-group clearfix" style="margin-bottom: 10px"><div class="pull-left"><input type="hidden" class="span4 form-control select2 select2-tags"name="ccValues" data-tags='<?php echo json_encode($_smarty_tpl->tpl_vars['allEmailArr']->value);?>
'placeholder="<?php echo vtranslate('CC',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" style="width: 300px; margin-right: 10px" /></div><div class="pull-left"><input type="hidden" class="span4 form-control select2 select2-tags"name="bccValues" data-tags='<?php echo json_encode($_smarty_tpl->tpl_vars['allEmailArr']->value);?>
'placeholder="<?php echo vtranslate('BCC',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" style="width: 300px" /></div></div><?php }else{ ?><?php echo vtranslate('Does not have any email to select.',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></div><div class="row-fluid" style="margin: 5px;"><div class="span12"><textarea placeholder="Email Content" id="email_content" name="email_content" rows="5"><?php echo $_smarty_tpl->tpl_vars['EMAIL_CONTENT']->value;?>
</textarea></div></div></div><div class="modal-footer"><div class="pull-left custom_proposal_link"><label class="check_attach_file text-left" style="display: block;"><input type="checkbox" name="check_attach_file" />&nbsp;<span><?php echo vtranslate('EMAIL_ATTACH_DOCUMENT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></label><a href="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_PROPOSAL_LINK']->value;?>
" target="_blank"><?php echo vtranslate('EMAIL_DOCUMENT_PREVIEW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div><div class="pull-right cancelLinkContainer" style="margin-top:0;"><a class="cancelLink" type="reset" data-dismiss="modal"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div><button class="btn addButton" type="submit" name="saveButton"><strong><?php echo vtranslate('LBL_SEND',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></div></form></div></div></div><?php }} ?>