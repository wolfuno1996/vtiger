<?php
/* ********************************************************************************
* The content of this file is subject to the Quoting Tool ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */

include_once 'modules/QuotingTool/QuotingTool.php';

/**
 * Class QuotingTool_EmailPreviewTemplate_View
 */
class QuotingTool_EmailPreviewTemplate_View extends Vtiger_IndexAjax_View
{

    /**
     * @param Vtiger_Request $request
     */
    function process(Vtiger_Request $request)
    {
        global $site_URL, $current_user;

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);

        $recordId = $request->get('record');
        $templateId = $request->get('template_id');
        $isCreateNewRecord  = $request->get('isCreateNewRecord');
        $childModule  = $request->get('childModule');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($templateId);
        $relModule = $record->get('module');
        $quotingTool = new QuotingTool();
        // get $token before decompileRecord because when after decompileRecord , get $token incorrect (\$)
        $varContent = $quotingTool->getVarFromString(base64_decode($record->get('content')));
        $record = $record->decompileRecord($recordId, array('content', 'header', 'footer', 'email_subject', 'email_content'));

        /** @var QuotingTool_Record_Model $model */
        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
        // Encode before put to database
        $full_content = base64_encode($record->get('content'));
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $record->get('module'), $recordId, null, null, $full_content, $record->get('description'));
        $transactionRecord = $transactionRecordModel->findById($transactionId);
        $hash = $transactionRecord->get('hash');
        $hash = $hash ? $hash : '';

        // Merge special tokens
        $keys_values = array();
        $site = rtrim($site_URL, '/');
        if ($isCreateNewRecord == 1) {
            $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$transactionId}&session={$hash}&iscreatenewrecord=true&childmodule={$childModule}";
        }else{
            $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$transactionId}&session={$hash}";
        }
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
            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $relModule, $recordId, null, null, $full_content, $record->get('description'));
        }

        // Email content
        $varEmailSubject = $quotingTool->getVarFromString($record->get('email_subject'));
        if (!empty($varEmailSubject)) {
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
                $record->set('email_subject', $quotingTool->mergeCustomTokens($record->get('email_subject'), $keys_values));
            }
        }

        // Email content
        $varEmailContent = $quotingTool->getVarFromString($record->get('email_content'));
        if (!empty($varEmailContent)) {
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
                $record->set('email_content', $quotingTool->mergeCustomTokens($record->get('email_content'), $keys_values));
            }
        }

        // Send Email list

            $multiRecord = $request->get('multiRecord');
            $email_field_list = $quotingTool->getEmailList($relModule, $recordId, $isCreateNewRecord,$multiRecord);






        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RECORDID', $recordId);
        $viewer->assign('TEMPLATEID', $templateId);
        $viewer->assign('EMAIL_FIELD_LIST', $email_field_list);
        $viewer->assign('EMAIL_SUBJECT', $record->get('email_subject'));
        $viewer->assign('EMAIL_CONTENT', $record->get('email_content'));
        $viewer->assign('CUSTOM_PROPOSAL_LINK', $link);
        $viewer->assign('TRANSACTION_ID', $transactionId);
        $viewer->assign('MULTI_RECORD', $multiRecord);


        echo $viewer->view('EmailPreviewTemplate.tpl', $moduleName, true);

    }

}