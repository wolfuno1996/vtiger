<?php

$Vtiger_Utils_Log = true;
require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('modules/ModTracker/ModTracker.php');

$moduleInstance = Vtiger_Module::getInstance('Employees');

if($moduleInstance){
    echo "Module Ton Tai";
}else{
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "Employees";
    $moduleInstance->parent='Support';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}

ModTracker::enableTrackingForModule($moduleInstance->id);
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Contact'), 'Contacts', array('add','select'),'get_attachments');

// Block Employess
$blockEmployees = Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION',$moduleInstance);

if($blockEmployees){
    echo 'Block Employess Informatin Exits';
}else{
    $blockEmployees = new Vtiger_Block();
    $blockEmployees->label = 'LBL_EMPLOYEES_INFORMATION';
    $moduleInstance->addBlock($blockEmployees);
}

// Field FirstName
$fieldFN = Vtiger_Field::getInstance('first_name',$moduleInstance);
if($fieldFN){
    echo "Field first_name Exits ";
}else{
    $fieldFN = new Vtiger_Field();
    $fieldFN->label = 'LBL_EMPLOYEES_FIRSTNAME';
    $fieldFN->name = 'first_name';
    $fieldFN->table = 'vtiger_employees';
    $fieldFN->column = 'first_name';
    $fieldFN->columntype = 'varchar(255)';
    $fieldFN->uitype = '1';
    $fieldFN->typeofdata = 'V~O';
    $fieldFN->quickcreate=0;
    $fieldFN->summaryfield=1;

    $blockEmployees->addField($fieldFN);
}

//Field LastName

$fieldLN = Vtiger_Field::getInstance('last_name',$moduleInstance);
if($fieldLN){
    echo "Field last_name Exits ";
}else{
    $fieldLN = new Vtiger_Field();
    $fieldLN->label = 'LBL_EMPLOYEES_LASTNAME';
    $fieldLN->name = 'last_name';
    $fieldLN->table = 'vtiger_employees';
    $fieldLN->column = 'last_name';
    $fieldLN->columntype = 'varchar(255)';
    $fieldLN->uitype = '1';
    $fieldLN->typeofdata = 'V~M';
    $fieldLN->quickcreate=0;
    $fieldLN->summaryfield=1;
    $blockEmployees->addField($fieldLN);
}

//Field gender

$fieldGen = Vtiger_Field::getInstance('gender',$moduleInstance);
if($fieldGen){
    echo "Field gender Exits ";
}else{
    $fieldGen = new Vtiger_Field();
    $fieldGen->label = 'LBL_EMPLOYEES_GENDER';
    $fieldGen->name = 'gender';
    $fieldGen->table = 'vtiger_employees';
    $fieldGen->column = 'gender';
    $fieldGen->columntype = 'varchar(255)';
    $fieldGen->uitype = '16';
    $fieldGen->typeofdata = 'V~O';
    $fieldGen->quickcreate=0;
    $fieldGen->summaryfield=1;
    $blockEmployees->addField($fieldGen);
}

//Field Data Of Birth

$fieldDate = Vtiger_Field::getInstance('date',$moduleInstance);
if($fieldDate){
    echo "Field date Exits ";
}else{
    $fieldDate = new Vtiger_Field();
    $fieldDate->label = 'LBL_EMPLOYEES_DATE';
    $fieldDate->name = 'date';
    $fieldDate->table = 'vtiger_employees';
    $fieldDate->column = 'date';
    $fieldDate->columntype = 'varchar(255)';
    $fieldDate->uitype = '5';
    $fieldDate->typeofdata = 'D~O';
    $fieldDate->quickcreate=0;
    $fieldDate->summaryfield=1;

    $blockEmployees->addField($fieldDate);
}

// Field Email

$fieldEmail = Vtiger_Field::getInstance('email',$moduleInstance);
if($fieldEmail){
    echo "Field Email Exits ";
}else{
    $fieldEmail = new Vtiger_Field();
    $fieldEmail->label = 'LBL_EMPLOYEES_EMAIL';
    $fieldEmail->name = 'email';
    $fieldEmail->table = 'vtiger_employees';
    $fieldEmail->column = 'email';
    $fieldEmail->columntype = 'varchar(255)';
    $fieldEmail->uitype = '13';
    $fieldEmail->typeofdata = 'V~O';
    $fieldEmail->quickcreate=0;
    $fieldEmail->summaryfield=1;
    $blockEmployees->addField($fieldEmail);
}

