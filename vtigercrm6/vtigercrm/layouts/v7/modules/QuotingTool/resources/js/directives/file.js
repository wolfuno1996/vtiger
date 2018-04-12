/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

(function () {
    'use strict';

    var directives = angular.module('AppDirectives');

    directives.directive('fullPath', function ($rootScope) {
        return {
            link: function (scope, element, attributes) {
                scope.$watch('app.config', function () {
                    var fullPathUrl = $rootScope.app.config.base;

                    if (element[0].tagName === 'A') {
                        attributes.$set('href', fullPathUrl + attributes.href);
                    } else {
                        attributes.$set('src', fullPathUrl + attributes.src);
                    }
                });
            }
        }
    });

})();
