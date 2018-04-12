<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

include('modules/Emails/mail.php');
include('modules/QuotingTool/QuotingTool.php');
include('modules/QuotingTool/resources/mpdf/mpdf.php');

/**
 * Class QuotingTool_PDFHandler_Action
 */
class QuotingTool_PDFHandler_Action extends Vtiger_Action_Controller
{
    /**
     * Fn - __construct
     */
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('export');
//        $this->exposeMethod('send_email');
        $this->exposeMethod('download');
//        $this->exposeMethod('download_with_signature');
        $this->exposeMethod('preview_and_send_email');
        $this->exposeMethod('duplicate');
        $this->vteLicense();
    }

    /**
     *
     */
    function vteLicense()
    {
        $vTELicense = new QuotingTool_VTELicense_Model('QuotingTool');
        if (!$vTELicense->validate()) {
            header("Location: index.php?module=QuotingTool&view=List&mode=step2");
        }
    }

    /**
     * @param Vtiger_Request $request
     * @return bool
     */
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    /**
     * Fn - process
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * Fn - downloadPreview
     * Save PDF content to the file
     *
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function export(Vtiger_Request $request)
    {
        global $site_URL, $current_user;

        $moduleName = $request->getModule();
        $entityId = $request->get('record');
        $templateId = $request->get('template_id');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($templateId);

        if (!$record) {
            echo vtranslate('LBL_NOT_FOUND', $moduleName);
            exit;
        }

        $quotingTool = new QuotingTool();
        $module = $record->get('module');
        // get $token before decompileRecord because when after decompileRecord , get $token incorrect (\$)
        $varContent = $quotingTool->getVarFromString(base64_decode($record->get('content')));
        $varHeader = $quotingTool->getVarFromString(base64_decode($record->get('header')));
        $varFooter = $quotingTool->getVarFromString(base64_decode($record->get('footer')));


        $record = $record->decompileRecord($entityId, array('header', 'content', 'footer'));
        // File name
        $fileName = $quotingTool->makeUniqueFile($record->get('filename'));

        /** @var QuotingTool_Record_Model $model */
        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
        // Encode before put to database
        $full_content = base64_encode($record->get('content'));
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $module, $entityId, null, null, $full_content, $record->get('description'));
        $transactionRecord = $transactionRecordModel->findById($transactionId);
        $hash = $transactionRecord->get('hash');
        $hash = $hash ? $hash : '';

        // Merge special tokens
        $keys_values = array();
        $site = rtrim($site_URL, '/');
        $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$transactionId}&session={$hash}";
        $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);

        //company token
        $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
        $companyfields = array();
        foreach ($companyModel->getFields() as $key => $val) {
            if ($key == 'logo') {
                continue;
            }
            $companyfields["$"."Vtiger_Company_".$key."$"] = $companyModel->get($key);

        }
        foreach ($varContent as $var) {
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
            $record->set('content', $quotingTool->mergeCustomTokens($record->get('content'), $keys_values));
            $full_content = base64_encode($record->get('content'));
            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $module, $entityId, null, null, $full_content, $record->get('description'));
        }

        // Merge custom var for header
        foreach ($varHeader as $var) {
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
            $record->set('header', $quotingTool->mergeCustomTokens($record->get('header'), $keys_values));
        }

        // Merge custom var for footer
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
            $record->set('footer', $quotingTool->mergeCustomTokens($record->get('footer'), $keys_values));
        }

        // Create PDF
        // Get data info settings
        include_once 'include/simplehtmldom/simple_html_dom.php';
        $content = $record->get('content');

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }
        foreach ($html->find('table') as $table) {
            $table->removeAttribute('data-info');
        }
        $content = $html->save();
        // Clean un-necessary attributes
        $pdf = $quotingTool->createPdf($content, $record->get('header'), $record->get('footer'), $fileName);

        $fileContent = '';

        if (is_readable($pdf)) {
            $fileContent = file_get_contents($pdf);
        }

        header("Content-type: application/pdf");
        header("Pragma: public");
        header("Cache-Control: private");
        header("Content-Disposition: attachment; filename=".html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset')));
        header("Content-Description: PHP Generated Data");

        echo $fileContent;
    }

    /**
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
//    public function send_email(Vtiger_Request $request)
//    {
//        global $current_user, $site_URL;
//
//        $moduleName = $request->getModule();
//        $response = new Vtiger_Response();
//        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
//
//        $data = array();
//        $toEmail = null;
//        $id = null;
//        $recordId = $request->get('record');
//        $module = $request->get('module');
//        $relModule = $request->get('relmodule');
//        $templateId = $request->get('template_id');
//        $signature = null;
//        $signatureName = null;
//        $description = $request->get('description');
//        $selectedEmail = $request->get('selectedEmail');
//        $quotingToolRecordModel = new QuotingTool_Record_Model();
//        $templateRecord = $quotingToolRecordModel->getById($templateId);
//
//        if (!$templateRecord) {
//            $response->setError(200, vtranslate('LBL_NOT_FOUND', $module));
//            $response->emit();
//            exit;
//        }
//
//        // Invalid email template
//        if (!$templateRecord->get('email_subject') || !$templateRecord->get('email_content')) {
//            $response->setError(200, vtranslate('LBL_INVALID_EMAIL_TEMPLATE', $module));
//            $response->emit();
//            exit;
//        }
//
//        list($no, $email_record, $toEmail) = explode("||", $selectedEmail);
//        $emailRecordModel = Vtiger_Record_Model::getInstanceById($email_record);
//        $signatureName = $emailRecordModel->getDisplayName();
//
//        if (!$toEmail) {
//            $response->setError(200, vtranslate('LBL_INVALID_EMAIL', $module));
//            $response->emit();
//            exit;
//        }
//
//        $quotingTool = new QuotingTool();
//        // Content
//        $content = $templateRecord->get('content');
//        $content = $content ? base64_decode($content) : '';
//        $varContent = $quotingTool->getVarFromString($content);
//        if (!empty($varContent)) {
//            $content = $quotingTool->parseTokens($content, $relModule, $recordId);
//        }
//
//        // Encode before put to database
//        $full_content = base64_encode($content);
//
//        /** @var QuotingTool_Record_Model $model */
//        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
//        $saveId = $transactionRecordModel->saveTransaction($id, $templateId, $relModule, $recordId, $signature, $signatureName, $full_content, $description);
//        $transactionRecord = $transactionRecordModel->findById($saveId);
//        $hash = $transactionRecord->get('hash');
//        $hash = $hash ? $hash : '';
//
//        $site = rtrim($site_URL, '/');
//        $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$saveId}&session={$hash}";
//        $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);
//        $keys_values = array();
//
//        foreach ($varContent as $var) {
//            if ($var == '$custom_proposal_link$') {
//                $keys_values['$custom_proposal_link$'] = $compactLink;
//            } else if ($var == '$custom_user_signature$') {
//                $keys_values['$custom_user_signature$'] = $current_user->signature;
//            }
//        }
//        if (!empty($keys_values)) {
//            $content = $quotingTool->mergeCustomTokens($content, $keys_values);
//            $full_content = base64_encode($content);
//            $saveId = $transactionRecordModel->saveTransaction($saveId, $templateId, $relModule, $recordId, $signature, $signatureName, $full_content, $description);
//        }
//
//        // Email subject
//        $emailSubject = base64_decode($templateRecord->get('email_subject'));
//        $varEmailSubject = $quotingTool->getVarFromString($emailSubject);
//        if (!empty($varEmailSubject)) {
//            $emailSubject = $quotingTool->parseTokens($emailSubject, $relModule, $recordId);
//            $keys_values = array();
//
//            foreach ($varEmailSubject as $var) {
//                if ($var == '$custom_proposal_link$') {
//                    $keys_values['$custom_proposal_link$'] = $compactLink;
//                } else if ($var == '$custom_user_signature$') {
//                    $keys_values['$custom_user_signature$'] = $current_user->signature;
//                }
//            }
//            if (!empty($keys_values)) {
//                $emailSubject = $quotingTool->mergeCustomTokens($emailSubject, $keys_values);
//            }
//        }
//
//        // Email content
//        $emailContent = base64_decode($templateRecord->get('email_content'));
//        $varEmailContent = $quotingTool->getVarFromString($emailContent);
//        if (!empty($varEmailContent)) {
//            $emailContent = $quotingTool->parseTokens($emailContent, $relModule, $recordId);
//            $keys_values = array();
//
//            foreach ($varEmailContent as $var) {
//                if ($var == '$custom_proposal_link$') {
//                    $keys_values['$custom_proposal_link$'] = $compactLink;
//                } else if ($var == '$custom_user_signature$') {
//                    $keys_values['$custom_user_signature$'] = $current_user->signature;
//                }
//            }
//            if (!empty($keys_values)) {
//                $emailContent = $quotingTool->mergeCustomTokens($emailContent, $keys_values);
//            }
//        }
//
//        $fromName = $current_user->first_name . ' ' . $current_user->last_name;
//        $fromEmail = null;
//
//        if ($current_user->email1) {
//            $fromEmail = $current_user->email1;
//        } else if ($current_user->email2) {
//            $fromEmail = $current_user->email2;
//        } else if ($current_user->secondaryemail) {
//            $fromEmail = $current_user->secondaryemail;
//        }
//
//        if ($fromEmail) {
//            $fromName = "{$fromName} ({$fromEmail})";
//        }
//
//        $result = send_mail($module, $toEmail, $fromName, $fromEmail, $emailSubject, $emailContent);
//
//        if ($result != 1) {
//            $errorMessage = vtranslate('ERROR_UNABLE_TO_SEND_EMAIL', $module);
//            $response->setError(200, $errorMessage);
//            $response->emit();
//            exit;
//        }
//
//        // Success
//        $data['message'] = vtranslate('LBL_EMAIL_SENT', $module);
//        $response->setResult($data);
//        $response->emit();
//    }

    /**
     * Fn - downloadPreview
     * Save PDF content to the file
     *
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function preview_and_send_email(Vtiger_Request $request)
    {
        global $current_user, $site_URL, $application_unique_key, $vtiger_current_version;

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        $quotingTool = new QuotingTool();
        $moduleName = $request->getModule();
        $selectedEmail = $request->get('selectedEmail');

        // CC email process
        $strCcEmails = $request->get('ccValues');
        if ($strCcEmails === null) {
            $strCcEmails = '';
        }
        $arrCcEmails = explode(',', trim($strCcEmails));
        $ccEmails = array();
        foreach ($arrCcEmails as $cc) {
            $ccEmails[] = $quotingTool->getEmailFromString($cc);
        }

        // BCC email process
        $strBccEmails = $request->get('bccValues');

        if ($strBccEmails === null) {
            $strBccEmails = '';
        }
        $arrBccEmails = explode(',', trim($strBccEmails));
        $bccEmails = array();
        foreach ($arrBccEmails as $bcc) {
            $bccEmails[] = $quotingTool->getEmailFromString($bcc);
        }

        // Invalid email template
        if (!$request->get('email_subject') || !$request->get('email_content')) {
            $response->setError(200, vtranslate('LBL_INVALID_EMAIL_TEMPLATE', $moduleName));
            $response->emit();
            exit;
        }

        $emails = array();
        $toEmail = null;

        if (is_array($selectedEmail)) {
            foreach ($selectedEmail as $e) {
                list($no, $email_record, $toEmail) = explode("||", $e);
                $emails[] = $toEmail;
            }
        } else {
            list($no, $email_record, $toEmail) = explode("||", $selectedEmail);
            $emails[] = $toEmail;
        }

        if (empty($emails)) {
            $response->setError(200, vtranslate('LBL_INVALID_EMAIL', $moduleName));
            $response->emit();
            exit;
        }

        $emailSubject = base64_decode($request->get('email_subject'));
        $emailContent = base64_decode($request->get('email_content'));
        $fromName = $current_user->first_name . ' ' . $current_user->last_name;
        $fromEmail = null;

        if ($current_user->email1) {
            $fromEmail = $current_user->email1;
        } else if ($current_user->email2) {
            $fromEmail = $current_user->email2;
        } else if ($current_user->secondaryemail) {
            $fromEmail = $current_user->secondaryemail;
        }

        if ($fromEmail) {
            $fromName = "{$fromName} ({$fromEmail})";
        }

        $counter = 0;
        $emails = array_unique($emails);

        // Attach document
        $check_attach_file = $request->get('check_attach_file') == 'on';
        $multipleRecord = $request->get('multi_record');
        //$multipleRecord = json_decode($multipleRecord);

        if($multipleRecord!=null){

            foreach ($multipleRecord as $recordId){
                foreach ($emails as $email) {
                    //Storing the details of emails
                    $entityId = $recordId;
                    $cc = implode(',', $ccEmails);
                    $bcc = implode(',', $bccEmails);
                    $emailModuleName = 'Emails';
                    $userId = $current_user->id;
                    /** @var Emails $emailFocus */
                    $emailFocus = CRMEntity::getInstance($emailModuleName);
                    $emailFieldValues = array(
                        'assigned_user_id' => $userId,
                        'subject' => $emailSubject,
                        'description' => $emailContent,
                        'from_email' => $fromEmail,
                        'saved_toid' => $email,
                        'ccmail' => $cc,
                        'bccmail' => $bcc,
                        'parent_id' => $entityId . "@$userId|",
                        'email_flag' => 'SENT',
                        'activitytype' => $emailModuleName,
                        'date_start' => date('Y-m-d'),
                        'time_start' => date('H:i:s'),
                        'mode' => '',
                        'id' => ''
                    );
                    if(version_compare($vtiger_current_version, '7.0.0', '<')) {
                        $emailFocus->column_fields = $emailFieldValues;
                        $emailFocus->save($emailModuleName);
                        $emailId = $emailFocus->id;

                    }else{
                        if(!empty($recordId)) {
                            $emailFocus1 = Vtiger_Record_Model::getInstanceById($recordId,$emailModuleName);
                            $emailFocus1->set('mode', 'edit');
                        }else{
                            $emailFocus1 = Vtiger_Record_Model::getCleanInstance($emailModuleName);
                            $emailFocus1->set('mode', '');
                        }
                        $emailFocus1->set('assigned_user_id', $userId);
                        $emailFocus1->set('subject', $emailSubject);
                        $emailFocus1->set('description', $emailContent);
                        $emailFocus1->set('from_email', $fromEmail);
                        $emailFocus1->set('saved_toid', $email);
                        $emailFocus1->set('ccmail', $cc);
                        $emailFocus1->set('bccmail', $bcc);
                        $emailFocus1->set('parent_id', $entityId . "@$userId|");
                        $emailFocus1->set('email_flag', 'SENT');
                        $emailFocus1->set('activitytype', $emailModuleName);
                        $emailFocus1->set('date_start', date('Y-m-d'));
                        $emailFocus1->set('time_start', date('H:i:s'));
                        $emailFocus1->set('mode', '');
                        $emailFocus1->set('id', '');
                        $emailFocus1->save();
                        $emailId = $emailFocus1->getId();
                        $emailFocus->id = $emailId;
                    }
                    $emailFocus->column_fields = $emailFieldValues;

                    //Including email tracking details

                    if ($emailId) {
                        $trackURL = "$site_URL/modules/Emails/TrackAccess.php?record=$entityId&mailid=$emailId&app_key=$application_unique_key";
                        $emailContent = "<img src='$trackURL' alt='' width='1' height='1'>$emailContent";

                        $logo = 0;
                        if (stripos($emailContent, '<img src="cid:logo" />')) {
                            $logo = 1;
                        }

                        $transactionId = $request->get('transaction_id');
                        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
                        /** @var Vtiger_Record_Model $transactionRecord */
                        $transactionRecord = $transactionRecordModel->findById($transactionId);
                        $hash = $transactionRecord->get('hash');
                        $hash = $hash ? $hash : '';

                        $templateId = $transactionRecord->get('template_id');
                        $recordModel = new QuotingTool_Record_Model();
                        /** @var QuotingTool_Record_Model $record */
                        $record = $recordModel->getById($templateId);
                        // get $token before decompileRecord because when after decompileRecord , get $token incorrect (\$)
                        $varContent = $quotingTool->getVarFromString(base64_decode($record->get('content')));
                        $record = $record->decompileRecord($entityId, array('header', 'content', 'footer'));

                        if ($check_attach_file) {
                            // Create PDF file
                            $fileName = $quotingTool->makeUniqueFile($record->get('filename'));
                            $attachmentId = $quotingTool->createAttachFile($emailFocus, $fileName);
                            $fileName = $attachmentId . '_' . $fileName;

                            // Merge special tokens
                            $keys_values = array();
                            $site = rtrim($site_URL, '/');
                            $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$transactionId}&session={$hash}";
                            $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);

                            //company token
                            $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
                            $companyfields = array();
                            foreach ($companyModel->getFields() as $key => $val) {
                                if ($key == 'logo') {
                                    continue;
                                }
                                $companyfields["$"."Vtiger_Company_".$key."$"] = $companyModel->get($key);
                            }
                            foreach ($varContent as $var) {
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
                                $record->set('content', $quotingTool->mergeCustomTokens($record->get('content'), $keys_values));
                            }

                            $pdf = $quotingTool->createPdf($record->get('content'), $record->get('header'), $record->get('footer'), $fileName);
                        }

                        $result = send_mail($moduleName, $email, $fromName, $fromEmail, $emailSubject, $emailContent, $cc, $bcc, 'all', $emailId, $logo);
                        $emailFocus->setEmailAccessCountValue($emailId);

                        if (!$result) {
                            //If mail is not sent then removing the details about email
                            $emailFocus->trash($emailModuleName, $emailId);
                        } else {
                            $counter += $result;
                        }
                    }
                }
            }
        }
        else{
            $recordId = $request->get('record');
            foreach ($emails as $email) {
                //Storing the details of emails
                $entityId = $request->get('record');
                $cc = implode(',', $ccEmails);
                $bcc = implode(',', $bccEmails);
                $emailModuleName = 'Emails';
                $userId = $current_user->id;
                /** @var Emails $emailFocus */
                $emailFocus = CRMEntity::getInstance($emailModuleName);
                $emailFieldValues = array(
                    'assigned_user_id' => $userId,
                    'subject' => $emailSubject,
                    'description' => $emailContent,
                    'from_email' => $fromEmail,
                    'saved_toid' => $email,
                    'ccmail' => $cc,
                    'bccmail' => $bcc,
                    'parent_id' => $entityId . "@$userId|",
                    'email_flag' => 'SENT',
                    'activitytype' => $emailModuleName,
                    'date_start' => date('Y-m-d'),
                    'time_start' => date('H:i:s'),
                    'mode' => '',
                    'id' => ''
                );
                if(version_compare($vtiger_current_version, '7.0.0', '<')) {
                    $emailFocus->column_fields = $emailFieldValues;
                    $emailFocus->save($emailModuleName);
                    $emailId = $emailFocus->id;

                }else{
                    if(!empty($recordId)) {
                        $emailFocus1 = Vtiger_Record_Model::getInstanceById($recordId,$emailModuleName);
                        $emailFocus1->set('mode', 'edit');
                    }else{
                        $emailFocus1 = Vtiger_Record_Model::getCleanInstance($emailModuleName);
                        $emailFocus1->set('mode', '');
                    }
                    $emailFocus1->set('assigned_user_id', $userId);
                    $emailFocus1->set('subject', $emailSubject);
                    $emailFocus1->set('description', $emailContent);
                    $emailFocus1->set('from_email', $fromEmail);
                    $emailFocus1->set('saved_toid', $email);
                    $emailFocus1->set('ccmail', $cc);
                    $emailFocus1->set('bccmail', $bcc);
                    $emailFocus1->set('parent_id', $entityId . "@$userId|");
                    $emailFocus1->set('email_flag', 'SENT');
                    $emailFocus1->set('activitytype', $emailModuleName);
                    $emailFocus1->set('date_start', date('Y-m-d'));
                    $emailFocus1->set('time_start', date('H:i:s'));
                    $emailFocus1->set('mode', '');
                    $emailFocus1->set('id', '');
                    $emailFocus1->save();
                    $emailId = $emailFocus1->getId();
                    $emailFocus->id = $emailId;
                }
                $emailFocus->column_fields = $emailFieldValues;

                //Including email tracking details

                if ($emailId) {
                    $trackURL = "$site_URL/modules/Emails/TrackAccess.php?record=$entityId&mailid=$emailId&app_key=$application_unique_key";
                    $emailContent = "<img src='$trackURL' alt='' width='1' height='1'>$emailContent";

                    $logo = 0;
                    if (stripos($emailContent, '<img src="cid:logo" />')) {
                        $logo = 1;
                    }

                    $transactionId = $request->get('transaction_id');
                    $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
                    /** @var Vtiger_Record_Model $transactionRecord */
                    $transactionRecord = $transactionRecordModel->findById($transactionId);
                    $hash = $transactionRecord->get('hash');
                    $hash = $hash ? $hash : '';

                    $templateId = $transactionRecord->get('template_id');
                    $recordModel = new QuotingTool_Record_Model();
                    /** @var QuotingTool_Record_Model $record */
                    $record = $recordModel->getById($templateId);
                    // get $token before decompileRecord because when after decompileRecord , get $token incorrect (\$)
                    $varContent = $quotingTool->getVarFromString(base64_decode($record->get('content')));
                    $record = $record->decompileRecord($entityId, array('header', 'content', 'footer'));

                    if ($check_attach_file) {
                        // Create PDF file
                        $fileName = $quotingTool->makeUniqueFile($record->get('filename'));
                        $attachmentId = $quotingTool->createAttachFile($emailFocus, $fileName);
                        $fileName = $attachmentId . '_' . $fileName;

                        // Merge special tokens
                        $keys_values = array();
                        $site = rtrim($site_URL, '/');
                        $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$transactionId}&session={$hash}";
                        $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);

                        //company token
                        $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
                        $companyfields = array();
                        foreach ($companyModel->getFields() as $key => $val) {
                            if ($key == 'logo') {
                                continue;
                            }
                            $companyfields["$"."Vtiger_Company_".$key."$"] = $companyModel->get($key);
                        }
                        foreach ($varContent as $var) {
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
                            $record->set('content', $quotingTool->mergeCustomTokens($record->get('content'), $keys_values));
                        }

                        $pdf = $quotingTool->createPdf($record->get('content'), $record->get('header'), $record->get('footer'), $fileName);
                    }

                    $result = send_mail($moduleName, $email, $fromName, $fromEmail, $emailSubject, $emailContent, $cc, $bcc, 'all', $emailId, $logo);
                    $emailFocus->setEmailAccessCountValue($emailId);

                    if (!$result) {
                        //If mail is not sent then removing the details about email
                        $emailFocus->trash($emailModuleName, $emailId);
                    } else {
                        $counter += $result;
                    }
                }
            }
        }

        if (!$counter) {
            $errorMessage = vtranslate('ERROR_UNABLE_TO_SEND_EMAIL', $moduleName);
            $response->setError(200, $errorMessage);
            $response->emit();
            exit;
        }

        // Success
        $data['message'] = vtranslate('LBL_EMAIL_SENT', $moduleName);
        $data['total'] = $counter;
        $response->setResult($data);
        $response->emit();
    }

    /**
     * Fn - downloadPreview
     * Save PDF content to the file
     *
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function download(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($recordId);

        if (!$record) {
            echo vtranslate('LBL_NOT_FOUND', $moduleName);
            exit;
        }

        $quotingTool = new QuotingTool();
        $record = $record->decompileRecord(0, array('header', 'content', 'footer'));
        // File name
        $fileName = $quotingTool->makeUniqueFile($record->get('filename'));
        // Create PDF
        $pdf = $quotingTool->createPdf($record->get('content'), $record->get('header'), $record->get('footer'), $fileName);

        // Download the file
        $fileContent = '';
        if (is_readable($pdf)) {
            $fileContent = file_get_contents($pdf);
        }
        header("Content-type: application/pdf");
        header("Pragma: public");
        header("Cache-Control: private");
        header("Content-Disposition: attachment; filename=".html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset')));
        header("Content-Description: PHP Generated Data");

        echo $fileContent;
    }

    /**
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
//    public function download_with_signature(Vtiger_Request $request)
//    {
//        $moduleName = $request->getModule();
//        $record = $request->get('record');
//        $templateId = $request->get('template_id');
//        $quotingToolRecordModel = new QuotingTool_Record_Model();
//        $templateRecord = $quotingToolRecordModel->getById($templateId);
//
//        if (!$templateRecord) {
//            echo vtranslate('LBL_NOT_FOUND', $moduleName);
//            exit;
//        }
//
//        $quotingTool = new QuotingTool();
//        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
//        $transactionRecord = $transactionRecordModel->getLastTransactionByModule($request->get('relmodule'), $record);
//        $module = $templateRecord->get('module');
//        // Name
//        $filename = $templateRecord->get('filename');
//        // Header
//        $header = $templateRecord->get('header');
//        $header = $header ? base64_decode($header) : '';
//        // Content
//        $content = ($transactionRecord && $transactionRecord->get('full_content')) ?
//            $transactionRecord->get('full_content') : $templateRecord->get('content');
//        $content = $content ? base64_decode($content) : '';
//        // Parse tokens
//        $tokens = $quotingTool->getFieldTokenFromString($content);
//        // Parse content
//        $content = $quotingTool->mergeBlockTokens($tokens, $record, $content);
//        $content = $quotingTool->mergeTokens($tokens, $record, $content, $module);
//        $content = $quotingTool->mergeCustomFunctions($content);
//        // Footer
//        $footer = $templateRecord->get('footer');
//        $footer = $footer ? base64_decode($footer) : '';
//        // File name
//        $fileName = $quotingTool->makeUniqueFile($filename);
//        // Create PDF
//        $pdf = $quotingTool->createPdf($content, $header, $footer, $fileName);
//
//        // Download the file
//        header('Content-Type: application/octet-stream');
//        header('Content-disposition: attachment; filename="' . $fileName . '"');
//        header('Content-Length: ' . filesize($pdf));
//        print readfile($pdf);
//        exit;
//    }

    /**
     * @param Vtiger_Request $request
     */
    public function duplicate(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $params = array();

        /** @var QuotingTool_Record_Model $recordModel */
        $recordModel = QuotingTool_Record_Model::getCleanInstance($module);
        $recordId = $request->get('record');
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($recordId);

        if (!$record) {
            return;
        }

        $data = $record->getData();
        $allow = array('filename', 'module', 'body', 'header', 'content', 'footer', 'description', 'deleted', 'email_subject',
            'email_content', 'mapping_fields', 'attachments');

        if ($data && !empty($data)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $allow, true)) {
                    continue;
                }

                if ($key == 'filename') {
                    // Add suffix to file name
                    $value = $value . '_' . vtranslate('LBL_COPY', $module);
                } else if (($key == 'mapping_fields' || $key == 'attachments') && $value) {
                    // decode html entity when encode json string
                    $value = html_entity_decode($value);
                }

                $params[$key] = $value;
            }
        }

        // Save data
        $template = $recordModel->save(null, $params);
        $id = $template->getId();

        if (!$id) {
            // When error
            return;
        }

        // Save history
        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $historyParams = array(
            'body' => $template->get('body')
        );
        $historyRecordModel->saveByTemplate($id, $historyParams);

        header("Location: index.php?module={$module}&view=List");
    }

}