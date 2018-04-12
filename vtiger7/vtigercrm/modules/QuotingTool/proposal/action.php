<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

chdir(dirname(__FILE__) . '/../../..');
require_once 'config.inc.php';
require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
require_once 'modules/Users/Users.php';
include('modules/QuotingTool/QuotingTool.php');

global $adb, $current_user;
$adb = PearDatabase::getInstance();
$current_user = new Users();
$activeAdmin = $current_user->getActiveAdminUser();
$current_user->retrieve_entity_info($activeAdmin->id, 'Users');

// Submit request
$action = (isset($_REQUEST['_action'])) ? $_REQUEST['_action'] : null;
if ($action) {
    switch ($action) {
        case 'submit':
            submit();
            break;
        case 'download_pdf':
            downloadPdf();
            break;
        case 'get_picklist_values':
            get_picklist_values();
            break;
        case 'get_currency_values':
            get_currency_values();
            break;
        case 'an_paid':
            an_paid();
            break;
        default:
            break;
    }
}

/**
 * Fn - submit
 */
function submit()
{
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
    $quotingTool = new QuotingTool();
    $record = $_REQUEST['record'];  // Transaction id
    $status = $_REQUEST['status'];  // Accept = 1; Decline = -1; Cancel: 0;
    $status_text = $_REQUEST['status_text'];
    $signature = $_REQUEST['signature'];
    $signatureName = $_REQUEST['signature_name'];
    $fullContent = $_REQUEST['content'];
    $description = $_REQUEST['description'];
    $customMappingFields = $_REQUEST['custom_mapping_fields'];
    $childModule = $_REQUEST['child_module'];
    $isCreateNewRecord = $_REQUEST['is_create_new_record'];
    $formCreateNewRecord =  $_REQUEST['form_create_record'];
    $primaryModule = $_REQUEST['module'];
    $timestamp = time();

    $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
    if($formCreateNewRecord !='true'){
        $success1 = $transactionRecordModel->updateSignature($record, $signature, $signatureName, $fullContent, $description);
        $success2 = $transactionRecordModel->changeStatus($record, $status);
    }
    // Template
    /** @var Vtiger_Record_Model $transactionRecord */
    $transactionRecord = $transactionRecordModel->findById($record);

    if (!$transactionRecord) {
        $response->setError(200, vtranslate('LBL_INVALID_DOCUMENT', 'QuotingTool'));
        return $response->emit();
    }

//    $refModule = $transactionRecord->get('module');
    $refId = $transactionRecord->get('record_id');  // Module record id
    $quotingToolRecordModel = new QuotingTool_Record_Model();
    $templateRecord = $quotingToolRecordModel->getById($transactionRecord->get('template_id'));
    $mappingFields = array();
    $tempMappingFields = $templateRecord->get('mapping_fields');
    if ($tempMappingFields) {
        $mappingFields = json_decode(htmlspecialchars_decode($tempMappingFields));
    }
    $recordChildModue = '';

    // Mapping module
    // From custom mapping fields (in form)
    if ($customMappingFields) {
        $tmpCustomMappingFields = json_decode(htmlspecialchars_decode($customMappingFields));
        foreach ($tmpCustomMappingFields as $recordId => $fieldMapping) {
            $mappingFields2 = array();

            foreach ($fieldMapping as $fieldMappingId => $fieldMappingDetail) {
                $fieldMappingValue = $fieldMappingDetail->value;

                switch ($fieldMappingDetail->datatype) {
                    case 'date':
                        $fieldMappingValue = Vtiger_Date_UIType::getDBInsertedValue($fieldMappingValue);
                        break;
                    case 'time':
                        $fieldMappingValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldMappingValue);
                        break;
                    case 'currency':
                        $fieldMappingValue = CurrencyField::convertToDBFormat($fieldMappingValue);
                        break;
                    default:
                        break;
                }

                $objMappingField2 = array(
                    'selected-field' => $fieldMappingId,
                    'selected-value' => $fieldMappingValue,
                    'type' => 1 // Only update when accept proposal
                );
                $objMappingField2 = (object)$objMappingField2;

                $mappingFields2[] = $objMappingField2;
            }
            if ($formCreateNewRecord == true) {
                $recordId = 0;
            }
             $recordChildModue =  mappingData($recordId, $mappingFields2, $status, $isCreateNewRecord, $childModule, $primaryModule);
        }
    }
    if (count($mappingFields) > 0 && $isCreateNewRecord != true) {
        mappingData($refId, $mappingFields, $status, $isCreateNewRecord, $childModule, $primaryModule);
//        mappingData($refId, $mappingFields, $status);
    }

    // Create PDF file
    $temFilename = $templateRecord->get('filename');
    $tempHeader = $templateRecord->get('header');
    $tempFooter = $templateRecord->get('footer');
    $pdfContent = $fullContent ? base64_decode($fullContent) : '';
    $pdfHeader = $tempHeader ? base64_decode($tempHeader) : '';
    $pdfFooter = $tempFooter ? base64_decode($tempFooter) : '';
    // File name
    $pdfName = $quotingTool->makeUniqueFile($temFilename);
    // Create PDF
    $pdf = $quotingTool->createPdf($pdfContent, $pdfHeader, $pdfFooter, $pdfName);

    if($pdf){
        global $adb,$current_user,$HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME,$site_URL;
        $results= $adb->pquery("SELECT id FROM vtiger_users WHERE is_admin='on' ORDER BY id ASC limit 1",array());
        if($adb->num_rows($results)>0) {
            $userId = $adb->query_result($results, 0, 'id');
        }

        $current_user=Users::getInstance('Users');
        $current_user->retrieve_entity_info($userId,'Users');
        createDocument($userId,$pdfName,$pdf);

//        require_once("modules/Emails/mail.php");
//        $subject = 'New Document';
//        $contents = 'Hi,';
//        $contents .= "<br>form has been signed.";
//        $contents .= "<br><br>title: ".$pdfName;
//        $contents .= "<br><br>file name: ". $pdfName;
//        $contents .= "<br><br>description:";
//        $contents .= "<br><br> Thanks";

//        send_mail('', $current_user->email1, 'New Document', $HELPDESK_SUPPORT_EMAIL_ID, $subject, $contents,'','','','','',true);
    }
