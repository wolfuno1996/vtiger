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
    <div id="massEditContainer" class='modelContainer'>
        <div id="massEdit">
            <div class="modal-header contentsBackground">
                <button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="massEditHeader">Preview & Send Email</h3>
            </div>
            <form class="form-horizontal" action="index.php" id="quotingtool_emailtemplate">
                <input type="hidden" name="module" value="{$MODULE}"/>
                <input type="hidden" name="action" value="PDFHandler"/>
                <input type="hidden" name="mode" value="preview_and_send_email"/>
                <input type="hidden" name="transaction_id" value='{$TRANSACTION_ID}'/>
                <input type="hidden" name="record" value="{$RECORDID}"/>
                <input type="hidden" name="template_id" value='{$TEMPLATEID}'/>
                <input type="hidden" name="multi_record" value='{Zend_Json::encode($MULTI_RECORD)}'/>
                <div name='massEditContent' class="row-fluid">
                    <div class="modal-body">
                        <div class="row-fluid" style="margin: 5px;">
                            <div class="span12">
                                <input type="text" style="width: 98%;" class="input-large" id="email_subject" name="email_subject"
                                       placeholder="Email Subject" value="{$EMAIL_SUBJECT}"/>
                            </div>
                        </div>
                        <div id="multiEmailContainer">
                            {if $EMAIL_FIELD_LIST}
                                {assign var=i value=0}
                                {assign var=allEmailArr value=[]}

                                {foreach item=EMAIL_FIELD_LABEL key=EMAIL_FIELD_NAME from=$EMAIL_FIELD_LIST name=emailFieldIterator}
                                    {append var=allEmailArr value=$EMAIL_FIELD_LABEL index=$i}

                                    <div class="control-group">
                                        <label class="checkbox">
                                            <input type="checkbox" class="emailField" name="selectedEmail[{$i++}]" value='{$EMAIL_FIELD_NAME}' />
                                            <span>{$EMAIL_FIELD_LABEL}</span>
                                        </label>
                                    </div>
                                {/foreach}

                                <div class="control-group">
                                    <div class="pull-left">
                                        <input type="hidden" class="span4 select2 select2-tags"
                                               name="ccValues" data-tags='{$allEmailArr|json_encode}'
                                               placeholder="{vtranslate('CC', $MODULE)}" />
                                    </div>

                                    <div class="pull-left">
                                        <input type="hidden" class="span4 select2 select2-tags"
                                               name="bccValues" data-tags='{$allEmailArr|json_encode}'
                                               placeholder="{vtranslate('BCC', $MODULE)}" />
                                    </div>
                                </div>
                            {else}
                                {vtranslate('Does not have any email to select.', $MODULE)}
                            {/if}
                        </div>
                        <div class="row-fluid" style="margin: 5px;">
                            <div class="span12">
                                <textarea placeholder="Email Content" id="email_content" name="email_content" rows="5">{$EMAIL_CONTENT}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-left custom_proposal_link">
                        <label class="checkbox check_attach_file">
                            <input type="checkbox" name="check_attach_file" />
                            <span>{vtranslate('EMAIL_ATTACH_DOCUMENT', $MODULE)}</span>
                        </label>
                        <a href="{$CUSTOM_PROPOSAL_LINK}" target="_blank">{vtranslate('EMAIL_DOCUMENT_PREVIEW', $MODULE)}</a>
                    </div>
                    <div class="pull-right cancelLinkContainer" style="margin-top:0;">
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    <button class="btn addButton" type="submit" name="saveButton">
                        <strong>{vtranslate('LBL_SEND', $MODULE)}</strong>
                    </button>
                </div>
            </form>
        </div>
    </div>
{/strip}