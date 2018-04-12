<?php /* Smarty version Smarty-3.1.7, created on 2018-04-04 03:54:12
         compiled from "C:\xampp\htdocs\vtiger7\vtigercrm\includes\runtime/../../layouts/v7\modules\QuotingTool\ListViewContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5495175695ac434b3db15f5-37225912%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '495489595184ea1bb13800f69dd7e1efd380cf8b' => 
    array (
      0 => 'C:\\xampp\\htdocs\\vtiger7\\vtigercrm\\includes\\runtime/../../layouts/v7\\modules\\QuotingTool\\ListViewContents.tpl',
      1 => 1522809082,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5495175695ac434b3db15f5-37225912',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ac434b4cce8d',
  'variables' => 
  array (
    'MODULE' => 0,
    'MBSTRING' => 0,
    'PHPZIP' => 0,
    'CURRENT_USER_MODEL' => 0,
    'LISTVIEW_HEADERS' => 0,
    'LISTVIEW_HEADER' => 0,
    'TEMPLATES' => 0,
    'LISTVIEW_ENTRY' => 0,
    'COLUMNNAME' => 0,
    'WIDTHTYPE' => 0,
    'LISTVIEW_ENTRIES_COUNT' => 0,
    'IS_MODULE_EDITABLE' => 0,
    'SINGLE_MODULE' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ac434b4cce8d')) {function content_5ac434b4cce8d($_smarty_tpl) {?>
<style>.listViewEntries td {cursor: default;}</style><div class="row"><div class="col-md-6" style="padding: 15px; margin-left: 10px; width: 49%"><button id="Contacts_listView_basicAction_LBL_ADD_RECORD" class="btn addButton"onclick="window.location.href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit'"><i class="icon-plus"></i>&nbsp;<strong><?php echo vtranslate('LBL_ADD',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></div><div class="col-md-6" style="padding: 15px"><p data-toggle="tooltip" <?php if ($_smarty_tpl->tpl_vars['MBSTRING']->value=='installed'){?> hidden<?php }?> title="php-mbstring is php extension that is required for Document Designer to work properly." style="width: 100px; float: left;font-weight: bold;"><i class="glyphicon glyphicon-warning-sign" style="color: red"></i>  php-mbstring</p><p data-toggle="tooltip" <?php if ($_smarty_tpl->tpl_vars['PHPZIP']->value=='installed'){?> hidden<?php }?> title="php-gd is php extension is required to import/load default templates. If you are creating your own template, you don't need to install this." style="width: 100px; float: left;font-weight: bold; margin-left: 10px"><i class="glyphicon glyphicon-warning-sign" style="color: red"></i>  php-gd</p><button type="button" id="exportTemplate" class="btn btn-primary" style="float: right">Export</button><button type="button"  id="importTemplate"  class="btn btn-success" style="float: right; margin-right: 10px;">Import</button><select name="default-templates" id="default-templates" class="select2" aria-labelledby="Default Templates" style="width: 250px; float: right; margin-right: 10px"><option value="Default" selected>Default Templates</option><option value="Light-Blue-Invoice.zip">Light-Blue Invoice Template</option><option value="Light-Blue-Quote.zip">Light-Blue Quote Template</option><option value="Light-Blue-SO.zip">Light-Blue Sales Order Template</option><option value="Light-Blue-PO.zip">Light-Blue Purchase Order Template</option><option value="Gray-Invoice.zip">Gray Invoice Template</option><option value="Gray-Quote.zip">Gray Quote Template</option><option value="Gray-SO.zip">Gray Sales Order Template</option><option value="Gray-PO.zip">Gray Purchase Order Template</option><option value="Green-Invoice.zip">Green Invoice Template</option><option value="Green-Quote.zip">Green Quote Template</option><option value="Green-SO.zip">Green Sales Order Template</option><option value="Green-PO.zip">Green Purchase Order Template</option><option value="Red-Invoice.zip">Red Invoice Template</option><option value="Red-Quote.zip">Red Quote Template</option><option value="Red-SO.zip">Red Sales Order Template</option><option value="Red-PO.zip">Red Purchase Order Template</option><option value="Yellow-Opportunity.zip">Yellow Proposal Template (6 Pages)</option></select><input id="fileupload" type="file" name="files[]" data-url="index.php?module=QuotingTool&action=ActionAjax&mode=importTemplate" multiple style="visibility: hidden;"></div></div><div style="clear: both"></div><div class="listViewContentDiv" id="listViewContents"><div class="listViewEntriesDiv contents-bottomscroll"><div class="bottomscroll-div"><?php $_smarty_tpl->tpl_vars['WIDTHTYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'), null, 0);?><table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['COLUMNNAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['COLUMNNAME']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration++;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration === $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total;
?><th class="text-center"><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</th><?php } ?><th style="width: 100px;"></th></tr></thead><tbody><?php  $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['TEMPLATES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->key => $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listview']['index']++;
?><tr class="listViewEntries" data-id='<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('id');?>
'id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_row_<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['listview']['index']+1;?>
"><?php  $_smarty_tpl->tpl_vars['LISTVIEW_HEADER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = false;
 $_smarty_tpl->tpl_vars['COLUMNNAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_HEADERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key => $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->_loop = true;
 $_smarty_tpl->tpl_vars['COLUMNNAME']->value = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->key;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration++;
 $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last = $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->iteration === $_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->total;
?><td class="<?php if ($_smarty_tpl->tpl_vars['COLUMNNAME']->value=='filename'){?>listViewEntryValue<?php }?> <?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['COLUMNNAME']->value=='id'){?>text-right<?php }?>"data-column="<?php echo $_smarty_tpl->tpl_vars['COLUMNNAME']->value;?>
"><?php if ($_smarty_tpl->tpl_vars['COLUMNNAME']->value=='filename'){?><a href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('id');?>
'><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get($_smarty_tpl->tpl_vars['COLUMNNAME']->value),$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get($_smarty_tpl->tpl_vars['COLUMNNAME']->value));?>
</a><?php }elseif($_smarty_tpl->tpl_vars['COLUMNNAME']->value=='module'){?><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get($_smarty_tpl->tpl_vars['COLUMNNAME']->value),$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get($_smarty_tpl->tpl_vars['COLUMNNAME']->value));?>
<?php }elseif($_smarty_tpl->tpl_vars['COLUMNNAME']->value=='is_active'){?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get($_smarty_tpl->tpl_vars['COLUMNNAME']->value)==1){?>Active<?php }else{ ?>Inactive<?php }?><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get($_smarty_tpl->tpl_vars['COLUMNNAME']->value);?>
<?php }?></td><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_HEADER']->last){?><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><div class="actions text-center"><span class="actionImages"><a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&action=PDFHandler&mode=duplicate&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('id');?>
"><i title="<?php echo vtranslate('LBL_DUPLICATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="fa fa-files-o alignMiddle"></i></a>&nbsp;<a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&action=PDFHandler&mode=download&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('id');?>
"><i title="<?php echo vtranslate('LBL_DOWNLOAD',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-download glyphicon glyphicon-download alignMiddle"></i></a>&nbsp;<a href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('id');?>
'><i title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-pencil glyphicon glyphicon-pencil alignMiddle"></i></a>&nbsp;<a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&action=ActionAjax&mode=delete&record=<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('id');?>
"onclick="return confirm('Are you sure you want to delete template?')"><i title="<?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="icon-trash glyphicon glyphicon-trash alignMiddle"></i></a></span></div></td><?php }?><?php } ?></tr><?php } ?></tbody></table><!--added this div for Temporarily --><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES_COUNT']->value=='0'){?><table class="emptyRecordsDiv"><tbody><tr><td><?php $_smarty_tpl->tpl_vars['SINGLE_MODULE'] = new Smarty_variable("SINGLE_".($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php echo vtranslate('LBL_NO');?>
 <?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate('LBL_FOUND');?>
.<?php if ($_smarty_tpl->tpl_vars['IS_MODULE_EDITABLE']->value){?> <?php echo vtranslate('LBL_CREATE');?>
&nbsp;<a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit"><?php echo vtranslate($_smarty_tpl->tpl_vars['SINGLE_MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a><?php }?></td></tr></tbody></table><?php }?></div></div></div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?php }} ?>