//    // Add new SignatureRecord
//    $signedRecordModel = Vtiger_Module_Model::getInstance($module_SignedRecord);
//    $relatedModules = $quotingTool->getRelatedModules($signedRecordModel);
//    /** @var Vtiger_Module_Model $refModuleModel */
//    $refModuleModel = $relatedModules[$refModule];
    $newSignedRecord = array(
        'signature' => $signature,
        'signature_name' => $signatureName,
        'signature_date' => date('Y-m-d', $timestamp),
        'cf_signature_time' => date('H:i:s', $timestamp),
        'filename' => $pdf,
        'signedrecord_status' => $status_text,
//        'related_to' => $refId
    );

    $signedrecordId = (isset($_REQUEST['signedrecord_id']) && $_REQUEST['signedrecord_id']) ? intval($_REQUEST['signedrecord_id']) : 0;
    if ($signedrecordId) {
        $newSignedRecord['signedrecord_type'] = SignedRecord_Record_Model::TYPE_SIGNED;
    }

    saveSignedRecord($signedrecordId, $newSignedRecord);

    return $response->emit();
}

/**
 * @param $refId
 * @param $tempMappingFields
 * @param $status
 */
function mappingData($refId, $tempMappingFields, $status,$isCreateNewRecord = false, $childModule, $primaryModule)
{
    $mappingFields = array();
    foreach ($tempMappingFields as $k => $field) {
        $vField = Vtiger_Field_Model::getInstance($field->{'selected-field'});

        if (($field->type == $status) && $vField) {
            $mappingFields[$vField->get('name')] = $field->{'selected-value'};
        }
    }

    // Mapping module
    if ($isCreateNewRecord == true && ($status != 0 && $status != -1)) {
        $mappingModel = Vtiger_Record_Model::getCleanInstance($childModule);
        foreach ($mappingFields as $field => $value) {
            $mappingModel->set($field, $value);
        }
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($refId);
        $parentModuleName = $parentRecordModel->getModule()->getName();
        $parentField = getParentField($parentModuleName, $childModule);
        if ($parentField != '') {
            $mappingModel->set($parentField, $refId);
        }
        $mappingModel->save();
        return $mappingModel->getId();
    }else{
        if (($refId == 0 || !isRecordExists($refId)) && ($status !=0 && $status != -1)) {
            $mappingModel = Vtiger_Record_Model::getCleanInstance($primaryModule);
        }else{
            if ($refId == 0) {
                return;
            }
            $mappingModel = Vtiger_Record_Model::getInstanceById($refId);
            $mappingModel->set('id', $refId);
            $mappingModel->set('mode', 'edit');
        }

        foreach ($mappingFields as $field => $value) {
            $mappingModel->set($field, $value);
        }
        return $mappingModel->save();
    }
}

