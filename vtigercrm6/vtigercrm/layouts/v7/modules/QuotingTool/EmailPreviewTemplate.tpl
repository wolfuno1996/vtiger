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
    <div class="modal myModal fade in" style="display: block;" aria-hidden="false">
        <div class="modal-backdrop fade in"></div>

        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal" method="post" action="index.php" id="quotingtool_emailtemplate">
                    <div class="modal-header">
                        <div class="clearfix">
                            <div class="pull-right ">
                                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                                    <span aria-hidden="true" class="fa fa-close"></span>
                                </button>
                            </div>
                            <h4 class="pull-left">{vtranslate('Preview & Send Email', $MODULE)}</h4>
                        </div>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="module" value="{$MODULE}"/>
                        <input type="hidden" name="action" value="PDFHandler"/>
                        <input type="hidden" name="mode" value="preview_and_send_email"/>
                        <input type="hidden" name="transaction_id" value='{$TRANSACTION_ID}'/>
                        <input type="hidden" name="record" value="{$RECORDID}"/>
                        <input type="hidden" name="template_id" value='{$TEMPLATEID}'/>



                        <div class="row-fluid" style="margin: 5px;">
                            <div class="span12">
                                <input type="text" name="email_subject" class="input-large" id="email_subject"
                                       placeholder="Email Subject" value="{$EMAIL_SUBJECT}"
                                       style="width: 98%;"/>
                            </div>
                        </div>
                        <div id="multiEmailContainer">
                            {if $EMAIL_FIELD_LIST}
                                {assign var=i value=0}
                                {assign var=allEmailArr value=[]}

                                {foreach item=EMAIL_FIELD_LABEL key=EMAIL_FIELD_NAME from=$EMAIL_FIELD_LIST name=emailFieldIterator}
                                    {append var=allEmailArr value=$EMAIL_FIELD_LABEL index=$i}

                                    <div class="control-group" style="margin-left: 25px; margin-bottom: 10px">
                                        <label class="checkbox">
                                            <input type="checkbox" class="emailField" name="selectedEmail[{$i++}]" value='{$EMAIL_FIELD_NAME}' />
                                            <span style="padding-left: 10px">{$EMAIL_FIELD_LABEL}</span>
                                        </label>
                                    </div>
                                {/foreach}

                                <div class="control-group clearfix" style="margin-bottom: 10px">
                                    <div class="pull-left">
                                        <input type="hidden" class="span4 form-control select2 select2-tags"
                                               name="ccValues" data-tags='{$allEmailArr|json_encode}'
                                               placeholder="{vtranslate('CC', $MODULE)}" style="width: 300px; margin-right: 10px" />
                                    </div>

                                    <div class="pull-left">
                                        <input type="hidden" class="span4 form-control select2 select2-tags"
                                               name="bccValues" data-tags='{$allEmailArr|json_encode}'
                                               placeholder="{vtranslate('BCC', $MODULE)}" style="width: 300px" />
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
                    <div class="modal-footer">
                        <div class="pull-left custom_proposal_link">
                            <label class="check_attach_file text-left" style="display: block;">
                                <input type="checkbox" name="check_attach_file" />&nbsp;
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
    </div>
{/strip}