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
 * Class QuotingTool_ActionAjax_Action
 */
class QuotingTool_ActionAjax_Action extends Vtiger_Action_Controller
{
    /**
     * @constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('save');
        $this->exposeMethod('save_setting');
        $this->exposeMethod('delete');
        $this->exposeMethod('getTemplate');
        $this->exposeMethod('getHistories');
        $this->exposeMethod('getHistory');
        $this->exposeMethod('removeHistories');
        $this->exposeMethod('getAllRecord');
        $this->exposeMethod('exportTemplateQuotingTool');
        $this->exposeMethod('importTemplate');
        $this->exposeMethod('ImportDefaultTemplates');
        $this->exposeMethod('CreateNewProposal');
        $this->exposeMethod('save_proposal');
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
     * @param Vtiger_Request $request
     */
    public function save(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();
        $params = array();

        /** @var QuotingTool_Record_Model $recordModel */
        $recordModel = QuotingTool_Record_Model::getCleanInstance($module);
        $record = $request->get('record');

        if ($request->has('filename')) {
            $fileName = str_replace(array('\\','/',':','*','?','"','<','>','|'),' ',$request->get('filename'));
            $params['filename'] = $fileName;
        }

        if ($request->has('primary_module')) {
            $params['module'] = $request->get('primary_module');
        }

        if ($request->has('body')) {
            $params['body'] = $request->get('body');
        }

        if ($request->has('header')) {
            $params['header'] = $request->get('header');
        }

        if ($request->has('content')) {
            $params['content'] = $request->get('content');
        }

        if ($request->has('footer')) {
            $params['footer'] = $request->get('footer');
        }

        if ($request->has('description')) {
            $params['description'] = $request->get('description');
        }
        if ($request->has('expire_in_days')) {
            $params['expire_in_days'] = $request->get('expire_in_days');
        }
        if ($request->has('anwidget')) {
            if($request->get('anwidget')=='true'){
                $params['anwidget'] = 1;
            }else{
                $params['anwidget'] = 0;
            }
        }
        if ($request->has('createnewrecords')) {
            if($request->get('createnewrecords')=='true'){
                $params['createnewrecords'] = 1;
            }else{
                $params['createnewrecords'] = 0;
            }
        }
        if ($request->has('linkproposal')) {
            $params['linkproposal'] = $request->get('linkproposal');
        }

        if ($request->has('email_subject')) {
            $params['email_subject'] = $request->get('email_subject');
        }

        if ($request->has('email_content')) {
            $params['email_content'] = $request->get('email_content');
        }

        if ($request->has('mapping_fields')){
            $params['mapping_fields'] = ($request->get('mapping_fields')) ?
                QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('mapping_fields'), JSON_FORCE_OBJECT)) : null;
        }

        if ($request->has('attachments')){
            $params['attachments'] = ($request->get('attachments')) ?
                QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('attachments'))) : null;
        }
        if ($request->has('is_active')) {
            $params['is_active'] = $request->get('is_active');
        }

        // Save data
        $template = $recordModel->save($record, $params);
        $id = $template->getId();

        if (!$id) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data['id'] = $id;
        $data['message'] = vtranslate('LBL_SUCCESSFUL', $module);

        // Save history
        if ($request->get('history')) {
            $historyRecordModel = new QuotingTool_HistoryRecord_Model();
            $historyParams = array(
                'body' => $template->get('body')
            );
            $newHistory = $historyRecordModel->saveByTemplate($id, $historyParams);

            // Response data
            $calendarDatetimeUIType = new Calendar_Datetime_UIType();
            $data['history'] = array(
                'id' => $newHistory->getId(),
                'created' =>  $calendarDatetimeUIType->getDisplayValue($newHistory->get('created'))
            );
        }

        $response->setResult($data);
        return $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function save_setting(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();
        $params = array();

        /** @var QuotingTool_SettingRecord_Model $recordModel */
        $recordModel = new QuotingTool_SettingRecord_Model();
        $record = $request->get('record');  // templateID

        if ($request->has('description')) {
            $params['description'] = $request->get('description');
        }

        if ($request->has('label_decline')) {
            $params['label_decline'] = $request->get('label_decline');
        }

        if ($request->has('label_accept')) {
            $params['label_accept'] = $request->get('label_accept');
        }

        if ($request->has('background')){
            $params['background'] = ($request->get('background')) ?
                QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('background'), JSON_FORCE_OBJECT)) : null;
        }
        if ($request->has('expire_in_days')) {
            $params['expire_in_days'] = $request->get('expire_in_days');
        }


        // Save data
        $id = $recordModel->saveByTemplate($record, $params);

        if (!$id) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data['id'] = $id;
        $data['message'] = vtranslate('LBL_SUCCESSFUL', $module);

        $response->setResult($data);
        return $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function save_proposal(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);

        /** @var QuotingTool_SettingRecord_Model $recordModel */
        $moduleName = $request->getModule();
        $entityId = $request->get('record');
        $idTransaction = $request->get('idTransaction');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($entityId);

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
        $transactionId = $transactionRecordModel->saveTransaction($idTransaction, $entityId, $module, $entityId, null, null, $full_content, $record->get('description'));
        $response->setResult($transactionId);
        return $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function delete(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $model = new QuotingTool_Record_Model();
        $success = $model->delete($recordId);
        header("Location: index.php?module=QuotingTool&view=List");
    }

    public function getTemplate(Vtiger_Request $request) {
        $module = $request->getModule();
        $relModule = $request->get('rel_module');
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        if (!$relModule) {
            $response->setError(200, vtranslate('LBL_INVALID_MODULE', $module));
            return $response->emit();
        }

        $quotingToolRecordModel = new QuotingTool_Record_Model();
        $templates = $quotingToolRecordModel->findByModule($relModule);
        /** @var Vtiger_Record_Model $template */
        foreach ($templates as $template) {
            $templateModule = vtranslate($template->get('module'), $template->get('module')) ;
            $childModule = '';
            if ($template->get('createnewrecords') == 1 && $templateModule != $relModule) {
                $childModule = " <i>(".$templateModule.")</i> ";
            }
            $data[] = array(
                'id' => $template->getId(),
                'filename' => $template->get('filename').$childModule,
                'description' => $template->get('description'),
                'createnewrecords' => $template->get('createnewrecords'),
                'modulename' => $template->get('module')
            );
        }

        $response->setResult($data);
        return $response->emit();
    }

    public function getHistories(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        $record = $request->get('record');
        $calendarDatetimeUIType = new Calendar_Datetime_UIType();
        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $histories = $historyRecordModel->listAllByTemplateId($record);

        /** @var Vtiger_Record_Model $history */
        foreach ($histories as $history) {
            $data[] = array(
                'id' => intval($history->getId()),
//                'name' => $history->get('filename'),
                'created' =>  $calendarDatetimeUIType->getDisplayValue($history->get('created'))
            );
        }

        $response->setResult($data);
        return $response->emit();
    }

    public function getHistory(Vtiger_Request $request) {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $historyId = $request->get('history_id');

        if (!$historyId) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $history = $historyRecordModel->getById($historyId);

        /** @var Vtiger_Record_Model $history */
        if (!$history) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data = array(
            'id' => $history->getId(),
            'body' => $history->get('body')
        );

        $response->setResult($data);
        return $response->emit();
    }

    public function removeHistories(Vtiger_Request $request) {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        $historyIds = $request->get('history_id');

        if (!$historyIds) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $historyIds = array_map('trim', explode(',', $historyIds));

        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $success = $historyRecordModel->removeHistories($historyIds);

        if (!$success) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $response->setResult($data);
        return $response->emit();
    }

    public function getAllRecord(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $data = array();
        $quotingToolRecordModel = new QuotingTool_Record_Model();
        $templates = $quotingToolRecordModel->findAll();
        /** @var Vtiger_Record_Model $template */
        foreach ($templates as $template) {
            $data[] = array(
                'id' => $template->getId(),
                'filename' => $template->get('filename'),
                'module' => $template->get('module'),
                'description' => $template->get('description')
            );
        }
        $response->setResult($data);
        return $response->emit();
    }

    public function exportTemplateQuotingTool(Vtiger_Request $request)
    {

        global $site_URL;
        // Check dir
        if (!file_exists('storage/QuotingTool/')) {
            if (!mkdir('storage/QuotingTool/', 0777, true))
                return '';
        }
        include_once 'include/simplehtmldom/simple_html_dom.php';
        $recordTemplate = $request->get("idtemplate");
        $recordModel = new QuotingTool_Record_Model();
        $recordSettingModel = new QuotingTool_SettingRecord_Model();
        $record = $recordModel->getById($recordTemplate);
        $time = time();
        $fieldDecode = array('body', 'header', 'content', 'footer','email_subject', 'email_content');
        $fileName = preg_replace("/[^A-Za-z0-9]/", '_', $record->get('filename'));
        $templatePath = "template_".$fileName.$time;
        mkdir('storage/QuotingTool/'.$templatePath."/upload/files", 0777, true);
        mkdir('storage/QuotingTool/'.$templatePath."/upload/images", 0777, true);
        $fullPath = 'storage/QuotingTool/'.$templatePath."/upload";
        $fullPathFiles = 'storage/QuotingTool/'.$templatePath."/upload/files";
        $fullPathImg = 'storage/QuotingTool/'.$templatePath."/upload/images";


        $recordSetting = $recordSettingModel->findByTemplateId($recordTemplate);
        $dom = new DOMDocument('1.0','UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('root');
        $dom->appendChild($root);

        $quotingTool = $dom->createElement('quotingtool');

        $root->appendChild($quotingTool);
        foreach ($recordModel->quotingToolFields as $field) {
            if ($field == "attachments") {
                $attachments = json_decode(htmlspecialchars_decode($record->get($field)), true);
                foreach ($attachments as $key => $file) {
                    $fileAtt = explode(".", $file['name']);
                    $fileAtt[0] = $fileAtt[0]."_".$time;
                    $needFile = implode('.',$fileAtt);
                    copy($file['full_path'], "$fullPathFiles/".$needFile);

                    $fileAttachMent = explode("/", $file['full_path']);
                    if ($fileAttachMent) {
                        array_pop($fileAttachMent);
                        $fileAttachMent[] = $needFile;
                        $newFile = implode('/', $fileAttachMent);
                        $attachments[$key]['full_path'] = str_replace($site_URL, '$site_url$', $newFile);
                    }
                }
                $newVal =  ($attachments) ? QuotingToolUtils::jsonUnescapedSlashes(json_encode($attachments)) : null;
                $quotingTool->appendChild( $dom->createElement($field, $newVal));
            } elseif (in_array($field, $fieldDecode)) {
                $content = base64_decode($record->get($field));
                $html = str_get_html($content);
                if (!$html) {
                    $content = base64_encode($content);
                    $quotingTool->appendChild($dom->createElement($field, $content));
                    continue;
                }
                if (count($html->find('img')) > 0) {
                    foreach ($html->find('img') as $img) {
                        if (strpos($img->attr["src"], $site_URL) !== false) {
                            $linkImg = $img->attr["src"];
                            $imgName = explode("/", $linkImg);
                            if ($imgName) {
                                $oldImgName = end($imgName);
                                $fileNameImg = explode(".", $oldImgName);
                                $fileNameImg[0] = $fileNameImg[0]."_".$time;
                                $needFileNameImg = implode('.',$fileNameImg);
                                copy($linkImg, "$fullPathImg/" . $needFileNameImg);

                                array_pop($imgName);
                                $imgName[] = $needFileNameImg;
                                $newVal = str_replace($site_URL, '$site_url$', implode('/', $imgName));
                                $img->setAttribute("src", $newVal);

                                $linkDataCke = $img->attr["data-cke-saved-src"];
                                $newlinkDataCke = str_replace($site_URL, '$site_url$', $linkDataCke);
                                $newlinkDataCke = str_replace($oldImgName, $needFileNameImg, $newlinkDataCke);
                                $img->setAttribute("data-cke-saved-src", $newlinkDataCke);
                            }
                        }
                    }
                }
                // save html
                $content = $html->save();
                $content = base64_encode($content);
                $quotingTool->appendChild($dom->createElement($field, $content));
            } else{
                $quotingTool->appendChild( $dom->createElement($field, $record->get($field)) );
            }
        }

        $settingQuotingTool = $dom->createElement('quotingtool_settings');
//        $quotingToolSettingFields = array('background', 'label_accept', 'label_decline', 'expire_in_days');
        $root->appendChild($settingQuotingTool);
        foreach ($recordSettingModel->quotingToolSettingFields as $field) {
            if ($field == "background") {
                $img = json_decode(htmlspecialchars_decode($recordSetting->get($field)), true);
                if ($img['image'] != '') {
                    $imgName = explode("/", $img['image']);
                    if (strpos($img['image'], $site_URL) !== false) {
                        $oldBackGround = end($imgName);
                        $newNameBackGround = explode(".", $oldBackGround);
                        $newNameBackGround[0] = $newNameBackGround[0]."_".$time;
                        $needNameBackGround = implode('.',$newNameBackGround);
                        array_pop($imgName);
                        $imgName[] = $needNameBackGround;
                        $newVal = str_replace($site_URL, '$site_url$', implode('/', $imgName));
                        copy($img['image'], "$fullPathImg/".$needNameBackGround);
                        $img['image'] = $newVal;
                    }
                    $backGround = ($img) ?
                        QuotingToolUtils::jsonUnescapedSlashes(json_encode($img, JSON_FORCE_OBJECT)) : null;
                    $settingQuotingTool->appendChild( $dom->createElement($field, $backGround) );
                }else {
                    $settingQuotingTool->appendChild( $dom->createElement($field, $recordSetting->get($field)) );
                }

            }
            else{
                $settingQuotingTool->appendChild( $dom->createElement($field, $recordSetting->get($field)) );
            }
        }

        $fileName = $fileName.".xml";
        $dom->save("storage/QuotingTool/".$templatePath."/".$fileName);

//      zip file
        $dir = 'storage/QuotingTool/'.$templatePath;
        $zip_file = $templatePath.'.zip';

//      Get real path for our folder
        $rootPath = realpath($dir);

//      Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

//      Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                if($relativePath && strpos($relativePath, '\\')!== false) $relativePath = preg_replace("/\\\\/", "/", $relativePath);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

//      Zip archive will be created only after closing object
        $zip->close();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($zip_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zip_file));
        readfile($zip_file);
        $this->rrmdir($dir);
        exit;
    }

    public function importTemplate(Vtiger_Request $request)
    {
        global $site_URL;
        include_once "modules/QuotingTool/resources/uploadfile/server/php/UploadHandler.php";
        include_once 'include/simplehtmldom/simple_html_dom.php';
        $path = "storage/QuotingTool/ImportTemplate/";
        $fieldDecode = array('body', 'header', 'content', 'footer','email_subject', 'email_content');
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, true))
                return '';
        }
        $response = new Vtiger_Response();
        $data = array();
        $allFiles = array();
        /** @var QuotingTool_Record_Model $recordModel */
        $recordModel = new QuotingTool_Record_Model();
        $recordSettingModel = new QuotingTool_SettingRecord_Model();
        $id = 0;
        $module = $request->getModule();
        if (!empty($_FILES)) {
            $time = time();
            $options = array(
                'script_url' => 'index.php?module=QuotingTool&action=ActionAjax&mode=importTemplate',
                'upload_dir' => 'storage/QuotingTool/ImportTemplate/'.$time."/",
                'upload_url' => $site_URL . 'storage/QuotingTool/ImportTemplate/'.$time."/",
                'print_response' => false
            );
            $upload_handler = new UploadHandler($options);

            if ($upload_handler->response['files'] && count($upload_handler->response['files']) > 0) {
                foreach ($upload_handler->response['files'] as $file) {
                    $filePath = utf8_decode(urldecode(str_replace($site_URL, '', $file->url)));
                    $id = QuotingTool_ActionAjax_Action::processImportFile($recordSettingModel, $recordModel, $path, $time, $filePath, $fieldDecode);
                }
            }
        }
        $this->rrmdir($path);
        if (!$id) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data['id'] = $id;
        $data['message'] = vtranslate('LBL_SUCCESSFUL', $module);

        $response->setResult($data);
        return $response->emit();

    }

    public function processImportFile($recordSettingModel, $recordModel, $path ,$time, $filePath, $fieldDecode )
    {
        global $site_URL;
        $pathunzip = $path.$time;
        $zip = new ZipArchive;
        $res = $zip->open($filePath);
        if ($res === TRUE) {
            $zip->extractTo($pathunzip);
            $zip->close();
        }
        $pathImg = "storage/QuotingTool/ImportTemplate/".$time."/upload/images";
        $pathFile = "storage/QuotingTool/ImportTemplate/".$time."/upload/files";
        $pathCoreImg = "test/upload/images";
        $pathCoreFile = "test/upload/files";
        $this->recurse_copy($pathImg, $pathCoreImg);
        $this->recurse_copy($pathFile, $pathCoreFile);
        $xmlFile = glob("storage/QuotingTool/ImportTemplate/".$time."/*.xml");

        $xml = simplexml_load_file(utf8_decode(urldecode($xmlFile[0]))) or die("Error: Cannot create object");
        $params = array();
        $paramsSetting = array();
        foreach ($recordModel->quotingToolFields as $field) {
            if ($field == "attachments") {
                $needval = str_replace('$site_url$',$site_URL,$xml->quotingtool->{$field});
                $params[$field] = $needval;
            } elseif (in_array($field, $fieldDecode)) {
                $content = base64_decode($xml->quotingtool->{$field});
                $html = str_get_html($content);
                if (!$html) {
                    $content = base64_encode($content);
                    $params[$field] = $content;
                    continue;
                }
                if (count($html->find('img')) > 0) {
                    foreach ($html->find('img') as $img) {
                        if (strpos($img->attr["src"], '$site_url$') !==false) {
                            $linkImg = $img->attr["src"];
                            $needVal = str_replace('$site_url$', $site_URL, $linkImg);
                            $img->setAttribute("src", $needVal);
                            $linkDataCke = $img->attr["data-cke-saved-src"];
                            $newlinkDataCke = str_replace('$site_url$', $site_URL, $linkDataCke);
                            $img->setAttribute("data-cke-saved-src", $newlinkDataCke);
                        }
                    }
                }
                // save html
                $content = $html->save();
                $content = base64_encode($content);
                $params[$field] = $content;
            } else {
                if ($field == 'is_active') {
                    $params[$field] = 1;
                }else{
                    $params[$field] = $xml->quotingtool->{$field};
                }
            }
        }
        $template = $recordModel->save('', $params);
        $id = $template->getId();

        //setting fields
        foreach ($recordSettingModel->quotingToolSettingFields as $field) {
            if ($field == "background") {
                $img = htmlspecialchars_decode($xml->quotingtool_settings->{$field}, true);
                $needVal = str_replace('$site_url$', $site_URL, $img);
                $paramsSetting[$field] = $needVal;
            }
            else{
                $paramsSetting[$field] = $xml->quotingtool_settings->{$field};
            }
        }
        // Save data setting
        $recordSettingModel->saveByTemplate($id, $paramsSetting);

        // Save history
        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $historyParams = array(
            'body' => $xml->quotingtool->body
        );
        $historyRecordModel->saveByTemplate($id, $historyParams);
        return $id;
    }

    public function ImportDefaultTemplates(Vtiger_Request $request)
    {
        global $site_URL;
        $response = new Vtiger_Response();
        $zipFileName = $request->get('selectedValue');
        $module = 'QuotingTool';
        if ($zipFileName && $zipFileName != 'Default') {
            include_once "modules/QuotingTool/resources/uploadfile/server/php/UploadHandler.php";
            include_once 'include/simplehtmldom/simple_html_dom.php';
            $time = time();
            $path = "storage/QuotingTool/ImportTemplate/".$time;
            $recordModel = new QuotingTool_Record_Model();
            $recordSettingModel = new QuotingTool_SettingRecord_Model();
            $fieldDecode = array('body', 'header', 'content', 'footer','email_subject', 'email_content');
            if (!file_exists($path)) {
                if (!mkdir($path, 0777, true))
                    return '';
            }
            $uploadUrl = $site_URL . 'storage/QuotingTool/ImportTemplate/'.$time."/";
            $linkDownload = 'https://www.vtexperts.com/files/dd/'.$zipFileName;
            $fileContent = file_put_contents('storage/QuotingTool/ImportTemplate/'.$time.'/'.$zipFileName , fopen($linkDownload, 'r'));
            $filePath = utf8_decode(urldecode(str_replace($site_URL, '', $uploadUrl.$zipFileName)));
            $pathunzip = 'storage/QuotingTool/ImportTemplate/';
            $id = 0;
            $id = QuotingTool_ActionAjax_Action::processImportFile($recordSettingModel, $recordModel, $pathunzip, $time, $filePath, $fieldDecode);
        }
        $this->rrmdir($path);
        if (!$id || $id == 0) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data['id'] = $id;
        $data['message'] = vtranslate('Import Template Successful', $module);

        $response->setResult($data);
        return $response->emit();

    }

    public function CreateNewProposal(Vtiger_Request $request)
    {
        global $site_URL, $current_user;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $templateId = $request->get('template_id');
        $relModule = $request->get('primaryModule');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($templateId);
        $quotingTool = new QuotingTool();
        // get $token before decompileRecord because when after decompileRecord , get $token incorrect (\$)
        $varContent = $quotingTool->getVarFromString(base64_decode($record->get('content')));
        $record = $record->decompileRecord('0', array('content', 'header', 'footer', 'email_subject', 'email_content'));
        /** @var QuotingTool_Record_Model $model */
        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
        // Encode before put to database
        $full_content = base64_encode($record->get('content'));
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $record->get('module'), 0, null, null, $full_content, $record->get('description'));
        $transactionRecord = $transactionRecordModel->findById($transactionId);
        $hash = $transactionRecord->get('hash');
        $hash = $hash ? $hash : '';
        // Merge special tokens
        $keys_values = array();
        $site = rtrim($site_URL, '/');
            $link = "{$site}/modules/QuotingTool/proposal/index.php?record={$transactionId}&session={$hash}&id={$templateId}&newrecord=true";
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
            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $relModule, 0, null, null, $full_content, $record->get('description'));
        }
        $data = array(
            'site_url' => preg_replace("(^(https?|ftp)://)", "", $site),
            'link_propocal' => "/modules/QuotingTool/proposal/index.php?record={$transactionId}&session={$hash}&id={$templateId}",
        );

        $response->setResult($link);
        return $response->emit();
    }
    function recurse_copy($src,$dst) {
        $dir = opendir($src);
        if($dir) {
            @mkdir($dst);

            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($src . '/' . $file) ) {
                        $result = $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                    }     else {
                        $result = copy($src . '/' . $file,$dst . '/' . $file);
                    }
                }
            }

            closedir($dir);
        }
    }
    function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir"){
                        $this->rrmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}