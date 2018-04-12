<?php
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');

$moduleInstance = Vtiger_Module::getInstance('CocaCola');
if($moduleInstance){
    echo "<h3>CocaCola Module da ton tai </h3>";
}else{
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "CocaCola";
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}

$blockInstance = Vtiger_Block::getInstance('LBL_COCACOLA_INFORMATIN',$moduleInstance);
if($blockInstance){
    echo "<h3>The Record Information block already exists</h3><br> \n";
}else{
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_COCACOLA_INFORMATIN';
    $moduleInstance->addBlock($blockInstance);
}
$field1= Vtiger_Field::getInstance('taste',$moduleInstance);
if(!$field1){
    $field1 = new Vtiger_Field();
    $field1->label='LBL_TASTE';
    $field1->name='taste';
    $field1->table='vtiger_cocacola';
    $field1->column = 'taste';
    $field1->uitype=16;
    $field1->typeofdata='V~O';
    $field1->displaytype=1;

    $blockInstance->addField($field1);
    $field1->setPicklistValues(array('Fanta','Pepsi','LemonNade'));
}else{
    $field1->delete();
}