function getParentField($parentModule, $childModule)
{
    global $adb;
    $uitypes=array();
    // Get related modules
    switch ($parentModule){
        case "Accounts":
            $uitypes=array('51','73','68');
            break;
        case "Contacts":
            $uitypes=array('57','68');
            break;
        case "Campaigns":
            $uitypes=array('58');
            break;
        case "Products":
            $uitypes=array('59');
            break;
        case "Vendors":
            $uitypes=array('75','81');
            break;
        case "Potentials":
            $uitypes=array('76');
            break;
        case "Quotes":
            $uitypes=array('78');
            break;
        case "SalesOrder":
            $uitypes=array('80');
            break;
    };
    if (empty($uitypes)) {
        $uitypes = array('10');
    }
    $queryChild="SELECT * FROM (
            SELECT vtiger_tab.tabid, vtiger_tab.`name` as relmodule , vtiger_field.`fieldname`
            FROM `vtiger_field`
            INNER JOIN vtiger_tab ON vtiger_field.tabid=vtiger_tab.tabid
            WHERE vtiger_field.presence <> 1 AND uitype IN (" . generateQuestionMarks($uitypes) . ")
            UNION
            SELECT vtiger_field.tabid,vtiger_fieldmodulerel.module as relmodule, vtiger_field.`fieldname`
            FROM vtiger_fieldmodulerel
            INNER JOIN vtiger_field ON vtiger_fieldmodulerel.fieldid=vtiger_field.fieldid
            WHERE vtiger_field.presence <> 1 AND uitype = 10 AND relmodule = ?
            ) as temp
            WHERE `relmodule` NOT IN ('Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter','PBXManager')
             AND `relmodule` =?
            ";
    $rs = $adb->pquery($queryChild, array($uitypes, $parentModule, $childModule));
    $parentField = '';
    if ($adb->num_rows($rs) > 0) {
        while ($row = $adb->fetchByAssoc($rs)) {
            $parentField = $row['fieldname'];
            break;
        }
       return $parentField ;
    }
    return '';
}

/** Fn - createSignedRecord
 * @param int $id
 * @param array $data
 */
function saveSignedRecord($id, $data)
{
    $signedRecordModel = null;
    if ($id) {
        $signedRecordModel = Vtiger_Record_Model::getInstanceById($id);
        $signedRecordModel->set('id', $id);
        $signedRecordModel->set('mode', 'edit');
    } else {
        $signedRecordModel = Vtiger_Record_Model::getCleanInstance('SignedRecord');
    }

    foreach ($data as $field => $value) {
        $signedRecordModel->set($field, $value);
    }

    return $signedRecordModel->save();  // return Id
}

/**
 * Fn - downloadPdf
 */
function downloadPdf()
{
    global $current_user, $site_URL;

    $quotingTool = new QuotingTool();
    $transactionId = $_REQUEST['record'];
    $entityId = $_REQUEST['record_id'];
    $moduleName = $_REQUEST['module'];
    $name = $_REQUEST['name'];
    $pdfContent = $_REQUEST['content'] ? base64_decode($_REQUEST['content']) : '';
    $pdfHeader = $_REQUEST['header'] ? base64_decode($_REQUEST['header']) : '';
    $pdfFooter = $_REQUEST['footer'] ? base64_decode($_REQUEST['footer']) : '';


    $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
    /** @var Vtiger_Record_Model $transactionRecord */
    $transactionRecord = $transactionRecordModel->findById($transactionId);
    $hash = $transactionRecord->get('hash');
    $hash = $hash ? $hash : '';

    // Merge special tokens
    $keys_values = array();
    $site = rtrim($site_URL, '/');
    $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$transactionId}&session={$hash}";
    $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);

    // Parse
    $pdfHeader = $quotingTool->parseTokens($pdfHeader, $moduleName, $entityId);
    //company token
    $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
    $companyfields = array();
    foreach ($companyModel->getFields() as $key => $val) {
        if ($key == 'logo') {
            continue;
        }
        $companyfields["$"."Vtiger_Company_".$key."$"] = $companyModel->get($key);
    }

    $varFooter = $quotingTool->getVarFromString($pdfFooter);

    foreach ($varFooter as $var) {
        if ($var == '$custom_proposal_link$') {
            $keys_values['$custom_proposal_link$'] = $compactLink;
        } else if ($var == '$custom_user_signature$') {
            $keys_values['$custom_user_signature$'] = $current_user->signature;
        }
        if (array_key_exists($var, $companyfields)) {
            $keys_values[$var] = $companyfields[$var];
        }
    }
    if (!empty($keys_values)) {
        $pdfFooter = $quotingTool->mergeCustomTokens($pdfFooter, $keys_values);
    }


    $pdfFooter = $quotingTool->parseTokens($pdfFooter, $moduleName, $entityId);

    // File name
    $pdfName = $quotingTool->makeUniqueFile($name);
    // Create PDF
    $pdf = $quotingTool->createPdf($pdfContent, $pdfHeader, $pdfFooter, $pdfName);

    // Download the file
    header('Content-Type: application/octet-stream');
    header('Content-disposition: attachment; filename="' . $pdfName . '"');
    header('Content-Length: ' . filesize($pdf));
    print readfile($pdf);
    exit;
}

