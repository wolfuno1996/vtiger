<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/com_vtiger_workflow/include.inc';
include_once 'modules/QuotingTool/QuotingToolUtils.php';

/**
 * Class QuotingTool
 */
class QuotingTool extends CRMEntity
{

    var $table_name = 'vtiger_quotingtool';
    var $table_index = 'id';
    var $tab_name = Array('vtiger_quotingtool');
    var $tab_name_index = Array('vtiger_quotingtool' => 'id');

    /**
     * const
     */
    const MODULE_NAME = 'QuotingTool';
    /**
     * @var array
     */
//    public $enableModules = array('Quotes', 'HelpDesk', 'Potentials', 'Contacts', 'Leads', 'Accounts', 'Invoice', 'PurchaseOrder',
//        'SalesOrder', 'ServiceContracts', 'Project', 'ProjectTask', 'ProjectMilestone');
    public $enableModules = array();
    public $specialModules = array('Users');
    public $ignoreLinkModules = array('Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter');
    public $inventoryModules = array();
    /**
     * @var string
     */
    public $pdfLibLink = 'https://www.vtexperts.com/files/mpdf.zip';
    /**
     * @var array
     */
    public $workflows = array(
        'QuotingToolMailTask' => 'Send Email with Quoting Tool attachments'
    );
    // [Module [Block [Field]]]
    public $injectFields = array(
        'Users' => array(
            'LBL_USER_ADV_OPTIONS' => array('*'),
            'LBL_TAG_CLOUD_DISPLAY' => array('*'),
            'LBL_CURRENCY_CONFIGURATION' => array('*'),
            'LBL_CALENDAR_SETTINGS' => array('*'),
            'LBL_USERLOGIN_ROLE' => array('confirm_password', 'user_password')
        )
    );
    public $ignoreSpecialFields = array('starred', 'tags');

    // Regex for get variable from 2 dollar sign ($var$)
    // Old: '/.*?[^\\\]\\$(.+?[^\\\])\\$/'
    var $patternVar = '/\$([a-zA-Z0-9_]+?)\$/';   // '.*?[^\\]\$(.+?[^\\])\$'
    var $patternEscapeCharacters = '/.*?[\\\](.+?)/';   // '.*?[\\](.+?)'

    public function __construct()
    {
        // Translate the labels
        foreach ($this->workflows as $name => $label) {
            $this->workflows[$name] = vtranslate($label, self::MODULE_NAME);
        }

        // Inventory Modules
        $this->inventoryModules = getInventoryModules();
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type)
    {
        if ($event_type == 'module.postinstall') {
            // Handle actions when this module is install.
            self::updateModule($modulename);
            self::addWidgetTo($modulename);
            // self::addPDFWidget($modulename, $this->enableModules);
            self::installWorkflows($modulename);
            self::resetValid();
        } else if ($event_type == 'module.disabled') {
            // Handle actions when this module is disabled.
            self::removeWidgetTo($modulename);
            // self::removePDFWidget($modulename, $modulename, $this->enableModules);
            self::removeWorkflows($modulename);
        } else if ($event_type == 'module.enabled') {
            // Handle actions when this module is enabled.
             self::updateModule($modulename);
            self::addWidgetTo($modulename);
            // self::addPDFWidget($modulename, $this->enableModules);
            self::installWorkflows($modulename);
        } else if ($event_type == 'module.preuninstall') {
            // Handle actions when this module is about to be deleted.
            // Disable PDFWidget feature
            self::removeWidgetTo($modulename);
            self::removePDFWidget($modulename, $this->enableModules);
            self::removeWorkflows($modulename);
            self::removeValid();
        } else if ($event_type == 'module.preupdate') {
            // Handle actions before this module is updated.
            self::removeWidgetTo($modulename);
            // Disable PDFWidget feature
            self::removePDFWidget($modulename, $this->enableModules);
            self::removeWorkflows($modulename);
        } else if ($event_type == 'module.postupdate') {
            // Handle actions when this module is update.
            self::updateModule($modulename);
            self::addWidgetTo($modulename);
            // self::addPDFWidget($modulename, $this->enableModules);
            self::installWorkflows($modulename);
            self::resetValid();
        }
    }

    /**
     * @param string $moduleName
     */
    static function addWidgetTo($moduleName)
    {
        global $adb, $vtiger_current_version;

        $module = Vtiger_Module::getInstance($moduleName);
        $widgetName = 'Quoting Tool';
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
        }elsE{
            $template_folder= "layouts/v7";
        }

            if ($module) {
            $css_widgetType = 'HEADERCSS';
            $css_widgetLabel = vtranslate($widgetName, $moduleName);
            $css_link = "$template_folder/modules/{$moduleName}/resources/{$moduleName}CSS.css";

            $js_widgetType = 'HEADERSCRIPT';
            $js_widgetLabel = vtranslate($widgetName, $moduleName);
            $js_link = "$template_folder/modules/{$moduleName}/resources/{$moduleName}JS.js";
            $js_link_2 = "$template_folder/modules/{$moduleName}/resources/{$moduleName}Utils.js";

            // css
            $module->addLink($css_widgetType, $css_widgetLabel, $css_link);
            // js
            $module->addLink($js_widgetType, $js_widgetLabel, $js_link);
            $module->addLink($js_widgetType, $js_widgetLabel, $js_link_2);
        }

