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
