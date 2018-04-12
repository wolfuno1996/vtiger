<?php
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');

$module12 = Vtiger_Module::getInstance('Potentials');
$module21 = Vtiger_Module::getInstance('Project');

global $adb;


$blockON = Vtiger_Block::getInstance('LBL_PROJECT_INFORMATION', $module21);

$fieldON = Vtiger_Field::getInstance('opportunity_name',$module21);
if($fieldON){
    echo "Field opportunity_name exits ";
    //$fieldON->delete();
}else{
    $fieldON = new Vtiger_Field();
    $fieldON->label = 'LBL_EMPLOYEES_LASTNAME';
    $fieldON->name = 'opportunity_name';
    $fieldON->table = 'vtiger_project';
    $fieldON->column = 'opportunity_name';
    $fieldON->columntype = 'varchar(255)';
    $fieldON->uitype = '10';
    $fieldON->typeofdata = 'V~O';
    $fieldON->quickcreate=0;
    $fieldON->summaryfield=1;

    $blockON->addField($fieldON);
}
$fieldON->setRelatedModules(Array('Potentials'));
$rs = $adb->pquery('SELECT * FROM `vtiger_relatedlists` WHERE `tabid` = ? AND `related_tabid` =? AND `name`=?', array($module12->getId(), $module21->getId(), 'get_dependents_list'));
if($adb->num_rows($rs) ==0){
    $module12->setRelatedList($module21, 'Project', array('add','select'), 'get_dependents_list');
}
//ModTracker::enableTrackingForModule($module21->id);
//$module21->setRelatedList(Vtiger_Module::getInstance('Opportunity'), 'Opportunity_Name', array('add','select'),'get_attachments');

