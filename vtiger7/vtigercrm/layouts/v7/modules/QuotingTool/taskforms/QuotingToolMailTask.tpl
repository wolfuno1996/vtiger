{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    {assign var=FOCUS_MODULE value='QuotingTool'}
    <div id="VtEmailTaskContainer" class="clearfix">
        <div class="contents tabbable ui-sortable">
            <ul class="nav nav-tabs layoutTabs massEditTabs">
                <li class="active">
                    <a data-toggle="tab" href="#detailViewLayout" id="detailViewLayoutBtn">
                        <strong>{vtranslate('LBL_EMAIL_DETAILS', $FOCUS_MODULE)}</strong>
                    </a>
                </li>
                <li class="relatedListTab">
                    <a data-toggle="tab" href="#relatedTabTemplate" class="workflowTab">
                        <strong>{vtranslate('LBL_PDF_TEMPLATE', $FOCUS_MODULE)}</strong>
                    </a>
                </li>
                <li class="relatedListTab">
                    <a data-toggle="tab" href="#relatedTabContent" class="workflowTab">
                        <strong>{vtranslate('LBL_EMAIL_CONTENT', $FOCUS_MODULE)}</strong>
                    </a>
                </li>
            </ul>
            <div class="tab-content layoutContent padding20 themeTableColor overflowVisible">
                <div class="tab-pane active col-sm-12 col-xs-12" id="detailViewLayout">
                    <div class="row form-group">
                        <div class="col-sm-6 col-xs-6">
                            <div class="row">
                                <div class="col-sm-3 col-xs-3"><span
                                            class="span2">{vtranslate('LBL_FROM', $QUALIFIED_MODULE)|ucfirst}</span>
                                </div>
                                <div class="col-sm-9 col-xs-9"><input name="fromEmail" class=" fields inputElement" type="text"
                                                                      value="{$TASK_OBJECT->fromEmail}"/></div>
                            </div>
                        </div>
                        <div class="col-sm-5 col-xs-5">
                            <select id="fromEmailOption" style="min-width: 250px" class="task-fields select2"
                                    data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                <option></option>
                                {$FROM_EMAIL_FIELD_OPTION}
                            </select>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-6 col-xs-6">
                            <div class="row">
                                <span class="col-sm-3 col-xs-3">{vtranslate('LBL_TO',$QUALIFIED_MODULE)|ucfirst}<span class="redColor">*</span></span>
                                <div class="col-sm-9 col-xs-9">
                                    <input data-rule-required="true" name="recepient" class="fields inputElement"
                                           type="text" value="{$TASK_OBJECT->recepient}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 col-xs-5">
                            <select style="min-width: 250px" class="task-fields select2"
                                    data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                <option></option>
                                {$EMAIL_FIELD_OPTION}
                            </select>
                        </div>
                    </div>

                    <div class="row form-group {if empty($TASK_OBJECT->emailcc)}hide {/if}" id="ccContainer">
                        <div class="col-sm-6 col-xs-6">
                            <div class="row">
                                <div class="col-sm-3 col-xs-3">{vtranslate('LBL_CC',$QUALIFIED_MODULE)}</div>
                                <div class="col-sm-9 col-xs-9">
                                    <input class="fields inputElement" type="text" name="emailcc" value="{$TASK_OBJECT->emailcc}" />
                                </div>
                            </div>
                        </div>
					<span class="col-sm-5 col-xs-5">
						<select class="task-fields select2" data-placeholder='{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}' style="min-width: 250px">
                            <option></option>
                            {$EMAIL_FIELD_OPTION}
                        </select>
					</span>
                    </div>

                    <div class="row form-group {if (!empty($TASK_OBJECT->emailcc)) and (!empty($TASK_OBJECT->emailbcc))} hide {/if}">
                        <div class="col-sm-8 col-xs-8">
                            <div class="row">
                                <div class="col-sm-3 col-xs-3">&nbsp;</div>
                                <div class="col-sm-9 col-xs-9">
                                    <a class="cursorPointer {if (!empty($TASK_OBJECT->emailcc))}hide{/if}" id="ccLink">{vtranslate('LBL_ADD_CC',$QUALIFIED_MODULE)}</a>&nbsp;&nbsp;
                                    <a class="cursorPointer {if (!empty($TASK_OBJECT->emailbcc))}hide{/if}" id="bccLink">{vtranslate('LBL_ADD_BCC',$QUALIFIED_MODULE)}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6 col-xs-6">
                            <div class="row">
                                <div class="col-sm-3 col-xs-3">{vtranslate('LBL_SUBJECT',$QUALIFIED_MODULE)}<span class="redColor">*</span></div>
                                <div class="col-sm-9 col-xs-9">
                                    <input data-rule-required="true" name="subject" class="fields inputElement" type="text" name="subject" value="{$TASK_OBJECT->subject}" id="subject" spellcheck="true"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 col-xs-5">
                            <select style="min-width: 250px" class="task-fields select2" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                <option></option>
                                {$ALL_FIELD_OPTIONS}
                            </select>
                        </div>
                    </div>

                </div>
                <div class="tab-pane col-sm-12 col-xs-12" id="relatedTabTemplate">
                    <div class="row form-group">
                        <div class="col-sm-6 col-xs-6">
                            <div class="row">
                                <div class="col-sm-3 col-xs-3"><span
                                            class="span2">{vtranslate('LBL_PDF_TEMPLATE', $FOCUS_MODULE)}</span>
                                </div>
                                <div class="col-sm-9 col-xs-9">
                                    <select multiple style="min-width: 500px" id="tasks_template" name="template" class="select2">
                                        {html_options  options=$TASK_OBJECT->getTemplates($SOURCE_MODULE) selected=$TASK_OBJECT->template}
                                    </select>
                                    <input type="hidden" id="task_folder_value" value="{$TASK_OBJECT->template}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6 col-xs-6">
                            <div class="row">
                                <div class="col-sm-3 col-xs-3"><span
                                            class="span2">{vtranslate('LBL_PDF_ATTACH_FILE', $FOCUS_MODULE)}</span>
                                </div>
                                <div class="col-sm-9 col-xs-9">
                                    <input type="checkbox" class="alignTop" name="check_attach_file"
                                           {if $TASK_OBJECT->check_attach_file neq null}checked{/if}/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane col-sm-12 col-xs-12" id="relatedTabContent">
                    <div class="row form-group">
                        <div class="col-sm-6 col-xs-6">
                            <div class="row">
                                <div style="margin-top: 7px" class="col-sm-3 col-xs-3">{vtranslate('LBL_ADD_FIELD',$QUALIFIED_MODULE)}</div>&nbsp;&nbsp;
                                <div class="col-sm-8 col-xs-8">
                                    <select style="min-width: 250px" id="task-fieldnames" class="select2"
                                            data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                        <option></option>
                                        {$ALL_FIELD_OPTIONS}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 col-xs-5">
                            <div class="row">
                                <div style="margin-top: 7px" class="col-sm-3 col-xs-3">{vtranslate('LBL_ADD_TIME',$QUALIFIED_MODULE)}</div>&nbsp;&nbsp;
                                <div class="col-sm-8 col-xs-8">
                                    <select style="width: 205px" id="task_timefields" class="select2"
                                            data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                        <option></option>
                                        {foreach from=$META_VARIABLES item=META_VARIABLE_KEY key=META_VARIABLE_VALUE}
                                            <option value="${$META_VARIABLE_KEY}">{vtranslate($META_VARIABLE_VALUE,$QUALIFIED_MODULE)}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12 col-xs-12">
                            <textarea id="content" name="content">{$TASK_OBJECT->content}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        /**
         * Fn - registerQuotingToolMailTaskEvents
         */
        Settings_Workflows_Edit_Js.prototype.registerQuotingToolMailTaskEvents = function () {
            var textAreaElement = jQuery('#content');
            var ckEditorInstance = this.getckEditorInstance();
            ckEditorInstance.loadCkEditor(textAreaElement);
            this.registerFillMailContentEvent();
            this.registerFillTaskFromEmailFieldEvent();
            this.registerCcAndBccEvents();
        };

        /**
         * Fn - QuotingToolMailTaskCustomValidation
         */
        Settings_Workflows_Edit_Js.prototype.QuotingToolMailTaskCustomValidation = function () {
            var result = true;

            var selectElement1 = jQuery('input[name="recepient"]');
            var control1 = selectElement1.val();

            var selectElement2 = jQuery('input[name="subject"]');
            var control2 = selectElement2.val();

            if (control1 == "" || control2 == "") {
                jQuery('#detailViewLayoutBtn').trigger('click');
                result = app.vtranslate('JS_REQUIRED_FIELD');
            }

            return result;
        };

        /**
         * Fn - preSaveQuotingToolMailTask
         * @param tasktype
         */
        Settings_Workflows_Edit_Js.prototype.preSaveQuotingToolMailTask = function (tasktype) {
            var textAreaElement = jQuery('#content');

            textAreaElement.val(CKEDITOR.instances['content'].getData());
        };

    </script>
{/strip}