{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <div class="editViewContainer container-fluid" ng-app="app" id="quoting_tool-app">
        <div id="js_currentUser" class="hide noprint">{Zend_Json::encode($USER_PROFILE)}</div>
        <div id="js_config" class="hide noprint">{Zend_Json::encode($CONFIG)}</div>
        <div id="js_modules" class="hide noprint">{Zend_Json::encode($MODULES)}</div>
        <div id="js_custom_functions" class="hide noprint">{Zend_Json::encode($CUSTOM_FUNCTIONS)}</div>
        <div id="js_custom_fields" class="hide noprint">{Zend_Json::encode($CUSTOM_FIELDS)}</div>
        <div id="js_company_fields" class="hide noprint">{Zend_Json::encode($COMPANY_FIELDS)}</div>
        {if (isset($QUOTER_SETTINGS))}
            <div id="js_quoter_settings" class="hide noprint">{Zend_Json::encode($QUOTER_SETTINGS)}</div>
        {/if}

        <div id="quoting_tool-body" ng-controller="CtrlApp">
            <form action="index.php" id="EditView" name="EditView" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="Save"/>
                <input type="hidden" name="record" value="{$RECORD_ID}"/>
                <input type="hidden" name="module" value="{$MODULE}"/>
                <input type="hidden" name="primary_module" value="{{($TEMPLATE) ? $TEMPLATE->get('module') : ''}}"/>
                <textarea name="body" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('body') : ''}}</textarea>
                <textarea name="header" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('header') : ''}}</textarea>
                <textarea name="content" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('content') : ''}}</textarea>
                <textarea name="footer" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('footer') : ''}}</textarea>
                <input type="hidden" name="email_subject" value="{{($TEMPLATE) ? $TEMPLATE->get('email_subject') : ''}}">
                <textarea name="email_content" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('email_content') : ''}}</textarea>
                <textarea name="description" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('description') : ''}}</textarea>
                <textarea name="anwidget" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('anwidget') : ''}}</textarea>
                <textarea name="createnewrecords" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('createnewrecords') : ''}}</textarea>
                <textarea name="linkproposal" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('linkproposal') : ''}}</textarea>
                <textarea name="mapping_fields" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('mapping_fields') : ''}}</textarea>
                <textarea name="settings" class="hide">{$SETTINGS}</textarea>
                <textarea name="attachments" class="hide">{{($TEMPLATE) ? $TEMPLATE->get('attachments') : ''}}</textarea>
                <input type="hidden" name="is_active" class="hide" value="{{($TEMPLATE->get('is_active') !='') ? $TEMPLATE->get('is_active') : '1'}}">

                <div id="quoting_tool-header">
                    <div id="quoting_tool-header-actions" style="display: none;">
                        <div class="pull-left">
                            <input name="filename" value="{{($TEMPLATE) ? $TEMPLATE->get('filename') : ''}}">
                        </div>
                        <button class="btn btn-primary" type="submit" ng-click="saveTemplate($event)">Save</button>
                    </div>
                </div>
                <div id="quoting_tool-container">

                    <div id="quoting_tool-center" class="column" resize>
                        <div class="document__block-list quoting_tool-content">
                            <div class="quoting_tool-content-header doc-block doc-block--header"></div>

                            <div class="quoting_tool-content-main quoting_tool-drop-component-in-content document__block-list"></div>

                            <div class="quoting_tool-content-footer doc-block doc-block--footer"></div>
                        </div>

                        <div id="quoting_tool-overlay-content" class="blockUI blockOverlay"
                             style="display: none;"></div>
                    </div>

                    <div id="quoting_tool-left-panel" class="column">
                        {*<ul>*}
                            {*<li class="btn--navpanel">*}
                                {*<a href="#" title="Dashboard">*}
                                    {*<i class="icon icon--dashboard"></i>*}
                                    {*<span></span>*}
                                {*</a>*}
                            {*</li>*}
                        {*</ul>*}
                    </div>

                    <div id="quoting_tool-right-panel" class="column" ng-controller="CtrlAppRightPanel">
                        <div id="quoting_tool-tool-items">
                            <div id="quoting_too-file-name-container"
                                 ng-include="'layouts/vlayout/modules/QuotingTool/resources/js/views/right_panel/basic_infomation.html'">
                            </div>

                            <div ui-view="right_panel_tool_items"></div>
                        </div>

                        <div id="quoting_tool-tools"
                             ng-include="'layouts/vlayout/modules/QuotingTool/resources/js/views/right_panel/tools.html'">
                        </div>
                    </div>

                    <div class="clear"></div>
                </div>

                <div id="quoting_tool-footer">
                </div>
            </form>

            <div style="width: 0; height: 0; visibility: hidden;" id="quoting_tool-tmp">
                <div id="quoting_tool-tmp-content"></div>
            </div>
        </div>
    </div>
{/strip}