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
    <style>
        .listViewEntries td {
            cursor: default;
        }
    </style>
    <div class="row">
        <div class="span6" style="padding: 15px; margin-left: 10px; width: 48%">
            <button id="Contacts_listView_basicAction_LBL_ADD_RECORD" class="btn addButton"
                    onclick="window.location.href='index.php?module={$MODULE}&view=Edit'"><i class="icon-plus"></i>&nbsp;
                <strong>{vtranslate('LBL_ADD',$MODULE)}</strong>
            </button>
        </div>
        <div class="span6" style="padding: 15px; width: 45%">
            <p data-toggle="tooltip" {if $MBSTRING eq 'installed'} hidden{/if} data-placement="bottom" title="php-mbstring is php extension that is required for Document Designer to work properly." style="width: 100px; float: left;font-weight: bold;">
                <i class="glyphicon glyphicon-warning-sign" style="color: red"></i>  php-mbstring</p>
            <p data-toggle="tooltip" {if $PHPZIP eq 'installed'} hidden{/if} data-placement="bottom" title="php-zip is php extension is required to import/load default templates. If you are creating your own template, you don't need to install this." style="width: 100px; float: left;font-weight: bold; margin-left: 10px">
                <i class="glyphicon glyphicon-warning-sign" style="color: red"></i>  php-gd</p>
            <button type="button" id="exportTemplate" class="btn btn-primary" style="float: right">Export</button>
            <button type="button"  id="importTemplate"  class="btn btn-success" style="float: right; margin-right: 10px;">Import</button>
            <input id="fileupload" type="file" name="files[]" data-url="index.php?module=QuotingTool&action=ActionAjax&mode=importTemplate" multiple style="visibility: hidden; width: 10px">
            <div style="margin-right: 10px; float: right">
                <select name="default-templates" id="default-templates" class="select2" aria-labelledby="Default Templates" style="width: 200px; ">
                    <option value="Default" selected>Default Templates</option>
                    <option value="Light-Blue-Invoice.zip">Light-Blue Invoice Template</option>
                    <option value="Light-Blue-Quote.zip">Light-Blue Quote Template</option>
                    <option value="Light-Blue-SO.zip">Light-Blue Sales Order Template</option>
                    <option value="Light-Blue-PO.zip">Light-Blue Purchase Order Template</option>

                    <option value="Gray-Invoice.zip">Gray Invoice Template</option>
                    <option value="Gray-Quote.zip">Gray Quote Template</option>
                    <option value="Gray-SO.zip">Gray Sales Order Template</option>
                    <option value="Gray-PO.zip">Gray Purchase Order Template</option>

                    <option value="Green-Invoice.zip">Green Invoice Template</option>
                    <option value="Green-Quote.zip">Green Quote Template</option>
                    <option value="Green-SO.zip">Green Sales Order Template</option>
                    <option value="Green-PO.zip">Green Purchase Order Template</option>

                    <option value="Red-Invoice.zip">Red Invoice Template</option>
                    <option value="Red-Quote.zip">Red Quote Template</option>
                    <option value="Red-SO.zip">Red Sales Order Template</option>
                    <option value="Red-PO.zip">Red Purchase Order Template</option>

                    <option value="Yellow-Opportunity.zip">Yellow Proposal Template (6 Pages)</option>
                </select></div>

        </div>
    </div>

    <div class="listViewContentDiv" id="listViewContents">
        <div class="listViewEntriesDiv contents-bottomscroll">
            <div class="bottomscroll-div">
                {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                    <tr class="listViewHeaders">
                        {foreach item=LISTVIEW_HEADER key=COLUMNNAME from=$LISTVIEW_HEADERS}
                            <th nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if}>
                                {vtranslate($LISTVIEW_HEADER, $MODULE)}
                            </th>
                        {/foreach}
                    </tr>
                    </thead>

                    <tbody>
                    {foreach item=LISTVIEW_ENTRY from=$TEMPLATES name=listview}
                        <tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY->get('id')}'
                            id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">

                            {foreach item=LISTVIEW_HEADER key=COLUMNNAME from=$LISTVIEW_HEADERS}
                                <td class="{if $COLUMNNAME == 'filename'}listViewEntryValue{/if} {$WIDTHTYPE}" nowrap data-column="{$COLUMNNAME}">
                                    {if $COLUMNNAME == 'filename'}
                                        <a href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY->get('id')}'>
                                            {vtranslate($LISTVIEW_ENTRY->get($COLUMNNAME), $LISTVIEW_ENTRY->get($COLUMNNAME))}
                                        </a>
                                    {elseif $COLUMNNAME == 'module'}
                                        {vtranslate($LISTVIEW_ENTRY->get($COLUMNNAME), $LISTVIEW_ENTRY->get($COLUMNNAME))}
                                    {elseif $COLUMNNAME == 'is_active'}
                                        {if $LISTVIEW_ENTRY->get($COLUMNNAME) eq 1}Active{else}Inactive{/if}
                                    {else}
                                        {$LISTVIEW_ENTRY->get($COLUMNNAME)}
                                    {/if}
                                </td>

                                {if $LISTVIEW_HEADER@last}
                                    <td nowrap class="{$WIDTHTYPE}">
                                        <div class="actions pull-right">
                                            <span class="actionImages">
                                                <a href="index.php?module={$MODULE}&action=PDFHandler&mode=duplicate&record={$LISTVIEW_ENTRY->get('id')}">
                                                    <i title="{vtranslate('LBL_DUPLICATE', $MODULE)}" class="fa fa-files-o alignMiddle"></i>
                                                </a>
                                                &nbsp;
                                                <a href="index.php?module={$MODULE}&action=PDFHandler&mode=download&record={$LISTVIEW_ENTRY->get('id')}">
                                                    <i title="{vtranslate('LBL_DOWNLOAD', $MODULE)}" class="icon-download alignMiddle"></i>
                                                </a>
                                                &nbsp;
                                                <a href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY->get('id')}'>
                                                    <i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i>
                                                </a>
                                                &nbsp;
                                                <a href="index.php?module={$MODULE}&action=ActionAjax&mode=delete&record={$LISTVIEW_ENTRY->get('id')}"
                                                   onclick="return confirm('Are you sure you want to delete template?')">
                                                    <i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/foreach}
                    </tbody>
                </table>

                <!--added this div for Temporarily -->
                {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                    <table class="emptyRecordsDiv">
                        <tbody>
                        <tr>
                            <td>
                                {assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
                                {vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}
                                .{if $IS_MODULE_EDITABLE} {vtranslate('LBL_CREATE')} <a
                                        href="index.php?module={$MODULE}&view=Edit">{vtranslate($SINGLE_MODULE, $MODULE)}</a>{/if}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                {/if}
            </div>
        </div>
    </div>
{/strip}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

