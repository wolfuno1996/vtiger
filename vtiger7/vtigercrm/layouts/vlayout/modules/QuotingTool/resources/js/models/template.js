(function () {
    'use strict';

    var models = angular.module('AppModels');

    models.factory('Template', [function () {
        var baseUri = 'index.php?';

        return {
            /**
             *
             * @param uri
             * @param params
             * @returns {*}
             */
            get: function (uri, params) {
                var q = $.Deferred();
                uri = baseUri + uri;

                $.get(uri, params).done(function (result) {
                    q.resolve(result);
                }).fail(function (response) {
                    q.reject(response);
                });

                return q.promise();
            },

            /**
             *
             * @param uri
             * @param params
             * @returns {*}
             */
            post: function (uri, params) {
                uri = baseUri + uri;

                var q = $.Deferred();
                $.post(uri, params).done(function (result) {
                    q.resolve(result);
                }).fail(function (response) {
                    q.reject(response);
                });

                return q.promise();
            },

            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            save: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'save'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                    }
                );
            }
        };
    }]);

    models.factory('TemplateSetting', [function () {
        return {
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            save: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'save_setting'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                    }
                );
            }
        };
    }]);

    models.factory('TemplateProposal', [function () {
        return {
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            save: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'save_proposal'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                    }
                );
            }
        };
    }]);

    models.factory('TemplateHistory', function () {
        return {
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            getHistories: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'getHistories'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                    }
                );
            },
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            getHistory: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'getHistory'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                    }
                );
            },
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            removeHistories: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'removeHistories'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                    }
                );
            }
        };
    });

})();
