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
 * Class QuotingTool_Edit_View
 */
Class QuotingTool_Edit_View extends Vtiger_Edit_View
{
    /**
     * @var bool
     */
    protected $record = false;

    /**
     * @constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->vteLicense();
    }

    function vteLicense() {
        $vTELicense=new QuotingTool_VTELicense_Model('QuotingTool');
        if(!$vTELicense->validate()){
            header("Location: index.php?module=QuotingTool&view=List&mode=step2");
        }
    }

    /**
     * @param Vtiger_Request $request
     * @return bool|void
     * @throws AppException
     */
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);

        if (!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    /**
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        global $current_user, $vtiger_current_version, $adb;
        
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $quotingTool = new QuotingTool();
        $primaryModule = $request->get('primary_module');
        $record = $request->get('record');
        $quotingToolRecordModel = new QuotingTool_Record_Model();
        $template = $quotingToolRecordModel->getById($record);
        if(version_compare($vtiger_current_version, '7.0.0', '>=') && $record !='') {
            $template = $this->updateModule($template);
        }

        $userProfile = array(
            'user_name' => $current_user->user_name,
            'first_name' => $current_user->first_name,
            'last_name' => $current_user->last_name,
            'full_name' => $current_user->first_name . ' ' . $current_user->last_name,
            'email1' => $current_user->email1,
        );

        $quotingToolSettingRecordModel = new QuotingTool_SettingRecord_Model();
        $settings = array();

        if ($template) {
            $objSettings = $quotingToolSettingRecordModel->findByTemplateId($record);
            if ($objSettings) {
                $settings = array(
                    'template_id' => $objSettings->get('template_id'),
                    'description' => $objSettings->get('description'),
                    'expire_in_days' => $objSettings->get('expire_in_days'),
                    'label_decline' => $objSettings->get('label_decline'),
                    'label_accept' => $objSettings->get('label_accept'),
                    'background' => json_decode(html_entity_decode($objSettings->get('background')))
                );
            }
        } else {
            $template = Vtiger_Record_Model::getCleanInstance($moduleName);
            $template->set('module', $primaryModule);
        }

        // Pricing table (IDC - Quoter & VTEItem)
        $vteItemsModuleName = 'VTEItems';
        $vteItemsModuleModel = Vtiger_Module_Model::getInstance($vteItemsModuleName);
        $quoterModuleName = 'Quoter';
        /** @var Quoter_Module_Model $quoterModel */
        $quoterModel = Vtiger_Module_Model::getInstance($quoterModuleName);


        if ($vteItemsModuleModel && $quoterModel && $quoterModel->isActive() && $vteItemsModuleModel->isActive()) {
            $columnDefault=array("item_name","quantity","listprice","total","tax_total","net_price","comment","discount_amount","discount_percent");
            $listTable = array('quoter_quotes_settings','quoter_invoice_settings','quoter_salesorder_settings','quoter_purchaseorder_settings');
            $settingsFieldItems= array();
            foreach($listTable as $table){
                $rs = $adb->pquery("SELECT * FROM $table",array());
                if($adb->num_rows($rs) > 0){
                    $data = $adb->fetchByAssoc($rs,0);
                    $module = $data['module'];
                    foreach($data as $key => $val){
                        if(!empty($val) && $key !='module' && $key!='total_fields' && $key!='section_setting'){
                            if ($key == 'item_name') {
                                $settingsFieldItems[$module][] = 'productid';
                            }else{
                                $settingsFieldItems[$module][] = $key;
                            }
                        }
                    }
                }
            }
            $vteItemsModuleInfo = $quotingTool->parseModule($vteItemsModuleModel);
            $settingFieldsQuoter = array();
            foreach ($settingsFieldItems as $module => $value) {
                $settingFieldsQuoter[$module]['id'] = $vteItemsModuleModel->getId();
                $settingFieldsQuoter[$module]['name'] = $vteItemsModuleModel->getName();
                $settingFieldsQuoter[$module]['label'] = vtranslate($vteItemsModuleModel->get('label'), $vteItemsModuleModel->getName());
                    foreach ($vteItemsModuleInfo['fields'] as $key => $val ) {
                        if (in_array($val['name'],$value )) {
                            $settingFieldsQuoter[$module]['fields'][] = $vteItemsModuleInfo['fields'][$key];
                        }
                    }
            }
            $fieldSpecial = array('source', 'starred', 'tags', 'related_to');

            foreach ($vteItemsModuleInfo['fields'] as $key=> $item) {
                if (in_array($item['name'], $fieldSpecial)){
                    unset($vteItemsModuleInfo['fields'][$key]);
                }
            }

            $vteItemsModuleInfo['fields'] = array_values($vteItemsModuleInfo['fields']);
            $quoterSettings = array();
            //
            $totalSetting = $quoterModel->getAllTotalFieldsSetting();
            foreach ($totalSetting as $module => $setting) {
                $moduleInfo = $settingFieldsQuoter[$module];

                $totalBlock = array(
                    'name' => 'LBL_TOTAL_BLOCK',
                    'fields' => array()
                );

                foreach ($setting as $totalFieldName => $totalField) {
                    $totalBlock['fields'][] = array(
                        'name' => $totalFieldName,
                        'datatype' => $totalField['fieldType'],
                        'label' => vtranslate($totalField['fieldLabel'], $quoterModuleName)
                    );
                }

                $blocks = array();
                $blocks[] = $totalBlock;
                $moduleInfo['final_details'] = $quotingTool->fillBlockFields($vteItemsModuleName, $blocks);

                $quoterSettings[$module] = $moduleInfo;
            }

            $viewer->assign('QUOTER_SETTINGS', $quoterSettings);
        }

        $isIconHelpText = $quotingToolRecordModel->isIconHelpText();

        $viewer->assign('RECORD_ID', $record);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('TEMPLATE', $template);
        $viewer->assign('SETTINGS', QuotingToolUtils::jsonUnescapedSlashes(json_encode($settings, JSON_FORCE_OBJECT)));
        $viewer->assign('USER_PROFILE', $userProfile);
        $viewer->assign('CONFIG', QuotingTool::getConfig());
        $viewer->assign('MODULES', QuotingTool::getModules());

        $viewer->assign('CUSTOM_FUNCTIONS', QuotingTool::getCustomFunctions());
        $viewer->assign('CUSTOM_FIELDS', QuotingTool::getCustomFields());
        $viewer->assign('COMPANY_FIELDS', QuotingTool::getCompanyFields());
        $viewer->assign('ICON_HELPTEXT', $isIconHelpText);

        $viewer->view('EditView.tpl', $moduleName);
    }

    /**
     * @param Vtiger_Request $request
     * @return array
     */
    public function getHeaderCss(Vtiger_Request $request)
    {
        global $vtiger_current_version;

        $moduleName = $request->getModule();
        $fontAwesome = '';
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
            $fontAwesome = "~/$template_folder/modules/$moduleName/resources/css/font-awesome-4.5.0/css/font-awesome.min.css";
        }elsE{
            $template_folder= "layouts/v7";
        }


        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            "~/$template_folder/modules/$moduleName/resources/css/bootstrap.icon.css",
            "~/$template_folder/modules/$moduleName/resources/js/libs/ckeditor_4.5.6_full/CustomFonts/fonts.css",
            "~/libraries/bootstrap/js/eternicode-bootstrap-datepicker/css/datepicker.css",
            "~/$template_folder/modules/$moduleName/resources/js/libs/signature-pad/assets/jquery.signaturepad.css",
            $fontAwesome,
            "~/modules/$moduleName/resources/styles.css",
            "~/modules/$moduleName/resources/web.css",
            "~/$template_folder/modules/$moduleName/resources/css/app.css",
            '~/libraries/jquery/colorpicker/css/colorpicker.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return array
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        global $vtiger_current_version;

        $moduleName = $request->getModule();
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
        }elsE{
            $template_folder= "layouts/v7";
        }

        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames = array(
            /*Begin libs*/
            "~/modules/$moduleName/resources/mpdf/mpdf.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/modernizr-2.8.3/modernizr.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/angularjs-1.3.1/angular.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/angular-resource-1.3.1/angular-resource.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/angular-ui-router-0.2.11/angular-ui-router.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/angular-translate-2.4.2/angular-translate.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/ui-bootstrap-tpls-0.14.3/ui-bootstrap-tpls-0.14.3.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/angular-sanitize-1.2.26/angular-sanitize.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/jquery.nicescroll-3.6.0/jquery.nicescroll.min.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/ckeditor_4.5.6_full/override_ckeditor.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/ckeditor_4.5.6_full/ckeditor.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/ckeditor_4.5.6_full/adapters/jquery.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/ng-ckeditor-0.2.0/ng-ckeditor.min.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/signature-pad/jquery.signaturepad.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/signature-pad/assets/flashcanvas.js",

            "~/$template_folder/modules/$moduleName/resources/js/libs/css-element-queries/src/ResizeSensor.js",
            "~/$template_folder/modules/$moduleName/resources/js/libs/css-element-queries/src/ElementQueries.js",

            /*End libs*/
            /*Begin configs & init app*/
            "~/$template_folder/modules/$moduleName/resources/js/configs/app-constants.js",
            "~/$template_folder/modules/$moduleName/resources/js/configs/app-config.js",
            "~/$template_folder/modules/$moduleName/resources/js/app.js",
            /*End configs & init app*/
            /*Begin utils*/
            "~/$template_folder/modules/$moduleName/resources/js/utils/app-utils.js",
            "~/$template_folder/modules/$moduleName/resources/js/utils/helper.js",
            "~/$template_folder/modules/$moduleName/resources/js/utils/jQuery-customs.js",
            /*End utils*/
            /*Begin directives*/
            "~/$template_folder/modules/$moduleName/resources/js/directives/app-directive.js",
            "~/$template_folder/modules/$moduleName/resources/js/directives/file.js",
            "~/$template_folder/modules/$moduleName/resources/js/directives/datetime.js",
            "~/$template_folder/modules/$moduleName/resources/js/directives/select2.js",
            /*End directives*/
            /*Begin locale*/
            "~/$template_folder/modules/$moduleName/resources/js/locale/i18n.js",
            "~/$template_folder/modules/$moduleName/resources/js/locale/app-i18n.js",
            "~/$template_folder/modules/$moduleName/resources/js/locale/en.js",
            /*End locale*/
            /*Begin models*/
            "~/$template_folder/modules/$moduleName/resources/js/models/app-model.js",
            "~/$template_folder/modules/$moduleName/resources/js/models/template.js",
            /*End models*/
            /*Begin controllers*/
            "~/$template_folder/modules/$moduleName/resources/js/controllers/app-controller.js",
            "~/$template_folder/modules/$moduleName/resources/js/controllers/right-panel-controller.js",
            /*End controllers*/
            "modules.Emails.resources.Emails",
            "libraries/jquery/colorpicker/js/colorpicker",
            "libraries/jquery/colorpicker/js/eye",
            "libraries/jquery/colorpicker/js/utils"
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function updateModule($template)
    {
        $body = base64_decode($template->get('body'));
        $find = "layouts/vlayout/modules/QuotingTool";
        $replace = "layouts/v7/modules/QuotingTool";
        $results =  str_replace($find, $replace, $body);
        $data = $template->set('body', base64_encode($results));

        return $data;
    }

}