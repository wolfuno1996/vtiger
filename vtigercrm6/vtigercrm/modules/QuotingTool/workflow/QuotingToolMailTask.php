<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTEmailRecipientsTemplate.inc');
require_once('modules/Emails/mail.php');
require_once('modules/QuotingTool/QuotingTool.php');

/**
 * Class QuotingToolMailTask
 */
class QuotingToolMailTask extends VTTask
{
    /**
     * Sending email takes more time, this should be handled via queue all the time.
     * @var bool
     */
    public $executeImmediately = false;

    /**
     * @return array
     */
    public function getFieldNames()
    {
        return array('subject', 'content', 'recepient', 'emailcc', 'emailbcc', 'fromEmail', 'template', 'check_attach_file',
            'template_language');
    }

    /**
     * @param VTWorkflowEntity $entity
     */
    public function doTask($entity)
    {
        global $current_user,$vtiger_current_version;

        $util = new VTWorkflowUtils();
        $admin = $util->adminUser();
        $module = $entity->getModuleName();

        $taskContents = Zend_Json::decode($this->getContents($entity));
        $from_email = $taskContents['fromEmail'];
        $from_name = $taskContents['fromName'];
        $to_email = $taskContents['toEmail'];
        $cc = $taskContents['ccEmail'];
        $bcc = $taskContents['bccEmail'];
        $emailSubject = $taskContents['subject'];
        $emailContent = $taskContents['content'];

        if (!empty($to_email)) {
            //Storing the details of emails
            $entityIdDetails = vtws_getIdComponents($entity->getId());
            $entityId = $entityIdDetails[1];
            $moduleName = 'Emails';
            $userId = $current_user->id;
            /** @var Emails $emailFocus */
            $emailFocus = CRMEntity::getInstance($moduleName);
            $emailFieldValues = array(
                'assigned_user_id' => $userId,
                'subject' => $emailSubject,
                'description' => $emailContent,
                'from_email' => $from_email,
                'saved_toid' => $to_email,
                'ccmail' => $cc,
                'bccmail' => $bcc,
                'parent_id' => $entityId . "@$userId|",
                'email_flag' => 'SENT',
                'activitytype' => $moduleName,
                'date_start' => date('Y-m-d'),
                'time_start' => date('H:i:s'),
                'mode' => '',
                'id' => ''
            );
            if(version_compare($vtiger_current_version, '7.0.0', '<')) {
                $emailFocus->column_fields = $emailFieldValues;
                $emailFocus->save($moduleName);
                $emailId = $emailFocus->id;
            }else{
                if(!empty($recordId)) {
                    $emailFocus1 = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
                    $emailFocus1->set('mode', 'edit');
                }else{
                    $emailFocus1 = Vtiger_Record_Model::getCleanInstance($moduleName);
                    $emailFocus1->set('mode', '');
                }
                $emailFocus1->set('assigned_user_id', $userId);
                $emailFocus1->set('subject', $emailSubject);
                $emailFocus1->set('description', $emailContent);
                $emailFocus1->set('from_email', $from_email);
                $emailFocus1->set('saved_toid', $to_email);
                $emailFocus1->set('ccmail', $cc);
                $emailFocus1->set('bccmail', $bcc);
                $emailFocus1->set('parent_id', $entityId . "@$userId|");
                $emailFocus1->set('email_flag', 'SENT');
                $emailFocus1->set('activitytype', $moduleName);
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
            global $site_URL, $application_unique_key;
            $emailId = $emailFocus->id;
            $trackURL = "$site_URL/modules/Emails/actions/TrackAccess.php?record=$entityId&mailid=$emailId&app_key=$application_unique_key";
            $emailContent = "<img src='$trackURL' alt='' width='1' height='1'>$emailContent";

            $logo = 0;
            if (stripos($emailContent, '<img src="cid:logo" />')) {
                $logo = 1;
            }

            $templates = null;
            if (is_array($this->template)) {
                $templates = $this->template;
            } else {
                $templates = array($this->template);
            }

            $status = 0;

            if (count($templates) > 0) {
                foreach ($templates as $templateId) {
                    if ($templateId != "0" && $templateId != "") {
                        $id = null;
                        $signature = null;
                        $signatureName = null;
                        $quotingToolModel = new QuotingTool_Record_Model();
                        /** @var QuotingTool_Record_Model $quotingToolRecord */
                        $quotingToolRecord = $quotingToolModel->getById($templateId);

                        $quotingTool = new QuotingTool();
                        // Description
                        $description = $quotingToolRecord->get('description');
                        // Content
                        $temFilename = $quotingToolRecord->get('filename');
                        $pdfContent = $quotingToolRecord->get('content');
                        $tempHeader = $quotingToolRecord->get('header');
                        $tempFooter = $quotingToolRecord->get('footer');

                        $pdfContent = $pdfContent ? base64_decode($pdfContent) : '';
                        // Parse tokens
                        $varContent = $quotingTool->getVarFromString($pdfContent);
                        if (!empty($varContent)) {
                            $pdfContent = $quotingTool->parseTokens($pdfContent, $module, $entityId);
                        }
                        // Encode before put to database
                        $full_content = base64_encode($pdfContent);

                        $pdfHeader = $tempHeader ? base64_decode($tempHeader) : '';
                        $pdfFooter = $tempFooter ? base64_decode($tempFooter) : '';

                        /** @var QuotingTool_Record_Model $model */
                        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
                        $transactionId = $transactionRecordModel->saveTransaction($id, $templateId, $module, $entityId, $signature, $signatureName, $full_content, $description);
                        $transactionRecord = $transactionRecordModel->findById($transactionId);
                        $hash = $transactionRecord->get('hash');
                        $hash = $hash ? $hash : '';
                        // Merge special tokens
                        $keys_values = array();
                        $site = rtrim($site_URL, '/');
                        $link = "{$site}/modules/QuotingTool/proposal/index.php?record={$transactionId}&session={$hash}";
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
                            $content = $quotingTool->mergeCustomTokens($pdfContent, $keys_values);
                            $full_content = base64_encode($content);
                            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $module, $entityId, $signature, $signatureName, $full_content, $description);
                        }

                        if ($this->check_attach_file != null) {
                            // Create PDF file
                            // File name
                            $pdfName = $quotingTool->makeUniqueFile($temFilename);
                            // Create PDF
                            $attachmentId = $quotingTool->createAttachFile($emailFocus, $pdfName);
                            // To matching with: modules/Emails/mail.php:362
                            $pdfName = $attachmentId . '_' . $pdfName;
                            $pdf = $quotingTool->createPdf($pdfContent, $pdfHeader, $pdfFooter, $pdfName);
                        }

                        // Parse email subject
                        $varEmailSubject = $quotingTool->getVarFromString($emailSubject);
                        if (!empty($varEmailSubject)) {
                            $emailSubject = $quotingTool->parseTokens($emailSubject, $module, $entityId);
                            $keys_values = array();
                            //company token
                            $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
                            $companyfields = array();
                            foreach ($companyModel->getFields() as $key => $val) {
                                if ($key == 'logo') {
                                    continue;
                                }
                                $companyfields["$"."Vtiger_Company_".$key."$"] = $companyModel->get($key);
                            }

                            foreach ($varEmailSubject as $var) {
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
                                $emailSubject = $quotingTool->mergeCustomTokens($emailSubject, $keys_values);
                            }
                        }

                        // Parse email content
                        $varEmailContent = $quotingTool->getVarFromString($emailContent);
                        if (!empty($varEmailContent)) {
                            $emailContent = $quotingTool->parseTokens($emailContent, $module, $entityId);
                            $keys_values = array();
                            //company token
                            $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
                            $companyfields = array();
                            foreach ($companyModel->getFields() as $key => $val) {
                                if ($key == 'logo') {
                                    continue;
                                }
                                $companyfields["$"."Vtiger_Company_".$key."$"] = $companyModel->get($key);
                            }
                            foreach ($varEmailContent as $var) {
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
                                $emailContent = $quotingTool->mergeCustomTokens($emailContent, $keys_values);
                            }
                        }

                        $status = send_mail($module, $to_email, $from_name, $from_email, $emailSubject, $emailContent, $cc, $bcc, 'all', $emailId, $logo);
                    }
                }
            } else {
                $status = send_mail($module, $to_email, $from_name, $from_email, $emailSubject, $emailContent, $cc, $bcc, '', '', $logo);
            }

            if (!empty($emailId)) {
                $emailFocus->setEmailAccessCountValue($emailId);
            }

            if (!$status) {
                //If mail is not sent then removing the details about email
                $emailFocus->trash($moduleName, $emailId);
            }
        }
        $util->revertUser();
    }

    /**
     * Function to get contents of this task
     * @param <Object> $entity
     * @return <Array> contents
     */
    public function getContents($entity, $entityCache = false)
    {

        if (!$this->contents) {
            global $adb, $current_user;
            $taskContents = array();
            $entityId = $entity->getId();

            $utils = new VTWorkflowUtils();
            $adminUser = $utils->adminUser();
            if (!$entityCache) {
                $entityCache = new VTEntityCache($adminUser);
            }

            $fromUserId = Users::getActiveAdminId();
            $entityOwnerId = $entity->get('assigned_user_id');
            if ($entityOwnerId) {
                list ($moduleId, $fromUserId) = explode('x', $entityOwnerId);
            }

            $ownerEntity = $entityCache->forId($entityOwnerId);
            if ($ownerEntity->getModuleName() === 'Groups') {
                list($moduleId, $recordId) = vtws_getIdComponents($entityId);
                $fromUserId = Vtiger_Util_Helper::getCreator($recordId);
            }

            if ($this->fromEmail && !($ownerEntity->getModuleName() === 'Groups' && strpos($this->fromEmail, 'assigned_user_id : (Users) ') !== false)) {
                $et = new VTEmailRecipientsTemplate($this->fromEmail);
                $fromEmailDetails = $et->render($entityCache, $entityId);

                $con1 = strpos($fromEmailDetails, '&lt;');
                $con2 = strpos($fromEmailDetails, '&gt;');

                if ($con1 && $con2) {
                    list($fromName, $fromEmail) = explode('&lt;', $fromEmailDetails);
                    list($fromEmail, $rest) = explode('&gt;', $fromEmail);
                } else {
                    $fromName = "";
                    $fromEmail = $fromEmailDetails;
                }

            } else {
                $userObj = CRMEntity::getInstance('Users');
                $userObj->retrieveCurrentUserInfoFromFile($fromUserId);
                if ($userObj) {
                    $fromEmail = $userObj->email1;
                    $fromName = $userObj->user_name;
                } else {
                    $result = $adb->pquery('SELECT user_name, email1 FROM vtiger_users WHERE id = ?', array($fromUserId));
                    $fromEmail = $adb->query_result($result, 0, 'email1');
                    $fromName = $adb->query_result($result, 0, 'user_name');
                }
            }

            if (!$fromEmail) {
                $utils->revertUser();
                return false;
            }

            $taskContents['fromEmail'] = $fromEmail;
            $taskContents['fromName'] = $fromName;

            if ($entity->getModuleName() === 'Events') {
                $contactId = $entity->get('contact_id');
                if ($contactId) {
                    $contactIds = '';
                    list($wsId, $recordId) = explode('x', $entityId);
                    $webserviceObject = VtigerWebserviceObject::fromName($adb, 'Contacts');

                    $result = $adb->pquery('SELECT contactid FROM vtiger_cntactivityrel WHERE activityid = ?', array($recordId));
                    $numOfRows = $adb->num_rows($result);
                    for ($i = 0; $i < $numOfRows; $i++) {
                        $contactIds .= vtws_getId($webserviceObject->getEntityId(), $adb->query_result($result, $i, 'contactid')) . ',';
                    }
                }
                $entity->set('contact_id', trim($contactIds, ','));
                $entityCache->cache[$entityId] = $entity;
            }

            $et = new VTEmailRecipientsTemplate($this->recepient);
            $toEmail = $et->render($entityCache, $entityId);

            $ecct = new VTEmailRecipientsTemplate($this->emailcc);
            $ccEmail = $ecct->render($entityCache, $entityId);

            $ebcct = new VTEmailRecipientsTemplate($this->emailbcc);
            $bccEmail = $ebcct->render($entityCache, $entityId);

            if (strlen(trim($toEmail, " \t\n,")) == 0 && strlen(trim($ccEmail, " \t\n,")) == 0 && strlen(trim($bccEmail, " \t\n,")) == 0) {
                $utils->revertUser();
                return false;
            }

            $taskContents['toEmail'] = $toEmail;
            $taskContents['ccEmail'] = $ccEmail;
            $taskContents['bccEmail'] = $bccEmail;

            $st = new VTSimpleTemplate($this->subject);
            $taskContents['subject'] = $st->render($entityCache, $entityId);

            $ct = new VTSimpleTemplate($this->content);
            $taskContents['content'] = $ct->render($entityCache, $entityId);
            $this->contents = $taskContents;
            $utils->revertUser();
        }
        if (is_array($this->contents)) {
            $this->contents = Zend_Json::encode($this->contents);
        }
        return $this->contents;
    }

    /**
     * @param $selected_module
     * @return array
     */
    public function getTemplates($selected_module)
    {
        $options = array();
        $quotingToolRecordModel = new QuotingTool_Record_Model();
        $templates = $quotingToolRecordModel->findByModule($selected_module);

        if ($templates && count($templates) > 0) {
            /** @var Vtiger_Module_Model $t */
            foreach ($templates as $t) {
                $options[$t->get('id')] = $t->get('filename');
            }
        }

        return $options;
    }

}