//Field MobilePhone
$fieldPhone = Vtiger_Field::getInstance('phone',$moduleInstance);
if($fieldPhone){
    echo "Field phone Exits ";
}else{
    $fieldPhone = new Vtiger_Field();
    $fieldPhone->label = 'LBL_EMPLOYEES_PHONE';
    $fieldPhone->name = 'phone';
    $fieldPhone->table = 'vtiger_employees';
    $fieldPhone->column = 'phone';
    $fieldPhone->columntype = 'varchar(255)';
    $fieldPhone->uitype = '1';
    $fieldPhone->typeofdata = 'V~O';
    $fieldPhone->quickcreate=0;
    $fieldPhone->summaryfield=1;
    $blockEmployees->addField($fieldPhone);
}

//Block Address Information

$blockAddress = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION',$moduleInstance);

if($blockAddress){
    echo 'Block Address Informatin Exits';
}else{
    $blockAddress = new Vtiger_Block();
    $blockAddress->label = 'LBL_ADDRESS_INFORMATION';
    $moduleInstance->addBlock($blockAddress);
}

//Field Street
$fieldStreet = Vtiger_Field::getInstance('street',$moduleInstance);
if($fieldStreet){
    echo "Field street Exits ";
}else{
    $fieldStreet = new Vtiger_Field();
    $fieldStreet->label = 'LBL_EMPLOYEES_STREET';
    $fieldStreet->name = 'street';
    $fieldStreet->table = 'vtiger_employees';
    $fieldStreet->column = 'street';
    $fieldStreet->columntype = 'varchar(255)';
    $fieldStreet->uitype = '19';
    $fieldStreet->typeofdata = 'V~O';
    $fieldStreet->quickcreate=0;
    $fieldStreet->summaryfield=1;

    $blockAddress->addField($fieldStreet);
}

// Field City

$fieldCity = Vtiger_Field::getInstance('city',$moduleInstance);
if($fieldCity){
    echo "Field city Exits ";
}else{
    $fieldCity = new Vtiger_Field();
    $fieldCity->label = 'LBL_EMPLOYEES_CITY';
    $fieldCity->name = 'city';
    $fieldCity->table = 'vtiger_employees';
    $fieldCity->column = 'city';
    $fieldCity->columntype = 'varchar(255)';
    $fieldCity->uitype = '1';
    $fieldCity->typeofdata = 'V~O';
    $fieldCity->quickcreate=0;
    $fieldCity->summaryfield=1;

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', "Employees", 'EMP', 1);

    $blockAddress->addField($fieldCity);
}

// Field State
$fieldState = Vtiger_Field::getInstance('state',$moduleInstance);
if($fieldState){
    echo "Field state Exits ";
}else{
    $fieldState = new Vtiger_Field();
    $fieldState->label = 'LBL_EMPLOYEES_STATE';
    $fieldState->name = 'state';
    $fieldState->table = 'vtiger_employees';
    $fieldState->column = 'state';
    $fieldState->columntype = 'varchar(255)';
    $fieldState->uitype = '1';
    $fieldState->typeofdata = 'V~O';
    $fieldState->quickcreate=0;
    $fieldState->summaryfield=1;

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', "Employees", 'EMP', 1);

    $blockAddress->addField($fieldState);
}
// Field Zip
$fieldZip = Vtiger_Field::getInstance('zip',$moduleInstance);
if($fieldZip){
    echo "Field state Exits ";
}else{
    $fieldZip = new Vtiger_Field();
    $fieldZip->label = 'LBL_EMPLOYEES_ZIP';
    $fieldZip->name = 'zip';
    $fieldZip->table = 'vtiger_employees';
    $fieldZip->column = 'zip';
    $fieldZip->columntype = 'varchar(255)';
    $fieldZip->uitype = '1';
    $fieldZip->typeofdata = 'V~O';
    $fieldZip->quickcreate=0;
    $fieldZip->summaryfield=1;

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', "Employees", 'EMP', 1);

    $blockAddress->addField($fieldZip);
}

//Filed Country
$fieldCountry = Vtiger_Field::getInstance('country',$moduleInstance);
if($fieldZip){
    echo "Field Country Exits ";
}else{
    $fieldCountry = new Vtiger_Field();
    $fieldCountry->label = 'LBL_EMPLOYEES_COUNTRY';
    $fieldCountry->name = 'country';
    $fieldCountry->table = 'vtiger_employees';
    $fieldCountry->column = 'country';
    $fieldCountry->columntype = 'varchar(255)';
    $fieldCountry->uitype = '1';
    $fieldCountry->typeofdata = 'V~O';
    $fieldCountry->quickcreate=0;
    $fieldCountry->summaryfield=1;

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', "Employees", 'EMP', 1);

    $blockAddress->addField($fieldCountry);
}

