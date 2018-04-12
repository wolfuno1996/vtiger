/* ********************************************************************************
 * The content of this file is subject to the Signed Record ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/** @class SignedRecord */
Vtiger.Class("SignedRecord", {}, {
    /**
     * Fn - registerConvertFieldEvents
     */
    registerConvertFieldEvents: function () {

        var relatedModule = jQuery('[name="relatedModuleName"]');
        if (relatedModule && relatedModule.length > 0) {
            var relatedModuleName = relatedModule.val();

            if (relatedModuleName == 'SignedRecord') {
                var listViewEntriesTable = jQuery('.listViewEntriesTable');
                var itemRow = listViewEntriesTable.find('[data-recordurl^="index.php?module=SignedRecord"]');
                var dataFieldTypeCell = itemRow.find('[data-field-type="documentsFileUpload"]');

                if (!dataFieldTypeCell || dataFieldTypeCell.length == 0) {
                    return;
                }

                var row = null;
                var rowId = null;
                var cell = null;
                itemRow.each(function () {
                    row = $(this);
                    rowId = row.data('id');

                    dataFieldTypeCell.each(function () {
                        cell = $(this);
                        var dataFieldTypeText = cell.text();
                        var filename = dataFieldTypeText.split('/').pop();
                        cell.html('<a href="index.php?module=SignedRecord&action=DownloadFile&record=' + rowId + '">' + filename + '</a>');
                    });
                });
            }
        }
    },

    /**
     * Function returns the record id
     */
    getRecordId: function () {
        var view = jQuery('[name="view"]').val();
        var recordId;
        if (view == "Edit") {
            recordId = jQuery('[name="record"]').val();
        } else if (view == "Detail") {
            recordId = jQuery('#recordId').val();
        }
        return recordId;
    },

    /**
     * Fn - registerEvents
     */
    registerEvents: function () {
        var thisInstance = this;
        thisInstance.registerConvertFieldEvents();

        var view = _META.view;
        var record = thisInstance.getRecordId();

        if (view == "Detail" && record) {
            var detailContainer = jQuery('.detailViewInfo');
            jQuery('.related', detailContainer).on('click', 'li', function () {
                var thisFocus = jQuery(this);
                var dataUrl = thisFocus.data('url');

                if (!dataUrl) {
                    return;
                }

                var urlParts = dataUrl.split('&');
                if ((jQuery.inArray('relatedModule=SignedRecord', urlParts) < 0)
                    || (jQuery.inArray('mode=showRelatedList', urlParts) < 0)) {
                    return;
                }

                var flag = false;
                var listener = setInterval(function () {
                    var relatedModule = jQuery('[name="relatedModuleName"]');
                    if (relatedModule && relatedModule.length > 0) {
                        thisInstance.registerConvertFieldEvents();
                        flag = true;
                        clearInterval(listener);
                    }
                }, 100);
            });
        }
    }
});

jQuery(document).ready(function () {
    var instance = new SignedRecord();
    instance.registerEvents();
});
