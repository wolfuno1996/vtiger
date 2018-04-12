<?php
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');
$moduleInstance = Vtiger_Module::getInstance('Transactions');
if ($moduleInstance)
{
    echo "<h2>Transactions Module already exists </h2><br>";
}else{
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Transactions';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}
$blockInstance = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The Record Update Information block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_RECORD_UPDATE_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}
$field1 = Vtiger_Field::getInstance('name', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_NAME';
    $field1->name = 'name';
    $field1->table = 'vtiger_transactions';
    $field1->column = 'name';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $blockInstance->addField($field1);
}
$field2 = Vtiger_Field::getInstance('phone', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_PHONE';
    $field2->name = 'phone';
    $field2->table = 'vtiger_transactions';
    $field2->column = 'phone';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $blockInstance->addField($field2);
}
$filter1 = Vtiger_Filter::getInstance('All', $moduleInstance);
if(!$filter1) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field1)->addField($field2, 1);
}