(function () {
    'use strict';

    var en = angular.module('AppI18N');

    en.config(function ($translateProvider) {
        $translateProvider.translations('en', AppHelper.getLanguageString());
    });

})();
