/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

(function () {
    'use strict';

    var en = angular.module('AppI18N');

    en.config(function ($translateProvider) {
        $translateProvider.translations('en', AppHelper.getLanguageString());
    });

})();