//Block Job Information

$blockJob = Vtiger_Block::getInstance('LBL_JOB_INFORMATION',$moduleInstance);

if($blockJob){
    echo 'Block Address Informatin Exits';
}else{
    $blockJob = new Vtiger_Block();
    $blockJob->label = 'LBL_JOB_INFORMATION';
    $moduleInstance->addBlock($blockJob);
}

//Field Type
$fieldType = Vtiger_Field::getInstance('type',$moduleInstance);
if($fieldType){
    echo "Field Type Exits ";
}else{
    $fieldType = new Vtiger_Field();
    $fieldType->label = 'LBL_EMPLOYEES_TYPE';
    $fieldType->name = 'type';
    $fieldType->table = 'vtiger_employees';
    $fieldType->column = 'type';
    $fieldType->columntype = 'varchar(255)';
    $fieldType->uitype = '16';
    $fieldType->typeofdata = 'V~O';
    $fieldType->quickcreate=0;
    $fieldType->summaryfield=1;
    $blockJob->addField($fieldType);
    $fieldType->setPicklistValues(array('value1','value2'));
}

//Field Salary
$fieldSalary = Vtiger_Field::getInstance('salary',$moduleInstance);
if($fieldSalary){
    echo "Field Type Exits ";
}else{
    $fieldSalary = new Vtiger_Field();
    $fieldSalary->label = 'LBL_EMPLOYEES_SALARY';
    $fieldSalary->name = 'salary';
    $fieldSalary->table = 'vtiger_employees';
    $fieldSalary->column = 'salary';
    $fieldSalary->columntype = 'varchar(255)';
    $fieldSalary->uitype = '71';
    $fieldSalary->typeofdata = 'N~O';
    $fieldSalary->quickcreate=0;
    $fieldSalary->summaryfield=1;

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', "Employees", 'EMP', 1);

    $blockJob->addField($fieldSalary);
}
// Field Contacts
$fieldContacts = Vtiger_Field::getInstance('contacts',$moduleInstance);
if($fieldContacts){
    echo "Field contacts Exits ";
}else{
    $fieldContacts = new Vtiger_Field();
    $fieldContacts->label = 'LBL_EMPLOYEES_CONTACTS';
    $fieldContacts->name = 'contacts';
    $fieldContacts->table = 'vtiger_employees';
    $fieldContacts->column = 'contacts';
    $fieldContacts->columntype = 'varchar(255)';
    $fieldContacts->uitype = '75';
    $fieldContacts->typeofdata = 'I~O';
    $fieldContacts->quickcreate=0;
    $fieldContacts->summaryfield=1;

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', "Employees", 'EMP', 1);

    $blockJob->addField($fieldContacts);
    $fieldContacts->setRelatedModules(array('Contacts'));
}

// Block Descripts

$blockDes = Vtiger_Block::getInstance('LBL_DES_INFORMATION',$moduleInstance);

if($blockDes){
    echo 'Block DES Informatin Exits';
}else{
    $blockDes = new Vtiger_Block();
    $blockDes->label = 'LBL_DES_INFORMATION';
    $moduleInstance->addBlock($blockDes);
}

//Field Des
$fieldDes = Vtiger_Field::getInstance('des',$moduleInstance);
if($fieldDes){
    echo "Field Des Exits ";
}else{
    $fieldDes = new Vtiger_Field();
    $fieldDes->label = 'LBL_EMPLOYEES_DES';
    $fieldDes->name = 'des';
    $fieldDes->table = 'vtiger_employees';
    $fieldDes->column = 'des';
    $fieldDes->columntype = 'varchar(255)';
    $fieldDes->uitype = '19';
    $fieldDes->typeofdata = 'N~O';
    $fieldDes->quickcreate=0;
    $fieldDes->summaryfield=1;

    $blockDes->addField($fieldDes);
}
$filter1 = Vtiger_Filter::getInstance('All', $moduleInstance);
if(!$filter1) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($fieldFN)->addField($fieldLN, 2)->addField($fieldEmail, 3)->addField($fieldPhone, 4);
}