        // Check module
        $rs = $adb->pquery("SELECT * FROM `vtiger_ws_entity` WHERE `name` = ?", array($moduleName));
        if ($adb->num_rows($rs) == 0) {
            $adb->pquery("INSERT INTO `vtiger_ws_entity` (`name`, `handler_path`, `handler_class`, `ismodule`)
            VALUES (?, 'include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation', '1');", array($moduleName));
        }
        $max_id=$adb->getUniqueID('vtiger_settings_field');
        $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`) VALUES (?, ?, ?, ?, ?, ?)",array($max_id, '4', 'Document Designer', 'Settings area for Document Designer', 'index.php?module=QuotingTool&parent=Settings&view=Settings', $max_id));
    }

    /**
     * @param $moduleName
     */
    static function removeWidgetTo($moduleName)
    {
        global $adb, $vtiger_current_version;

        $module = Vtiger_Module::getInstance($moduleName);
        $widgetName = 'Quoting Tool';
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
            $vtVersion='vt6';
            $css_link_vt6 = "$template_folder/modules/{$moduleName}/resources/{$moduleName}CSS.css";
            $js_link_vt6 = "$template_folder/modules/{$moduleName}/resources/{$moduleName}JS.js";
            $js_link_2_vt6 = "$template_folder/modules/{$moduleName}/resources/{$moduleName}Utils.js";
        }elsE{
            $template_folder= "layouts/v7";
            $vtVersion='vt7';
        }

        if ($module) {
            $css_widgetType = 'HEADERCSS';
            $css_widgetLabel = vtranslate($widgetName, $moduleName);
            $css_link = "$template_folder/modules/{$moduleName}/resources/{$moduleName}CSS.css";

            $js_widgetType = 'HEADERSCRIPT';
            $js_widgetLabel = vtranslate($widgetName, $moduleName);
            $js_link = "$template_folder/modules/{$moduleName}/resources/{$moduleName}JS.js";
            $js_link_2 = "$template_folder/modules/{$moduleName}/resources/{$moduleName}Utils.js";

            // css
            $module->deleteLink($css_widgetType, $css_widgetLabel, $css_link);
            // js
            $module->deleteLink($js_widgetType, $js_widgetLabel, $js_link);
            $module->deleteLink($js_widgetType, $js_widgetLabel, $js_link_2);

            // remove existed link on vt6 when current vt is vt7
            if($vtVersion!='vt6'){
                $module->deleteLink($css_widgetType, $css_widgetLabel, $css_link_vt6);
                $module->deleteLink($js_widgetType, $js_widgetLabel, $js_link_vt6);
                $module->deleteLink($js_widgetType, $js_widgetLabel, $js_link_2_vt6);
            }
        }

        // Check module
        $adb->pquery("DELETE FROM `vtiger_ws_entity` WHERE `name` = ?", array($moduleName));
        $adb->pquery("DELETE FROM vtiger_settings_field WHERE `name` = ?",array('Document Designer'));
    }

    /**
     * Add widget to other module.
     * @param string $moduleName
     * @param array $moduleNames
     * @param string $widgetType
     * @param string $widgetName
     */
    function addPDFWidget($moduleName, $moduleNames, $widgetType = 'DETAILVIEWSIDEBARWIDGET', $widgetName = 'Quoting Tool')
    {
        if (empty($moduleNames))
            return;

        if (is_string($moduleNames))
            $moduleNames = array($moduleNames);

        $widgetLabel = vtranslate($widgetName, $moduleName);
        $url = 'module=' . $moduleName . '&view=Widget';

        foreach ($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
                $module->addLink($widgetType, $widgetLabel, $url, '', '', '');
            }
        }
    }

    /**
     * Remove widget from other modules.
     * @param string $moduleName
     * @param array $moduleNames
     * @param string $widgetType
     * @param string $widgetName
     */
    function removePDFWidget($moduleName, $moduleNames, $widgetType = 'DETAILVIEWSIDEBARWIDGET', $widgetName = 'Quoting Tool')
    {
        if (empty($moduleNames)) {
            // Invalid modules
            return;
        }

        if (is_string($moduleNames)) {
            $moduleNames = array($moduleNames);
        }

        $widgetLabel = vtranslate($widgetName, $moduleName);
        $url = 'module=' . $moduleName . '&view=Widget';

        foreach ($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
                $module->deleteLink($widgetType, $widgetLabel, $url);
            }
        }

    }

    /**
     * @param string $fieldName
     * @param string $moduleName
     * @param bool $restrict
     * @return string
     */
    public function convertFieldToken($fieldName, $moduleName = null, $restrict = true)
    {
        $supportedModulesList = Settings_LayoutEditor_Module_Model::getSupportedModules();
        $supportedModulesList = array_flip($supportedModulesList);
        ksort($supportedModulesList);
        if (!$moduleName || ($restrict && !in_array($moduleName, $supportedModulesList) && !in_array($moduleName, $this->specialModules))) {
            $token = '$' . $fieldName . '$';
        } else {
            $token = '$' . $moduleName . '__' . $fieldName . '$';
        }

        return $token;
    }

    /**
     * @param string $token
     * @return array
     */
    public function extractFieldToken($token)
    {
        $tmp = explode('__', $token);
        $moduleName = $tmp[0];
        $fieldName = $tmp[1];
        return array(
            'moduleName' => $moduleName,
            'fieldName' => $fieldName
        );
    }

    /**
     * @param string $subject
     * @return array
     */
    public function getVarFromString($subject)
    {
        $vars = array();

        if ($subject) {
            preg_match_all($this->patternVar, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $v = array_unique($matches[1]);

                foreach ($v as $t) {
                    if (!in_array($t, $vars)) {
                        $vars[] = '$' . $t . '$';
                    }
                }
            }
        }

        return $vars;
    }

    /**
     * @param $subject
     * @return array
     */
    public function getFieldTokenFromString($subject)
    {
        $tokens = array();

        if ($subject) {
            preg_match_all($this->patternVar, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $tk = array_unique($matches[1]);

                foreach ($tk as $t) {
                    $extract = $this->extractFieldToken($t);

                    $moduleName = $extract['moduleName'];
                    $fieldName = $extract['fieldName'];

                    if (!array_key_exists($moduleName, $tokens)) {
                        $tokens[$moduleName] = array();
                    }

                    $needle = '$' . $t . '$';
                    $tokens[$moduleName][$needle] = $fieldName;
                }
            }
        }

        return $tokens;
    }

    /**
     * @param $subject
     * @return array
     */
    public function getEscapeCharactersFromString($subject)
    {
        $characters = array();

        if ($subject) {
            preg_match_all($this->patternEscapeCharacters, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $m = array_unique($matches[1]);

                foreach ($m as $c) {
                    if (!in_array($c, $characters)) {
                        $needle = '\\' . $c;
                        $characters[$needle] = $c;
                    }
                }
            }
        }

        return $characters;
    }

    /**
     * @param $subject
     * @return array
     */
    public function getEmailFromString($subject)
    {
        $email = '';

        if ($subject) {
            $pattern = '/\\((.*?)\\)/';
            preg_match_all($pattern, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $email = ($matches[1][0]) ? $matches[1][0] : $subject;
            }
        }

        return $email;
    }

    /**
     * @param $tokens
     * @param $record
     * @param $content
     * @return mixed
     */
    public function mergeBlockTokens($tokens, $record, $content)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        $crmid = 'crmid';
        $inventoryModules = getInventoryModules();
        $productModules = array('Products', 'Services');
        $currencyFieldsList = array('adjustment', 'grandTotal', 'hdnSubTotal', 'preTaxTotal', 'tax_totalamount',
            'shtax_totalamount', 'discountTotal_final', 'discount_amount_final', 'shipping_handling_charge', 'totalAfterDiscount');

        $blockStartTemplates = array(
            '#PRODUCTBLOC_START#',
            '#SERVICEBLOC_START#',
            '#PRODUCTSERVICEBLOC_START#'
        );
        $blockEndTemplates = array(
            '#PRODUCTBLOC_END#',
            '#SERVICEBLOC_END#',
            '#PRODUCTSERVICEBLOC_END#'
        );
        $blockTemplates = array_merge($blockStartTemplates, $blockEndTemplates);
        $dataTableType = null;
        /**
         * Copy from modules/Inventory/views/Detail.php:99
         */
        $currencyFieldsList2 = array('taxTotal', 'netPrice', 'listPrice', 'unitPrice', 'productTotal',
            'discountTotal', 'discount_amount');

        /** @var simple_html_dom_node $table */
        foreach ($html->find('table') as $table) {
            $dataTableType = $table->attr['data-table-type'];

            if (!$dataTableType || $dataTableType != 'pricing_table') {
                // Only parse pricing table
                continue;
            }

            $pdfContentModel = new QuotingTool_PDFContent_Model();
            if ($record == 0) {
                return $content;
            }
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            $moduleName = $recordModel->getModuleName();

            // Clean un-necessary attributes
            $table->removeAttribute('data-info');

            $isTemplateStart = false;
            $isTemplateEnd = false;

            $newHeader = array();
            $newBody = array();
            $newFooter = array();

            /** @var simple_html_dom_node $thead */
            $thead = null;
            /** @var simple_html_dom_node $tbody */
            $tbody = null;
            /** @var simple_html_dom_node $tfoot */
            $tfoot = null;

            $newHeaderTokens = array();
            $newBodyTokens = array();
            $newFooterTokens = array();

            $dataOddStyle = $table->attr['data-odd-style'];
            $dataEvenStyle = $table->attr['data-even-style'];

            /** @var simple_html_dom_node $row */
            foreach ($table->find('tr') as $row) {
                $isNormalRow = true;

                /** @var simple_html_dom_node $cell */
                foreach ($row->children() as $cell) {
                    $cellText = trim($cell->plaintext);

                    if (!in_array($cellText, $blockTemplates)) {
                        // Normal cell
                        continue;
                    }

                    $isNormalRow = false;
                    $cell->parent->outertext = $cellText;

                    if (in_array($cellText, $blockStartTemplates)) {
                        // BlockStart cell
                        $isTemplateStart = true;
                        break;
                    } else if (in_array($cellText, $blockEndTemplates)) {
                        // BlockEnd cell
                        $isTemplateEnd = true;
                        break;
                    }
                }

                if ($isNormalRow) {
                    if (!$isTemplateStart) {
                        $newHeader[] = $row;
                        $newHeaderTokens = array_merge($newHeaderTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateStart && !$isTemplateEnd) {
                        $newBody[] = $row;
                        $newBodyTokens = array_replace_recursive($newBodyTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateEnd) {
                        $newFooter[] = $row;
                        $newFooterTokens = array_merge($newFooterTokens, $this->getFieldTokenFromString($row->outertext));
                    }
                }

                /** @var simple_html_dom_node $parent */
                $parent = $row->parent();

                if (($thead === null) && ($parent->tag == 'thead')) {
                    $thead = $parent;
                } else if (($tbody === null) && ($parent->tag == 'tbody')) {
                    $tbody = $parent;
                } else if (($tfoot === null) && ($parent->tag == 'tfoot')) {
                    $tfoot = $parent;
                }
            }

            $innertext = '';

            // Header
            $tmpHead = '';
            foreach ($newHeader as $row) {
                $newTheadRowsText = $row->outertext;

                foreach ($newHeaderTokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = $recordModel->getDisplayValue($f, $record);
                        $newTheadRowsText = str_replace($k, $needValue, $newTheadRowsText);
                    }
                }

                $tmpHead .= $newTheadRowsText;
            }

            if ($thead !== null) {
                $thead->innertext = $tmpHead;
                $innertext .= $thead->outertext;
            } else if ($tbody !== null) {
                $innertext .= '<thead>' . $tmpHead . '</thead>';
            } else {
                $innertext .= $tmpHead;
            }

            // Body
            $tmpBody = '';
            if ($tbody !== null) {
                $dataOddStyle = $tbody->attr['data-odd-style'];
                $dataEvenStyle = $tbody->attr['data-even-style'];
            }

            $dataOddStyle = ($dataOddStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataOddStyle))) : '';
            $dataEvenStyle = ($dataEvenStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataEvenStyle))) : '';
            $final_details = array();

            if (in_array($moduleName, $inventoryModules)) {
                /** @var Inventory_Record_Model $recordModel */
                $recordModel = Inventory_Record_Model::getInstanceById($record, $moduleName);
                // Get products - to get final detail only
                $products = $recordModel->getProducts();

                if ($products && count($products) > 0) {
                    $final_details = $products[1]['final_details'];
                    $items = $pdfContentModel->getLineItemsAndTotal($record);

                    if ($items && count($items) > 0) {
                        // Merge
                        $products = $this->mergeRelatedProductWithQueryProduct($products, $items);
                    }

                    $counter = 0;
                    foreach ($products as $k => $value) {
                        $even = (++$counter % 2) == 0;

                        $cloneTbodyRowTokens = $newBodyTokens;

                        // Update token value to clone token
                        foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                            $moduleModel = Vtiger_Module_Model::getInstance($tModuleName);

                            foreach ($tFields as $fToken => $fName) {
                                // Hardcode fieldname - by vtiger core :(
                                if ($fName == $crmid) {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $record;
                                } elseif ($fName == 'productid') {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $value['productname'];
                                } else if ($fName == 'qty_per_unit' || $fName == 'unit_price' || $fName == 'weight'
                                    || $fName == 'commissionrate' || $fName == 'qtyinstock' || $fName == 'quantity'
                                    || $fName == 'listprice' || $fName == 'tax1' || $fName == 'tax2' || $fName == 'tax3'
                                    || $fName == 'discount_amount' || $fName == 'discount_percent'
                                    || in_array($fName, $currencyFieldsList) || in_array($fName, $currencyFieldsList2)
                                ) {
                                    //Format number
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = Vtiger_Currency_UIType::transformDisplayValue($value[$fName], null, true);
                                } else if ($fName == 'sequence_no') {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $value[$fName];
                                } else if (in_array($tModuleName, $productModules)) {
                                    $needValue = $value[$fName];
                                    if (is_numeric($needValue) && is_float($needValue)) {
                                        $needValue = Vtiger_Currency_UIType::transformDisplayValue($needValue, null, true);
                                    }

                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $needValue;
                                } else {
                                    $fieldModel = $moduleModel->getField($fName);
                                    // Check field on field table or table column
                                    if ($fieldModel) {
                                        $fieldDataType = $fieldModel->getFieldDataType();

                                        if ($fieldModel->get('table') == 'vtiger_inventoryproductrel') {
                                            // inventory table
                                            $needValue = $value[$fName];
                                            if (is_numeric($needValue) && is_float($needValue)) {
                                                $needValue = Vtiger_Currency_UIType::transformDisplayValue($needValue, null, true);
                                            } else if ($fieldDataType == 'text') {

                                            }

                                            $cloneTbodyRowTokens[$tModuleName][$fToken] = $needValue;
                                        } else {
                                            // Base table
                                            $cloneTbodyRowTokens[$tModuleName][$fToken] = $recordModel->getDisplayValue($fName, $recordModel->getId());
                                        }
                                    }
                                }
                            }
                        }

                        /** @var simple_html_dom_node $row */
                        foreach ($newBody as $row) {
                            // Row index
                            $row->setAttribute('data-row-number', $counter);

                            /** @var simple_html_dom_node $cell */
                            foreach ($row->children() as $cell) {
                                $style = ($even) ? $dataEvenStyle : $dataOddStyle;
                                $oldStyle = $cell->getAttribute('style');
                                $newStyle = null;

                                if (!$oldStyle) {
                                    $newStyle = $style;
                                } else {
                                    $oldStyle = trim($oldStyle);
                                    if (QuotingToolUtils::endsWith($oldStyle, ';')) {
                                        $newStyle = $oldStyle . ' ' . $style;
                                    } else {
                                        $newStyle = $oldStyle . '; ' . $style;
                                    }
                                }

                                $newStyle = trim($newStyle);

                                if ($newStyle !== '') {
                                    $cell->setAttribute('style', $newStyle);
                                }
                            }

                            $cloneTbodyRowsTemplate = $row->outertext;
                            // Update clone template
                            foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                                foreach ($tFields as $k => $f) {
                                    $f = nl2br($f);
                                    $cloneTbodyRowsTemplate = str_replace($k, $f, $cloneTbodyRowsTemplate);
                                }
                            }

                            $tmpBody .= $cloneTbodyRowsTemplate;
                        }
                    }
                }
            } else {
                foreach ($newBody as $row) {
                    $newTbodyRowsText = $row->outertext;

                    foreach ($newBodyTokens as $tModuleName => $tFields) {
                        foreach ($tFields as $k => $f) {
                            $needValue = $recordModel->getDisplayValue($f, $record);
                            $newTbodyRowsText = str_replace($k, $needValue, $newTbodyRowsText);
                        }
                    }

                    $tmpBody .= $newTbodyRowsText;
                }
            }

            if ($tbody !== null) {
                $tbody->innertext = $tmpBody;
                $innertext .= $tbody->outertext;
            } else {
                $innertext .= $tmpBody;
            }

            // Footer
            $tmpFoot = '';
            foreach ($newFooter as $row) {
                $newTfootRowsText = $row->outertext;

                foreach ($tokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = null;
                        if (in_array($moduleName, $inventoryModules) && isset($final_details[$f])) {
                            $needValue = Vtiger_Currency_UIType::transformDisplayValue($final_details[$f], null, true);
                            $needValue = nl2br($needValue);
                        } else if ($f == $crmid) {
                            $needValue = $record;
                        } else {
                            $needValue = $recordModel->getDisplayValue($f, $record);
                        }

                        $newTfootRowsText = str_replace($k, $needValue, $newTfootRowsText);
                    }
                }

                $tmpFoot .= $newTfootRowsText;
            }

            if ($tfoot !== null) {
                $tfoot->innertext = $tmpFoot;
//                $innertext .= $tfoot->outertext;
            } else if ($tbody !== null) {
//                $innertext .= '<tfoot>' . $tmpFoot . '</tfoot>';
            } else {
//                $innertext .= $tmpFoot;
            }

            $table->innertext = $innertext;

            // Check tfoot innertext
            $tfootInnertext = ($tfoot !== null) ? $tfoot->innertext : $tmpFoot;
            // Check has tbody
            $hasTbody = ($tbody !== null);

            // Remove tfoot tag and push to tbody content
            if ($hasTbody) {
                $newTable = clone $table;
                $newTable->innertext = $tfootInnertext;
                $table->outertext = $table->outertext . $newTable->outertext;
            }

            // Save DOM
            $content = $html->save();
        }

        return $content;
    }

    /**
     * @param string $content
     * @param array $attributes
     * @return string
     */
    public function cleanAttributes($content, $attributes)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        foreach ($attributes as $attribute) {

            /** @var simple_html_dom_node $tag */
            foreach ($html->find("[$attribute]") as $tag) {
                // Prevent Repeating Table Header
                $tag->setAttribute($attribute, 'cleaned');
            }
        }

        $content = $html->save();

        return $content;
    }

    //format number
    function numberFormat($number){
        if(is_numeric($number)){
            $userModel = Users_Record_Model::getCurrentUserModel();
            $currency_grouping_separator = $userModel->get('currency_grouping_separator');
            $currency_decimal_separator = $userModel->get('currency_decimal_separator');
            $no_of_decimal_places = getCurrencyDecimalPlaces();
            return number_format($number, $no_of_decimal_places,$currency_decimal_separator,$currency_grouping_separator);
        }else{
            return $number;
        }
    }

    /**
     * @param $tokens
     * @param $record
     * @param $content
     * @return mixed
     */
    public function mergeQuoterBlockTokens($tokens, $record, $content)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        $crmid = 'crmid';

        $blockStartTemplates = array(
            '#PRODUCTBLOC_START#',
            '#SERVICEBLOC_START#',
            '#PRODUCTSERVICEBLOC_START#'
        );
        $blockEndTemplates = array(
            '#PRODUCTBLOC_END#',
            '#SERVICEBLOC_END#',
            '#PRODUCTSERVICEBLOC_END#'
        );
        $blockTemplates = array_merge($blockStartTemplates, $blockEndTemplates);
        $dataTableType = null;

        /** @var simple_html_dom_node $table */
        foreach ($html->find('table') as $table) {
            $dataTableType = $table->attr['data-table-type'];

            if (!$dataTableType || $dataTableType != 'pricing_table_idc') {
                // Only parse pricing table
                continue;
            }
            $inventoryModules = getInventoryModules();
            if ($record == 0) {
                return $content;
            }
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            $moduleName = $recordModel->getModuleName();


            // Get data info settings
            $dataInfo = $table->getAttribute('data-info');

            if ($dataInfo) {
                $dataInfo = json_decode(html_entity_decode($table->getAttribute('data-info')), true);
            }

            // Clean un-necessary attributes
            $table->removeAttribute('data-info');

            $isTemplateStart = false;
            $isTemplateEnd = false;

            $newHeader = array();
            $newBody = array();
            $newFooter = array();

            /** @var simple_html_dom_node $thead */
            $thead = null;
            /** @var simple_html_dom_node $tbody */
            $tbody = null;
            /** @var simple_html_dom_node $tfoot */
            $tfoot = null;

            $newHeaderTokens = array();
            $newBodyTokens = array();
            $newFooterTokens = array();

            $dataOddStyle = $table->attr['data-odd-style'];
            $dataEvenStyle = $table->attr['data-even-style'];

            /** @var simple_html_dom_node $row */
            foreach ($table->find('tr') as $row) {
                $isNormalRow = true;

                /** @var simple_html_dom_node $cell */
                foreach ($row->children() as $cell) {
                    $cellText = trim($cell->plaintext);

                    if (!in_array($cellText, $blockTemplates)) {
                        // Normal cell
                        continue;
                    }

                    $isNormalRow = false;
                    $cell->parent->outertext = $cellText;

                    if (in_array($cellText, $blockStartTemplates)) {
                        // BlockStart cell
                        $isTemplateStart = true;
                        break;
                    } else if (in_array($cellText, $blockEndTemplates)) {
                        // BlockEnd cell
                        $isTemplateEnd = true;
                        break;
                    }
                }

                if ($isNormalRow) {
                    if (!$isTemplateStart) {
                        $newHeader[] = $row;
                        $newHeaderTokens = array_merge($newHeaderTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateStart && !$isTemplateEnd) {
                        $newBody[] = $row;
                        $newBodyTokens = array_replace_recursive($newBodyTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateEnd) {
                        $newFooter[] = $row;
                        $newFooterTokens = array_merge($newFooterTokens, $this->getFieldTokenFromString($row->outertext));
                    }
                }

                /** @var simple_html_dom_node $parent */
                $parent = $row->parent();

                if (($thead === null) && ($parent->tag == 'thead')) {
                    $thead = $parent;
                } else if (($tbody === null) && ($parent->tag == 'tbody')) {
                    $tbody = $parent;
                } else if (($tfoot === null) && ($parent->tag == 'tfoot')) {
                    $tfoot = $parent;
                }
            }

            $innertext = '';

            // Header
            $tmpHead = '';
            foreach ($newHeader as $row) {
                $newTheadRowsText = $row->outertext;

                foreach ($newHeaderTokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = $recordModel->getDisplayValue($f, $record);
                        $newTheadRowsText = str_replace($k, $needValue, $newTheadRowsText);
                    }
                }

                $tmpHead .= $newTheadRowsText;
            }

            if ($thead !== null) {
                $thead->innertext = $tmpHead;
                $innertext .= $thead->outertext;
            } else if ($tbody !== null) {
                $innertext .= '<thead>' . $tmpHead . '</thead>';
            } else {
                $innertext .= $tmpHead;
            }

            // Body
            $tmpBody = '';
            if ($tbody !== null) {
                $dataOddStyle = $tbody->attr['data-odd-style'];
                $dataEvenStyle = $tbody->attr['data-even-style'];
            }

            $dataOddStyle = ($dataOddStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataOddStyle))) : '';
            $dataEvenStyle = ($dataEvenStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataEvenStyle))) : '';
            $final_details = array();
            $quoterModel = new Quoter_Module_Model();
            $quoterSettings = $quoterModel->getSettingForModule($moduleName);
            $quoterCustomSettings = array();
            foreach($quoterSettings as $key => $val){
                if($quoterModel->isCustomFields($val->columnName)){
                    $quoterCustomSettings[$key] = $val->columnName;
                }
            }
            $totalSettings = $quoterModel->getTotalFieldsSetting($moduleName);
            $quoterRecordModel =  new Quoter_Record_Model();

            if (in_array($moduleName, $inventoryModules)) {
                $products = $quoterRecordModel->getProducts($moduleName,$record,$quoterSettings);

                if ($products && count($products) > 0) {
                    $final_details = $totalValues = $quoterRecordModel->getTotalValues($moduleName,array_keys($totalSettings),$record);
                    $counter = 0;

                    foreach ($products as $k => $value) {
                        $even = (++$counter % 2) == 0;
                        $cloneTbodyRowTokens = $newBodyTokens;

                        // Update token value to clone token
                        foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                            foreach ($tFields as $fToken => $fName) {
                                $fkName = $fName . $k;

                                if ($fName == $crmid) {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $record;
                                } elseif ($fName == 'productid' || $fName == 'related_to') {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $value['item_name' . $k];
                                } else {
                                    $needValue = (in_array($fName, $quoterCustomSettings)) ? $value[$fkName]->get('fieldvalue') : $value[$fkName];
                                    if(is_numeric($needValue) && $fName != 'quantity'){
                                        $needValue = $this->numberFormat($needValue);
                                    }
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $needValue;
                                }
                            }
                        }

                        /** @var simple_html_dom_node $row */
                        foreach ($newBody as $row) {
                            $maxCol = 0;
                            // Row index
                            $row->setAttribute('data-row-number', $counter);

                            /** @var simple_html_dom_node $cell */
                            foreach ($row->children() as $cell) {
                                $maxCol ++;
                                $style = ($even) ? $dataEvenStyle : $dataOddStyle;
                                $style = '';    // Clear IDC odd and even style
                                $oldStyle = $cell->getAttribute('style');
                                $newStyle = null;

                                if (!$oldStyle) {
                                    $newStyle = $style;
                                } else {
                                    $oldStyle = trim($oldStyle);
                                    if (QuotingToolUtils::endsWith($oldStyle, ';')) {
                                        $newStyle = $oldStyle . ' ' . $style;
                                    } else {
                                        $newStyle = $oldStyle . '; ' . $style;
                                    }
                                }

                                $newStyle = trim($newStyle);

                                if ($newStyle !== '') {
                                    $cell->setAttribute('style', $newStyle);
                                }
                            }

                            $cloneTbodyRowsTemplate = $row->outertext;
                            // Update clone template
                            foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                                foreach ($tFields as $kReplace => $fNameReplace) {
                                    $fNameReplace = nl2br($fNameReplace);
                                    $cloneTbodyRowsTemplate = str_replace($kReplace, $fNameReplace, $cloneTbodyRowsTemplate);
                                }
                            }

                            $cellStyle = '';
                            if ($dataInfo['settings']['theme']['settings']['cell']['style']) {
                                $cellStyles = $dataInfo['settings']['theme']['settings']['cell']['style'];

                                foreach ($cellStyles as $attrName => $attrVal) {
                                    $cellStyle .= $attrName . ':' . $attrVal . ';';
                                }
                            }

                            $theadStyle = '';
                            if ($dataInfo['settings']['theme']['settings']['thead']['style']) {
                                $theadStyles = $dataInfo['settings']['theme']['settings']['thead']['style'];

                                foreach ($theadStyles as $attrName => $attrVal) {
                                    $theadStyle .= $attrName . ':' . $attrVal . ';';
                                }
                            }

                            // Include Sections
                            if ($dataInfo['settings']['include_sections'] && !empty($value['section' . $k])) {
                                $tmpBody .= '<tr class="section">
                                    <td colspan="' . $maxCol . '" style=" ' . $dataOddStyle . $cellStyle . ' ">
                                        <span style="font-weight: bold;">' . $value['section' . $k] . '</span>
                                    </td>
                                </tr>';
                            }

                            $tmpBody .= $cloneTbodyRowsTemplate;

                            // Include Running Totals
                            if ($dataInfo['settings']['include_running_totals'] && !empty($value['running_item_value' . $k])) {
                                foreach ($value['running_item_value' . $k] as $runningItemName => $runningItem) {
                                    $tmpBody .= '<tr class="running_item">
                                        <td colspan="' . $maxCol . '" style="text-align: right; ' . $cellStyle . '">
                                            <span style="font-weight: bold;">Running '
                                        . vtranslate($totalSettings[$runningItemName]['fieldLabel'], 'Quoter')
                                        . ': ' . $runningItem . '</span>
                                        </td>
                                    </tr>';
                                }
                            }
                        }
                    }
                }
            } else {
                foreach ($newBody as $row) {
                    $newTbodyRowsText = $row->outertext;

                    foreach ($newBodyTokens as $tModuleName => $tFields) {
                        foreach ($tFields as $k => $f) {
                            $needValue = $recordModel->getDisplayValue($f, $record);
                            $newTbodyRowsText = str_replace($k, $needValue, $newTbodyRowsText);
                        }
                    }

                    $tmpBody .= $newTbodyRowsText;
                }
            }

            if ($tbody !== null) {
                $tbody->innertext = $tmpBody;
                $innertext .= $tbody->outertext;
            } else {
                $innertext .= $tmpBody;
            }

            // Footer
            $tmpFoot = '';
            foreach ($newFooter as $row) {
                $newTfootRowsText = $row->outertext;

                foreach ($tokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = null;
                        if (in_array($moduleName, $inventoryModules) && isset($final_details[$f])) {
                            $needValue = Vtiger_Currency_UIType::transformDisplayValue($final_details[$f], null, true);
                            $needValue = nl2br($needValue);
                        } else if ($f == $crmid) {
                            $needValue = $record;
                        } else {
                            $needValue = $recordModel->getDisplayValue($f, $record);
                        }

                        $newTfootRowsText = str_replace($k, $needValue, $newTfootRowsText);
                    }
                }

                $tmpFoot .= $newTfootRowsText;
            }

            if ($tfoot !== null) {
                $tfoot->innertext = $tmpFoot;
//                $innertext .= $tfoot->outertext;
            } else if ($tbody !== null) {
//                $innertext .= '<tfoot>' . $tmpFoot . '</tfoot>';
            } else {
//                $innertext .= $tmpFoot;
            }

            $table->innertext = $innertext;

            // Check tfoot innertext
            $tfootInnertext = ($tfoot !== null) ? $tfoot->innertext : $tmpFoot;
            // Check has tbody
            $hasTbody = ($tbody !== null);

            // Remove tfoot tag and push to tbody content
            if ($hasTbody) {
                $newTable = clone $table;
                $newTable->innertext = $tfootInnertext;
                $table->outertext = $table->outertext . $newTable->outertext;
            }

            // Save DOM
            $content = $html->save();
        }

        return $content;
    }

    public function mergeLinkModulesTokens($tokens, $record, $content)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        $crmid = 'crmid';

        $pdfContentModel = new QuotingTool_PDFContent_Model();

        $blockStartTemplates = array(
            '#RELATEDBLOCK_START#',
        );
        $blockEndTemplates = array(
            '#RELATEDBLOCK_END#',
        );
        $blockTemplates = array_merge($blockStartTemplates, $blockEndTemplates);
        $dataTableType = null;

        /** @var simple_html_dom_node $table */
        foreach ($html->find('table') as $table) {
            $dataTableType = $table->attr['data-table-type'];

            if (!$dataTableType || $dataTableType != 'related_module') {
                // Only parse pricing table
                continue;
            }
            if ($record == 0) {
                return $content;
            }
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            $moduleName = $recordModel->getModuleName();

            // Clean un-necessary attributes
            $table->removeAttribute('data-info');

            $isTemplateStart = false;
            $isTemplateEnd = false;

            $newHeader = array();
            $newBody = array();
            $newFooter = array();

            /** @var simple_html_dom_node $thead */
            $thead = null;
            /** @var simple_html_dom_node $tbody */
            $tbody = null;
            /** @var simple_html_dom_node $tfoot */
            $tfoot = null;

            $newHeaderTokens = array();
            $newBodyTokens = array();
            $newFooterTokens = array();

            $dataOddStyle = $table->attr['data-odd-style'];
            $dataEvenStyle = $table->attr['data-even-style'];

            /** @var simple_html_dom_node $row */
            foreach ($table->find('tr') as $row) {
                $isNormalRow = true;

                /** @var simple_html_dom_node $cell */
                foreach ($row->children() as $cell) {
                    $cellText = trim($cell->plaintext);

                    if (!in_array($cellText, $blockTemplates)) {
                        // Normal cell
                        continue;
                    }

                    $isNormalRow = false;
                    $cell->parent->outertext = $cellText;

                    if (in_array($cellText, $blockStartTemplates)) {
                        // BlockStart cell
                        $isTemplateStart = true;
                        break;
                    } else if (in_array($cellText, $blockEndTemplates)) {
                        // BlockEnd cell
                        $isTemplateEnd = true;
                        break;
                    }
                }

                if ($isNormalRow) {
                    if (!$isTemplateStart) {
                        $newHeader[] = $row;
                        $newHeaderTokens = array_replace_recursive($newHeaderTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateStart && !$isTemplateEnd) {
                        $newBody[] = $row;
                        $newBodyTokens = array_replace_recursive($newBodyTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateEnd) {
                        $newFooter[] = $row;
                        $newFooterTokens = array_replace_recursive($newFooterTokens, $this->getFieldTokenFromString($row->outertext));
                    }
                }

                /** @var simple_html_dom_node $parent */
                $parent = $row->parent();

                if (($thead === null) && ($parent->tag == 'thead')) {
                    $thead = $parent;
                } else if (($tbody === null) && ($parent->tag == 'tbody')) {
                    $tbody = $parent;
                } else if (($tfoot === null) && ($parent->tag == 'tfoot')) {
                    $tfoot = $parent;
                }
            }

            $innertext = '';

            // Header
            $tmpHead = '';
            foreach ($newHeader as $row) {
                $newTheadRowsText = $row->outertext;

                foreach ($newHeaderTokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = $recordModel->getDisplayValue($f, $record);
                        $newTheadRowsText = str_replace($k, $needValue, $newTheadRowsText);
                    }
                }

                $tmpHead .= $newTheadRowsText;
            }

            if ($thead !== null) {
                $thead->innertext = $tmpHead;
                $innertext .= $thead->outertext;
            } else if ($tbody !== null) {
                $innertext .= '<thead>' . $tmpHead . '</thead>';
            } else {
                $innertext .= $tmpHead;
            }

            // Body
            $tmpBody = '';
            if ($tbody !== null) {
                $dataOddStyle = $tbody->attr['data-odd-style'];
                $dataEvenStyle = $tbody->attr['data-even-style'];
            }

            $dataOddStyle = ($dataOddStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataOddStyle))) : '';
            $dataEvenStyle = ($dataEvenStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataEvenStyle))) : '';





            /** @var Inventory_Record_Model $recordModel */
            $recordModel = Inventory_Record_Model::getInstanceById($record, $moduleName);
            // Get products - to get final detail only
//            $products = $recordModel->getProducts();
            $relatedModuleName = '';
            foreach ($newBodyTokens as $tModuleName => $tFields) {
                $relatedModuleName = $tModuleName;

            }
            if (!Vtiger_Module::getInstance($relatedModuleName)) {
                continue;
            }
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', 1);
            $products = $relationListView->getEntries($pagingModel);

            // $products = $models;
            // Reference fields
            $referenceFields = array();


            if ($products && count($products) > 0) {

                $counter = 0;
                foreach ($products as $k => $value) {
                    if (!$k) {
                        // Validate recordId
                        continue;
                    }
                    $even = (++$counter % 2) == 0;

                    $cloneTbodyRowTokens = $newBodyTokens;
                    $linkModuleRecordModel = Vtiger_Record_Model::getInstanceById($k);
                    // Update token value to clone token
                    foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                        $moduleModel = Vtiger_Module_Model::getInstance($tModuleName);
                        foreach ($tFields as $fToken => $fName) {
                            // Hardcode fieldname - by vtiger core :(
                            if ($fName == $crmid) {
                                $cloneTbodyRowTokens[$tModuleName][$fToken] = $k;
                            } else {
                                $fieldModel = $moduleModel->getField($fName);
                                // Check field on field table or table column
                                if ($fieldModel) {
                                    // Base table
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $linkModuleRecordModel->getDisplayValue($fName, $k);
                                    $fieldDataType = $fieldModel->getFieldDataType();
                                    // For special types - prevent nl2br html code uitype = 10 will return <a> tag
                                    if ($fieldDataType == 'reference' || in_array($fieldDataType, array('owner'))) {
                                        $cloneTbodyRowTokens[$tModuleName][$fToken] = $this->getTextFromHtmlTag($cloneTbodyRowTokens[$tModuleName][$fToken], 'a');
                                    }
                                }
                            }
                        }
                    }






                    /** @var simple_html_dom_node $row */
                    foreach ($newBody as $row) {
                        // Row index
                        $row->setAttribute('data-row-number', $counter);

                        /** @var simple_html_dom_node $cell */
                        foreach ($row->children() as $cell) {
                            $style = ($even) ? $dataEvenStyle : $dataOddStyle;
                            $oldStyle = $cell->getAttribute('style');
                            $newStyle = null;

                            if (!$oldStyle) {
                                $newStyle = $style;
                            } else {
                                $oldStyle = trim($oldStyle);
                                if (QuotingToolUtils::endsWith($oldStyle, ';')) {
                                    $newStyle = $oldStyle . ' ' . $style;
                                } else {
                                    $newStyle = $oldStyle . '; ' . $style;
                                }
                            }

                            $newStyle = trim($newStyle);

                            if ($newStyle !== '') {
                                $cell->setAttribute('style', $newStyle);
                            }
                        }

                        $cloneTbodyRowsTemplate = $row->outertext;
                        // Update clone template
                        foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                            foreach ($tFields as $k => $f) {
                                $f = nl2br($f);
                                $cloneTbodyRowsTemplate = str_replace($k, $f, $cloneTbodyRowsTemplate);
                            }
                        }

                        $tmpBody .= $cloneTbodyRowsTemplate;
                    }
                }
            }

            if ($tbody !== null) {
                $tbody->innertext = $tmpBody;
                $innertext .= $tbody->outertext;
            } else {
                $innertext .= $tmpBody;
            }

            $table->innertext = $innertext;
            $content = $html->save();
        }

        return $content;
    }
    public function getTextFromHtmlTag($content, $tagName)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        $text = $content;

        foreach ($html->find($tagName) as $element) {
            $text = $element->plaintext;
        }

        return $text;
    }

    /**
     * @param $tokens
     * @param $record
     * @param $content
     * @param string $module
     * @return mixed
     */
    public function mergeTokens($tokens, $record, $content, $module = 'Vtiger')
    {
        $supportedModulesList = Settings_LayoutEditor_Module_Model::getSupportedModules();
        $supportedModulesList = array_flip($supportedModulesList);
        ksort($supportedModulesList);
        $crmid = 'crmid';
        // TODO: hardcode
        $ignore = array('modifiedby', 'created_user_id');
        $export = array();

        $moduleModel = Vtiger_Module_Model::getInstance($module);
        if($record == 0 || !isRecordExists($record)){
            foreach ($tokens as $tModuleName => $tFields) {
                // Invalid module
                if (!in_array($tModuleName, $supportedModulesList) && !in_array($tModuleName, $this->specialModules)) {
                    continue;
                }
                // Reference fields
                $referenceFields = $moduleModel->getFieldsByType('reference');

                // If Primary module
                if ($tModuleName == $module) {
                    foreach ($tFields as $fToken => $fName) {
                        if ($fName == $crmid) {
                            $tokens[$tModuleName][$fToken] = '';
                        } else if (!in_array($fName, $ignore) && array_key_exists($fName, $referenceFields)) {
                            // Prepare reference record model
                            // Merge later
                                $tokens[$tModuleName][$fToken] = '';
                                continue;
                        } else {
                            $fieldModel = $moduleModel->getField($fName);
                            if (!$fieldModel) {
                                // Invalid field model
                                unset($tokens[$tModuleName][$fToken]);
                                continue;
                            }

                            $fieldDataType = $fieldModel->getFieldDataType();
                            $needValue = '';

                            if (in_array($fieldDataType, array('url','email', 'documentsFolder', 'fileLocationType', 'documentsFileUpload'))) {
                                $needValue = '';
                            }

                            $tokens[$tModuleName][$fToken] = $needValue;
                        }
                    }

                    $export[] = $tModuleName;
                }

                // If is Users module (special module)
                if ($tModuleName == 'Users') {
                    // Reference fields
                    $userModuleModel = Vtiger_Module_Model::getInstance($tModuleName);
                    $referenceFields = $userModuleModel->getFieldsByType('reference');
                    $assignedToId = '';

                    if (QuotingToolUtils::isUserExists($assignedToId)) {
                        $userRecordModel = Vtiger_Record_Model::getInstanceById($assignedToId, $tModuleName);

                        foreach ($tFields as $fToken => $fName) {
                            if ($fName == $crmid) {
                                $tokens[$tModuleName][$fToken] = $userRecordModel->getId();
                            } else if ($fName == 'roleid') {
                                $tokens[$tModuleName][$fToken] = getRoleName($userRecordModel->get('roleid'));
                            } else if (!in_array($fName, $ignore) && array_key_exists($fName, $referenceFields)) {
                                if (!$userRecordModel->get($fName)) {
                                    $tokens[$tModuleName][$fToken] = '';
                                    continue;
                                }

                                $relatedRecordModel = Vtiger_Record_Model::getInstanceById($userRecordModel->get($fName));
                                $tokens[$tModuleName][$fToken] = $relatedRecordModel ? $relatedRecordModel->getName() : '';
                            } else {
                                $fieldModel = $userRecordModel->getField($fName);
                                if (!$fieldModel) {
                                    // Invalid field model
                                    unset($tokens[$tModuleName][$fToken]);
                                    continue;
                                }

                                $fieldDataType = $fieldModel->getFieldDataType();
                                $needValue = $userRecordModel->getDisplayValue($fName, $userModuleModel->getId());

                                if (in_array($fieldDataType, array('email', 'documentsFolder', 'fileLocationType', 'documentsFileUpload'))) {
                                    $needValue = $userRecordModel->get($fName);
                                }

                                $tokens[$tModuleName][$fToken] = $needValue;
                            }
                        }
                    }

                    $export[] = $tModuleName;
                }
            }
        }else{
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $module);

            if (!$recordModel) {
                // Return if invalid record model
                return $content;
            }
            // Parse data
            foreach ($tokens as $tModuleName => $tFields) {
                // Invalid module
                if (!in_array($tModuleName, $supportedModulesList) && !in_array($tModuleName, $this->specialModules)) {
                    continue;
                }

                // Reference fields
                $referenceFields = $moduleModel->getFieldsByType('reference');

                // If Primary module
                if ($tModuleName == $module) {
                    foreach ($tFields as $fToken => $fName) {
                        if ($fName == $crmid) {
                            $tokens[$tModuleName][$fToken] = $recordModel->getId();
                        } else if (!in_array($fName, $ignore) && array_key_exists($fName, $referenceFields)) {
                            // Prepare reference record model
                            // Merge later
                            if (!$recordModel->get($fName)) {
                                $tokens[$tModuleName][$fToken] = '';
                                continue;
                            }

                            $relatedRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get($fName));
                            $tokens[$tModuleName][$fToken] = $relatedRecordModel ? $relatedRecordModel->getName() : '';
                        } else {
                            $fieldModel = $moduleModel->getField($fName);
                            if (!$fieldModel) {
                                // Invalid field model
                                unset($tokens[$tModuleName][$fToken]);
                                continue;
                            }

                            $fieldDataType = $fieldModel->getFieldDataType();
                            $needValue = $recordModel->getDisplayValue($fName, $recordModel->getId());

                            if (in_array($fieldDataType, array('url','email', 'documentsFolder', 'fileLocationType', 'documentsFileUpload'))) {
                                $needValue = $recordModel->get($fName);
                            }

                            $tokens[$tModuleName][$fToken] = $needValue;
                        }
                    }

                    $export[] = $tModuleName;
                }

                // If is Users module (special module)
                if ($tModuleName == 'Users') {
                    // Reference fields
                    $userModuleModel = Vtiger_Module_Model::getInstance($tModuleName);
                    $referenceFields = $userModuleModel->getFieldsByType('reference');
                    $assignedToId = $recordModel->get('assigned_user_id');

                    if (QuotingToolUtils::isUserExists($assignedToId)) {
                        $userRecordModel = Vtiger_Record_Model::getInstanceById($assignedToId, $tModuleName);

                        foreach ($tFields as $fToken => $fName) {
                            if ($fName == $crmid) {
                                $tokens[$tModuleName][$fToken] = $userRecordModel->getId();
                            } else if ($fName == 'roleid') {
                                $tokens[$tModuleName][$fToken] = getRoleName($userRecordModel->get('roleid'));
                            } else if (!in_array($fName, $ignore) && array_key_exists($fName, $referenceFields)) {
                                if (!$userRecordModel->get($fName)) {
                                    $tokens[$tModuleName][$fToken] = '';
                                    continue;
                                }

                                $relatedRecordModel = Vtiger_Record_Model::getInstanceById($userRecordModel->get($fName));
                                $tokens[$tModuleName][$fToken] = $relatedRecordModel ? $relatedRecordModel->getName() : '';
                            } else {
                                $fieldModel = $userRecordModel->getField($fName);
                                if (!$fieldModel) {
                                    // Invalid field model
                                    unset($tokens[$tModuleName][$fToken]);
                                    continue;
                                }

                                $fieldDataType = $fieldModel->getFieldDataType();
                                $needValue = $userRecordModel->getDisplayValue($fName, $userModuleModel->getId());

                                if (in_array($fieldDataType, array('email', 'documentsFolder', 'fileLocationType', 'documentsFileUpload'))) {
                                    $needValue = $userRecordModel->get($fName);
                                }

                                $tokens[$tModuleName][$fToken] = $needValue;
                            }
                        }
                    }

                    $export[] = $tModuleName;
                }

                // For reference record model
                /**
                 * @var string $fieldName
                 * @var Vtiger_Field_Model $fieldModel
                 */
                foreach ($referenceFields as $fieldName => $fieldModel) {
                    $relatedFieldValue = $recordModel->get($fieldName);

                    if (in_array($fieldName, $ignore) || !$relatedFieldValue
                        /* Validate all related records */
                        || !QuotingToolUtils::isRecordExists($recordModel->get($fieldName))
                    ) {
                        $referenceList = $fieldModel->getReferenceList();
                        foreach ($referenceList as $ref) {
                            // Unset empty field value
                            if (!isset($tokens[$ref]) || !$tokens[$ref] || in_array($ref, $export)) {
                                continue;
                            }

                            $relatedFields = $tokens[$ref];

                            foreach ($relatedFields as $fToken => $fName) {
                                $tokens[$ref][$fToken] = '';
                            }
                        }

                        continue;
                    }

                    $relatedRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get($fieldName));
                    $relatedModuleName = $relatedRecordModel->getModuleName();
                    if (in_array($relatedModuleName, $export)) {
                        continue;
                    }

                    $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName)->getFieldsByType('reference');

                    if (!array_key_exists($relatedModuleName, $tokens)) {
                        // Invalid related module name
                        continue;
                    }

                    $relatedFields = $tokens[$relatedModuleName];

                    foreach ($relatedFields as $fToken => $fName) {
                        $relatedFieldModel = $relatedRecordModel->getField($fName);

                        if (!$relatedFieldModel) {
                            unset($tokens[$relatedModuleName][$fToken]);
                            continue;
                        }

                        if (array_key_exists($fName, $relatedModuleModel) && $relatedRecordModel->get($fName)) {
                            $refRelatedRecordModel = Vtiger_Record_Model::getInstanceById($relatedRecordModel->get($fName));
                            $tokens[$relatedModuleName][$fToken] = $refRelatedRecordModel->getDisplayName();

                            continue;
                        }

                        $tokens[$relatedModuleName][$fToken] = html_entity_decode($relatedRecordModel->get($fName));
                    }

                    $export[] = $relatedModuleName;
                }
            }
        }

        // Merge data
        foreach ($tokens as $tModuleName => $tFields) {
            // Invalid module
            if (!in_array($tModuleName, $supportedModulesList) && !in_array($tModuleName, $this->specialModules)) {
                continue;
            }

            foreach ($tFields as $k => $f) {
                $f = nl2br($f);
                $content = str_replace($k, $f, $content);
            }
        }

        return $content;
    }

    /**
     * Fn - runCustomFunctions
     *
     * @param string $content
     * @return string
     */
    public function mergeCustomFunctions($content)
    {
        if (is_numeric(strpos($content, '[CUSTOMFUNCTION|'))) {
            include_once 'include/simplehtmldom/simple_html_dom.php';
            foreach (glob('modules/QuotingTool/resources/functions/*.php') as $cfFile) {
                include_once $cfFile;
            }

            $data = array();
            $data['[CUSTOMFUNCTION|'] = '<customfunction>';
            $data['|CUSTOMFUNCTION]'] = '</customfunction>';
            $content = $this->mergeBodyHtml($content, $data);
            $domBodyHtml = str_get_html($content);

            if (is_array($domBodyHtml->find('customfunction'))) {
                foreach ($domBodyHtml->find('customfunction') as $element) {
                    $params = $this->splitParametersFromText(trim($element->plaintext));
                    $function_name = $params[0];
                    unset($params[0]);
                    $result = call_user_func_array($function_name, $params);
                    $result = nl2br($result);
                    $element->outertext = $result;
                }

                $content = $domBodyHtml->save();
            }
        }

        return $content;
    }

    /**
     * Fn - mergeBodyHtml
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    private function mergeBodyHtml($content, $data)
    {
        if (!empty($data)) {
            $content = str_replace(array_keys($data), $data, $content);
            return $content;
        }

        return null;
    }

    /**
     * @param $content
     * @param $keys_values - Example: array('$custom_proposal_link$' => 'modules/QuotingTool/proposal/index.php?record=1')
     * @return string
     */
    public function mergeCustomTokens($content, $keys_values)
    {
        foreach ($keys_values as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }

    /**
     * @param $content
     * @param $keys_values - Example: array('$custom_proposal_link$' => 'modules/QuotingTool/proposal/index.php?record=1')
     * @return string
     */
    public function mergeEscapeCharacters($content, $keys_values)
    {
        foreach ($keys_values as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }

    /**
     * Fn - splitParametersFromText
     *
     * @param string $text
     * @return array
     */
    private function splitParametersFromText($text)
    {
        $params = array();
        $end = false;

        do {
            if (strstr($text, '|')) {
                if ($text[0] == '"') {
                    $delimiter = '"|';
                    $text = substr($text, 1);
                } elseif (substr($text, 0, 6) == '&quot;') {
                    $delimiter = '&quot;|';
                    $text = substr($text, 6);
                } else {
                    $delimiter = '|';
                }
                list($params[], $text) = explode($delimiter, $text, 2);
            } else {
                $params[] = $text;
                $end = true;
            }
        } while (!$end);

        return $params;
    }

    /**
     * @param string $moduleName
     * @return array
     */
    public function getOtherFields($moduleName)
    {
        $blocks = array();

        // Init by common field block
        $blocks[] = array(  // Block item
            'id' => 0,  // Option
            'name' => 'LBL_COMMON_FIELDS',  // Required
            'label' => vtranslate('LBL_COMMON_FIELDS', self::MODULE_NAME),  // Option
            'fields' => array(  // Fields - Required
                array(  // Field item
                    'id' => 0,  // Option
                    'name' => 'crmid',  // Required
                    'label' => vtranslate('crmid', self::MODULE_NAME),  // Option
                    'token' => $this->convertFieldToken('crmid', $moduleName),
                    'datatype' => 'integer'
                )
            ),
        );

        return $this->fillBlockFields($moduleName, $blocks);
    }

    /**
     * @param string $moduleName
     * @return array
     */
    public function getItemDetailsFields($moduleName)
    {
        $blocks = array();

        // Product detail block
        $blocks[] = array(
            'name' => 'LBL_ITEM_DETAILS',
            'fields' => array(
                array(
                    'name' => 'sequence_no',
                    'datatype' => 'integer'
                ),
                array(
                    'name' => 'totalAfterDiscount',
                    'datatype' => 'currency'
                ),
                array(
                    'name' => 'netPrice',
                    'datatype' => 'currency'
                ),
                array(
                    'name' => 'unitPrice',
                    'datatype' => 'currency'
                )
            )
        );

        return $this->fillBlockFields($moduleName, $blocks);
    }

    /**
     * @param Vtiger_Module_Model $moduleModel
     * @param array $excludeBlocks
     * @return array
     * @throws Exception
     */
    public function parseModule($moduleModel, $excludeBlocks = array())
    {
        $moduleId = $moduleModel->getId();
        $moduleName = $moduleModel->getName();
        $moduleFields = $moduleModel->getFields();

        $moduleInfo = array();
        $moduleInfo['id'] = $moduleId;
        $moduleInfo['name'] = $moduleName;
        $moduleInfo['label'] = vtranslate($moduleModel->get('label'), $moduleName);
        $moduleInfo['fields'] = array();

        $moduleInfo['fields'] = $this->getOtherFields($moduleName);
        /** @var Vtiger_Field_Model $moduleField */
        foreach ($moduleFields as $moduleField) {
            // Ignore special fields and hide fields
            if($moduleField->get('presence') == 1 || in_array($moduleField->getName(), $this->ignoreSpecialFields)){
                continue;
            }

            $fieldInfo = array();
            $fieldInfo['id'] = $moduleField->getId();
            $fieldInfo['uitype'] = $moduleField->get('uitype');
            $fieldInfo['datatype'] = $moduleField->getFieldDataType();
            $fieldInfo['name'] = $moduleField->getName();
            $fieldInfo['label'] = vtranslate($moduleField->get('label'), $moduleName);
            $fieldInfo['token'] = $this->convertFieldToken($moduleField->getName(), $moduleName);
            /** @var Vtiger_Block_Model $block */
            $block = $moduleField->get('block');
            $fieldInfo['block'] = array(
                'id' => $block->id,
                'name' => $block->label,
                'label' => vtranslate($block->label, $moduleName)
            );

            // Flag
            $ignore = false;
            // Get inject fields from config
            $injectFields = $this->injectFields;

            if (isset($injectFields[$moduleName])) {
                $ignoreBlocks = $injectFields[$moduleName];

                foreach ($ignoreBlocks as $ignoreBlock => $ignoreFields) {
                    if ($block->label != $ignoreBlock) {
                        // Not match block
                        continue;
                    }

                    foreach ($ignoreFields as $ignoreField) {
                        if ($ignoreField == '*' || $ignoreField == $fieldInfo['name']) {
                            $ignore = true;
                            /**
                             * Break multi loops
                             * @link http://php.net/manual/en/control-structures.break.php
                             */
                            break 2;
                        }
                    }
                }
            }

            if (!$ignore) {
                $moduleInfo['fields'][] = $fieldInfo;
            }
        }

        // Item Details Fields
        if (in_array($moduleName, $this->inventoryModules)) {
            $moduleInfo['fields'] = array_merge($moduleInfo['fields'], $this->getItemDetailsFields($moduleName));
        }

        // When exclude blocks
        if ($excludeBlocks && count($excludeBlocks) > 0) {
            $tmpFields = array();

            foreach ($moduleInfo['fields'] as $f => $fieldInfo) {
                // Flag
                $ignore = false;

                foreach ($excludeBlocks as $ignoreBlock => $ignoreFields) {
                    if ($fieldInfo['block']['name'] != $ignoreBlock) {
                        // Not match block
                        continue;
                    }

                    foreach ($ignoreFields as $ignoreField) {
                        if ($ignoreField == '*' || $ignoreField == $fieldInfo['name']) {
                            $ignore = true;
                            /**
                             * Break multi loops
                             * @link http://php.net/manual/en/control-structures.break.php
                             */
                            break 2;
                        }
                    }
                }

                if (!$ignore) {
                    $tmpFields[] = $moduleInfo['fields'][$f];
                }
            }

            $moduleInfo['fields'] = $tmpFields;
        }

        return $moduleInfo;
    }

    /**
     * @param Vtiger_Module_Model $currentModuleModel
     * @return array
     */
    public function getRelatedModules($currentModuleModel)
    {
        $relatedModules = array();
        $referenceFields = $currentModuleModel->getFieldsByType('reference');
        /** @var Vtiger_Field_Model $fieldModel */
        foreach ($referenceFields as $fieldModel) {
            $referenceModules = $fieldModel->getReferenceList();
            if (count($referenceModules) == 2 && $referenceModules[0] == 'Campaigns') {
                // Fix when conflict between Users & Campaigns modules
                unset($referenceModules[0]);
            }

            // Check by keys
            $relatedModuleKeys = array_keys($relatedModules);

            foreach ($referenceModules as $k => $relatedModule) {
                if (!in_array($relatedModule, $relatedModuleKeys)) {
                    $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
                    $relatedModules[$referenceModules[$k]] = $relatedModuleModel;
                }
            }
        }

        return $relatedModules;
    }

    /**
     * @param string $content
     * @param string $header
     * @param string $footer
     * @param string $name
     * @param string $path
     * @param array $styles
     * @param array $scripts
     * @param bool $escapeForm
     * @return string - path file
     */
    public function createPdf($content, $header = '', $footer = '', $name, $path = 'storage/QuotingTool/', $styles = array(),
                              $scripts = array(), $escapeForm = true)
    {
        global $site_URL;

        // Check dir
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, true))
                return '';
        }

        require_once('modules/QuotingTool/resources/mpdf/mpdf.php');
        include_once 'include/simplehtmldom/simple_html_dom.php';

        // Process if escape form
        if ($escapeForm) {
            // Replace <input> to <span>
            $contentDom = str_get_html($content);
            // with input type
            // @link http://www.w3schools.com/tags/tag_input.asp
            $inputs = $contentDom->find('input');

            if (is_array($inputs)) {
                foreach ($inputs as $k => $input) {
                    $value = $input->value;
                    $class = $input->class;
                    $style = $input->style;
                    $type = $input->type;

                    if ($type == 'text') {
                        $replaceBy = '<div class="' . $class . ' uneditable-input" style="' . $style . '">' . $value . '</div>';
                        $inputs[$k]->outertext = $replaceBy;
                    } else if ($type == 'checkbox') {
                        $inputs[$k]->disabled = 'disabled';
                    }
                }
            }

            $content = $contentDom->save();
        }

        $content = '<div id="quoting_tool-body">' . $content . '</div>';
        // Fix generate image from server
        $site = rtrim($site_URL, '/');
		$content = preg_replace('/\/\/test\//', '/test/', $content);
        $content = str_replace($site . '/test/upload/images/', 'test/upload/images/', $content);		
        $content = str_replace($site . '/modules/QuotingTool/resources/images/', 'modules/QuotingTool/resources/images/', $content);

        $mpdf = new mPDF();
        $mpdf->useActiveForms = true;
		//$mpdf->showImageErrors = true;
        // mpdf styles
        if (!$styles) {
            $styles = array();
        }
        $styles = array_merge($styles, array(
            'modules/QuotingTool/resources/styles.css',
            'modules/QuotingTool/resources/pdf.css'
        ));

        foreach ($styles as $css) {
            $stylesheet = file_get_contents($css);
            $mpdf->WriteHTML($stylesheet, 1);
        }

        // mpdf scripts
        if (!$scripts) {
            $scripts = array();
        }

        foreach ($scripts as $js) {
            $cScript = file_get_contents($js);
            $mpdf->WriteHTML($cScript, 1);
        }

        $mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($content);
        // put file for debug
//        file_put_contents('modules/QuotingTool/PDFcontent.html', $content);

        $fullFileName = $path . $name;
        $mpdf->Output($fullFileName, 'F');

        return $fullFileName;
    }

    /**
     * @param $content
     * @param $module
     * @param $record
     * @return mixed|string
     */
    public function parseTokens($content, $module, $record)
    {
        // Parse tokens
        $tokens = $this->getFieldTokenFromString($content);
        // Parse content
        $content = $this->mergeBlockTokens($tokens, $record, $content);

        // Only for Quoter module
        // Pricing table (IDC - Quoter & VTEItem)
        $vteItemsModuleName = 'VTEItems';
        $vteItemsModuleModel = Vtiger_Module_Model::getInstance($vteItemsModuleName);
        $quoterModuleName = 'Quoter';
        /** @var Quoter_Module_Model $quoterModel */
        $quoterModel = Vtiger_Module_Model::getInstance($quoterModuleName);

        if ($vteItemsModuleModel && $vteItemsModuleModel->isActive() && $quoterModel && $quoterModel->isActive()) {
            $content = $this->mergeQuoterBlockTokens($tokens, $record, $content);
        }

        $content = $this->mergeLinkModulesTokens($tokens, $record, $content);
        $content = $this->mergeTokens($tokens, $record, $content, $module);
        $content = $this->mergeCustomFunctions($content);
        // Escape special characters.
        $escapeCharacters = $this->getEscapeCharactersFromString($content);
        $content = $this->mergeEscapeCharacters($content, $escapeCharacters);

        return $content;
    }

    /**
     * @param string $moduleName
     */
    public function installWorkflows($moduleName)
    {
        global $adb, $vtiger_current_version;
        
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
        }elsE{
            $template_folder= "layouts/v7";
        }

        foreach ($this->workflows as $name => $label) {
            $dest1 = "modules/com_vtiger_workflow/tasks/{$name}.inc";
            $source1 = "modules/{$moduleName}/workflow/{$name}.inc";

            $file_exist1 = false;
            $file_exist2 = false;

            if (file_exists($dest1)) {
                $file_exist1 = true;
            } else {
                if (copy($source1, $dest1)) {
                    $file_exist1 = true;
                }
            }

            $dest2 = "$template_folder/modules/Settings/Workflows/Tasks/{$name}.tpl";
            $source2 = "$template_folder/modules/{$moduleName}/taskforms/{$name}.tpl";

            $templatepath = "modules/{$moduleName}/taskforms/{$name}.tpl";

            if (file_exists($dest2)) {
                $file_exist2 = true;
            } else {
                if (copy($source2, $dest2)) {
                    $file_exist2 = true;
                }
            }

            if ($file_exist1 && $file_exist2) {
                $sql1 = "SELECT * FROM com_vtiger_workflow_tasktypes WHERE tasktypename = ?";
                $result1 = $adb->pquery($sql1, array($name));

                if ($adb->num_rows($result1) == 0) {
                    // Add workflow task
                    $taskType = array(
                        'name' => $name,
                        'label' => $label,
                        'classname' => $name,
                        'classpath' => $source1,
                        'templatepath' => $templatepath,
                        'modules' => array(
                            'include' => array(),
                            'exclude' => array()
                        ),
                        'sourcemodule' => $moduleName
                    );
                    VTTaskType::registerTaskType($taskType);
                }
            }
        }
    }

    /**
     * @param string $moduleName
     */
    private function removeWorkflows($moduleName)
    {
        global $adb, $vtiger_current_version;
        
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
        }elsE{
            $template_folder= "layouts/v7";
        }

        $sql1 = "DELETE FROM com_vtiger_workflow_tasktypes WHERE sourcemodule = ?";
        $adb->pquery($sql1, array($moduleName));

        foreach ($this->workflows as $name => $label) {
            $likeTasks = '%:"' . $name . '":%';
            $sql2 = "DELETE FROM com_vtiger_workflowtasks WHERE task LIKE ?";
            $adb->pquery($sql2, array($likeTasks));

            @shell_exec("rm -f modules/com_vtiger_workflow/tasks/{$name}.inc");
            @shell_exec("rm -f $template_folder/modules/Settings/Workflows/Tasks/{$name}.tpl");

        }

    }

    /**
     * @param $name
     * @param string $extension
     * @param string $hash
     * @return string
     */
    public function makeUniqueFile($name, $extension = 'pdf', $hash = '')
    {
        $replace = '_';
        $hash = $hash . time();
        $name = preg_replace("/[^A-Za-z0-9]/", $replace, $name);
        $file = $name . $replace . $hash . '.' . $extension;

        return $file;
    }

    /**
     * @param mixed $focus
     * @param string $name
     * @param string $path
     * @return bool
     */
    public function createAttachFile($focus, $name, $path = 'storage/QuotingTool/')
    {
        global $adb, $current_user;

        $timestamp = date('Y-m-d H:i:s');
        $ownerid = $focus->column_fields['assigned_user_id'];
        $id = $adb->getUniqueID('vtiger_crmentity');
        $filetype = 'application/pdf';

        $sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES(?, ?, ?, ?, ?, ?, ?)";
        $params1 = array($id, $current_user->id, $ownerid, 'Emails Attachment', $focus->column_fields['description'], $timestamp, $timestamp);
        $adb->pquery($sql1, $params1);
        $sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) VALUES(?, ?, ?, ?, ?)";
        $params2 = array($id, $name, $focus->column_fields['description'], $filetype, $path);
        $adb->pquery($sql2, $params2);
        $sql3 = "INSERT INTO vtiger_seattachmentsrel VALUES(?,?)";
        $adb->pquery($sql3, array($focus->id, $id));

        return $id;
    }

    /**
     * @param $relatedProducts
     * @param $queryProducts
     * @return mixed
     */
    public function mergeRelatedProductWithQueryProduct($relatedProducts, $queryProducts)
    {
        $data = array();
        $queryProductKey = 0;

        // Remove all numerical keys
        // @link http://www.codingforums.com/php/66190-remove-all-numerical-keys.html
        foreach ($queryProducts as $p => $array) {
            foreach ($array as $key => $val) {
                if (is_numeric($key))
                    unset($queryProducts[$p][$key]);
            }
        }

        // Merge
        foreach ($relatedProducts as $k => $product) {
            $data[$k] = array();

            foreach ($product as $fieldName => $fieldValue) {
                if ($fieldName == 'final_details') {
                    continue;
                }

                // @link http://php.net/rtrim
                $myFieldName = rtrim($fieldName, $k);
                $data[$k][$myFieldName] = $fieldValue;
            }

            $data[$k] = array_merge($data[$k], $queryProducts[$queryProductKey++]);
        }

        return $data;
    }

    /**
     * Fn - formatNumber
     * @param $string_number
     * @return float
     */
    public function formatNumber($string_number)
    {
        global $current_user;

        $grouping = $current_user->currency_grouping_separator;
        $decimal = $current_user->currency_decimal_separator;
        $no_of_decimals = $current_user->no_of_currency_decimals;

        return number_format($string_number, $no_of_decimals, $decimal, $grouping);
    }

    static function resetValid()
    {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array(static::MODULE_NAME));
        $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);", array(static::MODULE_NAME, '0'));
    }

    static function removeValid()
    {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array(static::MODULE_NAME));
    }

    /**
     * Fn - getEmailList
     * Copy from SelectEmailFields.php
     *
     * @param string $moduleName
     * @param int $recordId
     * @return array
     */
        public function getEmailList($moduleName, $recordId, $isCreateNewRecord, $mutipRecord)
    {
        $email_field_list = array();
        $listRecord = array();
        if(!empty($mutipRecord))
        {
            $listRecord = $mutipRecord;
        }else{
            array_push( $listRecord,$recordId);
        }
        foreach ($listRecord as $val){
            $recordId = $val;
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            $accountId = 0;
            $contactId = 0;

            if ($moduleName == 'Quotes' || $moduleName == 'Invoice' || $moduleName == 'Contacts' || $moduleName == 'SalesOrder') {
                $accountId = $recordModel->get('account_id');
                $contactId = $recordModel->get('contact_id');
            } elseif ($moduleName == 'HelpDesk') {
                $accountId = $recordModel->get('parent_id');
                $contactId = $recordModel->get('contact_id');
            } elseif ($moduleName == 'Potentials') {
                $accountId = $recordModel->get('related_to');
                $contactId = $recordModel->get('contact_id');
            } elseif ($moduleName == 'Project') {
                $accountId = $recordModel->get('linktoaccountscontacts');
                if ($accountId && getSalesEntityType($accountId) != 'Accounts') {
                    $contactId = $accountId;
                    $accountId = 0;
                }
            } elseif ($moduleName == 'ProjectTask' && QuotingToolUtils::isRecordExists($recordModel->get('projectid'))) {
                $projectRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('projectid'));
                $accountId = $projectRecordModel->get('linktoaccountscontacts');
                if ($accountId && getSalesEntityType($accountId) != 'Accounts') {
                    $contactId = $accountId;
                    $accountId = 0;
                }
            } elseif ($moduleName == 'ServiceContracts') {
                $accountId = $recordModel->get('sc_related_to');
                if ($accountId && getSalesEntityType($accountId) != 'Accounts') {
                    $contactId = $accountId;
                    $accountId = 0;
                }
            }

            // With only contactid
            if ($moduleName == 'PurchaseOrder') {
                $contactId = $recordModel->get('contact_id');
            }

            if ($moduleName == 'Contacts') {
                $contactId = $recordId;
            }

            if ($moduleName == 'Accounts') {
                $accountId = $recordId;
            }

            if ($accountId && QuotingToolUtils::isRecordExists($accountId)) {
                $accountModuleModel = Vtiger_Module_Model::getInstance('Accounts');
                $accountRecordModel = Vtiger_Record_Model::getInstanceById($accountId);
                $emailFields = $accountModuleModel->getFieldsByType('email');
                $emailFields = array_keys($emailFields);
                $i = 1;
                foreach ($emailFields as $fieldname) {
                    $emailValue = $accountRecordModel->get($fieldname);
                    if ($emailValue) {
                        $email_field_list[$i . "||" . $accountId . "||" . $emailValue] = $accountRecordModel->getDisplayName() . " ($emailValue)";
                        $i++;
                    }
                }
            }

            if ($contactId && QuotingToolUtils::isRecordExists($contactId)) {
                $contactModuleModel = Vtiger_Module_Model::getInstance('Contacts');
                $contactRecordModel = Vtiger_Record_Model::getInstanceById($contactId);
                $emailFields = $contactModuleModel->getFieldsByType('email');
                $emailFields = array_keys($emailFields);
                $i = 1;
                foreach ($emailFields as $fieldname) {
                    $emailValue = $contactRecordModel->get($fieldname);
                    if ($emailValue) {
                        $email_field_list[$i . "||" . $contactId . "||" . $emailValue] = $contactRecordModel->getDisplayName() . " ($emailValue)";
                        $i++;
                    }
                }
            }

            // Primitive email on other modules
            if ($moduleName == 'Leads' || $moduleName == 'Accounts' && QuotingToolUtils::isRecordExists($recordId)) {
                $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
                $emailFields = $moduleModel->getFieldsByType('email');
                $emailFields = array_keys($emailFields);
                $i = 1;

                foreach ($emailFields as $fieldname) {
                    $emailValue = $recordModel->get($fieldname);

                    if ($emailValue) {
                        $email_field_list[$i . "||" . $contactId . "||" . $emailValue] = $recordModel->getDisplayName() . " ($emailValue)";
                        $i++;
                    }
                }
            }

            if ($moduleName == 'PurchaseOrder') {
                // Reference with vendor
                $vendorId = $recordModel->get('vendor_id');

                if (QuotingToolUtils::isRecordExists($vendorId)) {
                    $moduleModel = Vtiger_Module_Model::getInstance('Vendors');
                    $recordModel = Vtiger_Record_Model::getInstanceById($vendorId);
                    $emailFields = $moduleModel->getFieldsByType('email');
                    $emailFields = array_keys($emailFields);
                    $i = 1;

                    foreach ($emailFields as $fieldname) {
                        $emailValue = $recordModel->get($fieldname);

                        if ($emailValue) {
                            $email_field_list[$i . "||" . $contactId . "||" . $emailValue] = $recordModel->getDisplayName() . " ($emailValue)";
                            $i++;
                        }
                    }
                }
            }
            // Primitive email on other modules
            if (QuotingToolUtils::isRecordExists($recordId)) {
                $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
                $emailFields = $moduleModel->getFieldsByType('email');
                $emailFields = array_keys($emailFields);
                $i = 1;

                foreach ($emailFields as $fieldname) {
                    $emailValue = $recordModel->get($fieldname);

                    if ($emailValue) {
                        $email_field_list[$i . "||" . $recordId . "||" . $emailValue] = $recordModel->getDisplayName() . " ($emailValue)";
                        $i++;
                    }
                }
            }

            // get email when iscreatnewrecord = true
            if ($isCreateNewRecord == 1 && QuotingToolUtils::isRecordExists($recordId)) {
                $moduleName = $recordModel->getModuleName();
                $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                $emailFields = $moduleModel->getFieldsByType('email');
                $emailFields = array_keys($emailFields);
                $i = 1;

                foreach ($emailFields as $fieldname) {
                    $emailValue = $recordModel->get($fieldname);
                    if ($emailValue) {
                        $email_field_list[$i . "||" . $recordId . "||" . $emailValue] = $recordModel->getDisplayName() . " ($emailValue)";
                        $i++;
                    }
                }
            }
        }

        $email_field_list = array_unique($email_field_list);
        return $email_field_list;
    }

    public static function getConfig()
    {
        global $site_URL, $current_user;
        $data = array();

        $data['base'] = $site_URL;
        $data['date_format'] = $current_user->date_format;
        $data['hour_format'] = $current_user->hour_format;
        $data['start_hour'] = $current_user->start_hour;
        $data['end_hour'] = $current_user->end_hour;
        $data['time_zone'] = $current_user->time_zone;
        $data['dayoftheweek'] = $current_user->dayoftheweek;

        return $data;
    }

    public static function getModules()
    {
        $data = array();
        $quotingTool = new QuotingTool();
        $inventoryModules = getInventoryModules();
        $quotingTool->enableModules = $quotingTool->getAllEntityModule();

        foreach ($quotingTool->enableModules as $module) {
            $moduleModel = Vtiger_Module_Model::getInstance($module);
            $moduleInfo = $quotingTool->parseModule($moduleModel);
            $relations = $quotingTool->getRelatedModules($moduleModel);
            $moduleInfo['related_modules'] = array();
            $moduleInfo['link_modules'] = array();
            $moduleInfo['final_details'] = array();
            $linkModule = Vtiger_Relation_Model::getAllRelations($moduleModel, $selected = true, $onlyActive = true);

            foreach ($linkModule as $labelModule) {
                if(in_array($labelModule->get('modulename'),$quotingTool->ignoreLinkModules)){
                    continue;
                }
                $moduleLinkModel = Vtiger_Module_Model::getInstance($labelModule->get('modulename'));
                $excludeBlocks = array(
                    'LBL_ITEM_DETAILS' => array('*')
                );
                $moduleInfo['link_modules'][] = $quotingTool->parseModule($moduleLinkModel, $excludeBlocks);
            }
            // related_modules
            foreach ($relations as $relation) {
                if ($relation) {
                    $moduleInfo['related_modules'][] = $quotingTool->parseModule($relation);
                }
            }

            // picklist
            $moduleInfo['picklist'] = static::getPicklistFields($module);

            // final_details
            if (in_array($module, $inventoryModules)) {
                $moduleInfo['final_details'] = QuotingTool::getTotalFields($module);
            }

            $data[] = $moduleInfo;
        }
        return $data;
    }

    public static function getCustomFunctions()
    {
        $data = array();
        $ready = false;
        $function_name = "";
        $function_params = array();
        $functions = array();

        $files = glob('modules/QuotingTool/resources/functions/*.php');
        foreach ($files as $file) {
            $filename = $file;
            $source = fread(fopen($filename, "r"), filesize($filename));
            $tokens = token_get_all($source);
            foreach ($tokens as $token) {
                if (is_array($token)) {
                    if ($token[0] == T_FUNCTION)
                        $ready = true;
                    elseif ($ready) {
                        if ($token[0] == T_STRING && $function_name == "")
                            $function_name = $token[1];
                        elseif ($token[0] == T_VARIABLE)
                            $function_params[] = $token[1];
                    }
                } elseif ($ready && $token == "{") {
                    $ready = false;
                    $functions[$function_name] = $function_params;
                    $function_name = "";
                    $function_params = array();
                }
            }
        }

        foreach ($functions as $funcName => $funcParams) {
            $strPrams = implode("|", $funcParams);
            $customFunction = trim($funcName . "|" . str_replace("$", "", $strPrams), "|");
            $data[] = array(
                'token' => '[CUSTOMFUNCTION|' . $customFunction . '|CUSTOMFUNCTION]',
                'name' => $funcName,
                'label' => vtranslate($funcName, self::MODULE_NAME),
            );
        }

        return $data;
    }

    public static function getCustomFields()
    {
        $quotingTool = new QuotingTool();
        $customBlock = array(
            'name' => 'LBL_CUSTOM_BLOCK',
            'fields' => array(
                array(
                    'name' => 'custom_proposal_link',
                ),
                array(
                    'name' => 'custom_user_signature'
                )
            )
        );

        $blocks = array();
        $blocks[] = $customBlock;
        $data = $quotingTool->fillBlockFields('', $blocks);

        return $data;
    }

    public static function getCompanyFields()
    {
        $quotingTool = new QuotingTool();
        $moduleModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
        $fields = array();
        foreach ($moduleModel->getFields() as $key => $val) {
            if ($key == 'logo') {
                continue;
            }
            $fields[] = array('name' => "Vtiger_Company_".$key);
        }

        $customBlock = array(
            'name' => 'LBL_COMPANY_BLOCK',
            'fields' => $fields
        );

        $blocks = array();
        $blocks[] = $customBlock;
        $data = $quotingTool->fillBlockFields('Vtiger', $blocks);
        return $data;
    }

    /**
     * @param string $rel_module
     * @return array
     */
    public static function getPicklistFields($rel_module)
    {
        $data = array();
        $moduleModel = Vtiger_Module_Model::getInstance($rel_module);
        $fields = $moduleModel->getFields();

        /**
         * @var string $name
         * @var Vtiger_Field_Model $field
         */
        foreach ($fields as $name => $field) {
            $fieldModel = Vtiger_Field_Model::getInstance($field->get('id'));
            $fieldDataType = $fieldModel->getFieldDataType();

            if ($fieldDataType != 'picklist' && $fieldDataType != 'multipicklist') {
                continue;
            }

            $picklist = $fieldModel->getPicklistValues();

            if (!empty($picklist)) {
                $data[] = array(
                    'id' => $fieldModel->get('id'),
                    'name' => $fieldModel->get('name'),
                    'label' => $fieldModel->get('label'),
                    'values' => $picklist
                );
            }
        }

        return $data;
    }

    /**
     * @param string $moduleName
     * @param array $blocks
     * @return array
     */
    public function fillBlockFields($moduleName, $blocks)
    {
        $data = array();

        foreach ($blocks as $block) {
            $blockId = isset($block['id']) ? $block['id'] : 0;
            $blockName = $block['name'];
            $blockLabel = isset($block['label']) ? $block['label'] : vtranslate($blockName, self::MODULE_NAME);
            $fields = $block['fields'];

            foreach ($fields as $field) {
                $fieldId = isset($field['id']) ? $field['id'] : 0;
                $uitype = isset($field['uitype']) ? $field['uitype'] : 0;
                $datatype = isset($field['datatype']) ? $field['datatype'] : 'text';
                $fieldName = $field['name'];
                $fieldLabel = isset($field['label']) ? $field['label'] : vtranslate($fieldName, self::MODULE_NAME);
                $token = isset($field['token']) ? $field['token'] : $this->convertFieldToken($fieldName, $moduleName);

                $data[] = array(
                    'id' => $fieldId,
                    'name' => $fieldName,
                    'uitype' => $uitype,
                    'datatype' => $datatype,
                    'label' => $fieldLabel,
                    'token' => $token,
                    'block' => array(
                        'id' => $blockId,
                        'name' => $blockName,
                        'label' => $blockLabel,
                    )
                );
            }
        }

        return $data;
    }

    public function getAllEntityModule()
    {
        $supportedModulesList = Settings_LayoutEditor_Module_Model::getSupportedModules();
        return $supportedModulesList = array_keys($supportedModulesList);
    }

    /**
     * @param null|string $moduleName
     * @return array
     */
    public static function getTotalFields($moduleName = null)
    {
        $data = array();
        $quotingTool = new QuotingTool();
        // Hardcode from: modules/Inventory/views/Detail.php:74
        $totalBlock = array(
            'name' => 'LBL_TOTAL_BLOCK',
            'fields' => array(
                array(
                    'name' => 'hdnSubTotal',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_ITEMS_TOTAL', $moduleName)
                ),
                array(
                    'name' => 'discountTotal_final',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_DISCOUNT', $moduleName)
                ),
                array(
                    'name' => 'shipping_handling_charge',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_SHIPPING_AND_HANDLING_CHARGES', $moduleName)
                ),
                array(
                    'name' => 'preTaxTotal',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_PRE_TAX_TOTAL', $moduleName)
                ),
                array(
                    'name' => 'tax_totalamount',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_TAX', $moduleName)
                ),
                array(
                    'name' => 'shtax_totalamount',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_TAX_FOR_SHIPPING_AND_HANDLING', $moduleName)
                ),
                array(
                    'name' => 'adjustment',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_ADJUSTMENT', $moduleName)
                ),
                array(
                    'name' => 'grandTotal',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_GRAND_TOTAL', $moduleName)
                )
            )
        );
        if ($moduleName == 'Invoice') {
            array_push($totalBlock['fields'], array(
                'name' => 'received',
                'datatype' => 'currency',
                'label' => vtranslate('LBL_RECEIVED', $moduleName)
            ),array(
                    'name' => 'balance',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_BALANCE', $moduleName)
            ));
        }elseif ($moduleName == 'PurchaseOrder'){
            array_push($totalBlock['fields'], array(
                'name' => 'paid',
                'datatype' => 'currency',
                'label' => vtranslate('LBL_PAID', $moduleName)
            ),array(
                'name' => 'balance',
                'datatype' => 'currency',
                'label' => vtranslate('LBL_BALANCE', $moduleName)
            ));
        }

        if ($moduleName) {
            $blocks = array();
            $blocks[] = $totalBlock;
            $data = $quotingTool->fillBlockFields($moduleName, $blocks);
        } else {
            $inventoryModules = getInventoryModules();

            foreach ($inventoryModules as $moduleName) {
                $blocks = array();
                $blocks[] = $totalBlock;
                $data = array_merge($data, $quotingTool->fillBlockFields($moduleName, $blocks));
            }
        }

        return $data;
    }

    protected static function updateModule($moduleName) {
        require_once("scripts/add_new_field_20170203_1.php");
        require_once("scripts/add_new_field_20170306_1.php");
        require_once("scripts/add_status_field_20170724_1.php");
        require_once("scripts/update_status_field_20170810_1.php");
        require_once("scripts/add_createnewrecords_field_20171101_1.php");
    }

}