/**
 * Fn - get_picklist_values
 */
function get_picklist_values()
{
    $fieldModules = isset($_REQUEST['fields']) ? $_REQUEST['fields'] : null;
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
    $data = array();

    if (!$fieldModules) {
        $response->setResult($data);
        $response->emit();
    }

    foreach ($fieldModules as $moduleName => $fields) {
        $module = Vtiger_Module_Model::getInstance($moduleName);

        foreach ($fields as $fieldName => $fieldValue) {
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $module);
            if (!$fieldModel) {
                continue;
            }

            $data[$moduleName][$fieldName] = array();
            $datatype = $fieldModel->getFieldDataType();

            if ($datatype == 'picklist') {
                $data[$moduleName][$fieldName][''] = vtranslate('Select an Option');
            }

            $data[$moduleName][$fieldName] = array_merge($data[$moduleName][$fieldName], $fieldModel->getPicklistValues());
        }
    }

    $response->setResult($data);
    $response->emit();
}

/**
 * Fn - get_currency_values
 */
function get_currency_values()
{
    $fieldModules = isset($_REQUEST['fields']) ? $_REQUEST['fields'] : null;
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
    $data = array();

    if (!$fieldModules) {
        $response->setResult($data);
        $response->emit();
    }

    foreach ($fieldModules as $moduleName => $fields) {
        $module = Vtiger_Module_Model::getInstance($moduleName);

        foreach ($fields as $fieldName => $fieldValue) {
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $module);
            $data[$moduleName][$fieldName] = $fieldModel->getCurrencyList();

            foreach ($data[$moduleName][$fieldName] as $k => $cf) {
                $data[$moduleName][$fieldName][$k] = vtranslate($cf, $moduleName);
            }
        }
    }

    $response->setResult($data);
    $response->emit();
}

/**
 * Fn - submit payment to Authorize.Net
 */
function an_paid()
{
    require_once "modules/ANCustomers/libs/InvoiceWidget/QuotingTool.php";
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);

    $anQuotingTool = new ANQuotingTool();
    $paid_status = $anQuotingTool->ANPaid();

    $response->setResult($paid_status);
    $response->emit();
}
function createDocument($userId,$file_name, $oldFilePath)
{
    global $upload_badext;
    $adb = PearDatabase::getInstance();
    $currentUserModel = Users_Record_Model::getCurrentUserModel();


    $binFile = sanitizeUploadFileName($file_name, $upload_badext);

    $current_id = $adb->getUniqueID("vtiger_crmentity");

    $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
    $filetype =  'application/pdf';
    $filesize = '';

    //get the file path inwhich folder we want to upload the file
    $upload_file_path = decideFilePath();
    $newFilePath = $upload_file_path . $current_id . "_" . $binFile;

    copy($oldFilePath, $newFilePath);
    $attach_id = saveAttachment($userId,$file_name,$oldFilePath);
    // Create document record
    $document = CRMEntity::getInstance('Documents');
    $document->column_fields['notes_title'] = $file_name;
    $document->column_fields['filename'] = basename($oldFilePath);
    $document->column_fields['filestatus'] = 1;
    $document->column_fields['filetype'] = $filetype;
    $document->column_fields['filelocationtype'] = 'I';
    $document->column_fields['folderid'] = '';
    $document->column_fields['filesize'] = filesize($oldFilePath);
    $document->column_fields['assigned_user_id'] = $userId;
    $document->save('Documents');
    $doc_id = $document->id;

    // Link file attached to document
    $adb->pquery("INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",
        Array($doc_id, $attach_id));

    return false;
}
function saveAttachment($userId,$file_name, $oldFilePath)
{
    global $upload_badext;
    $adb = PearDatabase::getInstance();
    $date_var = date("Y-m-d H:i:s");

    $binFile = sanitizeUploadFileName($file_name, $upload_badext);

    $current_id = $adb->getUniqueID("vtiger_crmentity");

    $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
    $filetype =  'application/pdf';
    $filesize = '';

    //get the file path inwhich folder we want to upload the file
    $upload_file_path = decideFilePath();
    $newFilePath = $upload_file_path . $current_id . "_" . $binFile;

    copy($oldFilePath, $newFilePath);
    $sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
    $params1 = array($current_id, $userId, $userId, "Documents Attachment", '', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
    $adb->pquery($sql1, $params1);

    $sql2 = "insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
    $params2 = array($current_id, $filename, '', $filetype, $upload_file_path);
    $result = $adb->pquery($sql2, $params2);

    /*$sql3 = 'insert into vtiger_seattachmentsrel values(?,?)';
    $adb->pquery($sql3, array($recordModel->getId(), $current_id));*/
    return $current_id;
}