/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

(function () {
    'use strict';

    var models = angular.module('AppModels');

    models.factory('Template', function () {
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

                app.request.post({data: params}).then(
                    function (err, data) {
                        var response = {};

                        if (err === null) {
                            response.success = true;
                            response.result = data;
                        } else {
                            console.log(err);
                            response.success = false;
                            response.error = err;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    }
                );
            }
        };
    });

    models.factory('TemplateSetting', function () {
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

                app.request.post({data: params}).then(
                    function (err, data) {
                        var response = {};

                        if (err === null) {
                            response.success = true;
                            response.result = data;
                        } else {
                            console.log(err);
                            response.success = false;
                            response.error = err;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    }
                );
            }
        };
    });
    models.factory('TemplateProposal', function () {
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

                app.request.post({data: params}).then(
                    function (err, data) {
                        var response = {};

                        if (err === null) {
                            response.success = true;
                            response.result = data;
                        } else {
                            console.log(err);
                            response.success = false;
                            response.error = err;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    }
                );
            }
        };
    });

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

                app.request.post({data: params}).then(
                    function (err, data) {
                        var response = {};

                        if (err === null) {
                            response.success = true;
                            response.result = data;
                        } else {
                            console.log(err);
                            response.success = false;
                            response.error = err;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
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

                app.request.post({data: params}).then(
                    function (err, data) {
                        var response = {};

                        if (err === null) {
                            response.success = true;
                            response.result = data;
                        } else {
                            console.log(err);
                            response.success = false;
                            response.error = err;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
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

                app.request.post({data: params}).then(
                    function (err, data) {
                        var response = {};

                        if (err === null) {
                            response.success = true;
                            response.result = data;
                        } else {
                            console.log(err);
                            response.success = false;
                            response.error = err;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    }
                );
            }
        };
    });

})();
