/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/* Auto load when on QuotingTool module */

/** @class QuotingTool */
Vtiger.Class("QuotingTool", {}, {
    /**
     * Fn - registerInstallEvents
     */
    registerInstallEvents: function () {
        jQuery(document).on('click', '.quoting_tool-downloadLib', function () {
            app.helper.showProgress(app.vtranslate('Downloading...'));
            //
            var params = {
                type: 'GET',
                url: 'index.php',
                dataType: 'json',
                data: {
                    module: 'QuotingTool',
                    action: 'Install',
                    mode: 'downloadMPDF'
                }
            };
            app.request.post(params).then(
                function (err, data) {
                    app.helper.hideProgress();

                    if (err === null) {
                        window.location.href = 'index.php?module=QuotingTool&view=List';
                    } else {
                        console.log(err);
                    }
                }
            );
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
