/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/* Auto load when on QuotingTool module */

/** @class QuotingTool */
jQuery.Class("QuotingTool", {}, {
    /**
     * Fn - registerInstallEvents
     */
    registerInstallEvents: function () {
        jQuery(document).on('click', '.quoting_tool-downloadLib', function () {
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': 'Downloading...',
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });
            //
            var actionParams = {
                type: 'GET',
                url: 'index.php',
                dataType: 'json',
                data: {
                    module: 'QuotingTool',
                    action: 'Install',
                    mode: 'downloadMPDF'
                }
            };
            AppConnector.request(actionParams).then(
                function (response) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});

                    if (response.success) {
                        window.location.href = 'index.php?module=QuotingTool&view=List';
                    }
                },
                function (error) {
                    console.log('error =', error);
                    alert(error);
                });
        });
    },

    /**
     * Fn - registerEvents
     */
    registerEvents: function () {
        var thisInstance = this;
        thisInstance.registerInstallEvents();
    }
});

jQuery(document).ready(function () {
    var instance = new QuotingTool();
    instance.registerEvents();

});
