/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

(function () {
    'use strict';

    var i18n = angular.module('i18n', ['pascalprecht.translate']);

    i18n.factory('TableHeaderBuilder', function ($rootScope, $filter) {
        var doBuild = function (object, headers) {
            headers.forEach(function (el) {
                object[el] = $filter('translate')(el);
            })
        };

        return {
            build: function (scope, headers, field) {
                var f = field || 'header';
                scope[f] = {};

                doBuild(scope[f], headers);

                var unbind = $rootScope.$on('$translateChangeSuccess', function () {
                    doBuild(scope[f], headers);
                });

                scope.$on('$destroy', unbind);
            }
        }
    });
    
})();