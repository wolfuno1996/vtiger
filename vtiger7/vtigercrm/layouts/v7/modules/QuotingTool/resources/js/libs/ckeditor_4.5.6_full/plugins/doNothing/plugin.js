/**
 * @link http://ckeditor.com/forums/CKEditor-3.x/Disable-Enter-Key
 */

(function () {

    'use strict';

    var doNothingCmd =
    {
        exec: function (editor) {
            return;
        }
    };
    var pluginName = 'doNothing';
    CKEDITOR.plugins.add(pluginName,
        {
            init: function (editor) {
                editor.addCommand(pluginName, doNothingCmd);
            }
        });
})();
