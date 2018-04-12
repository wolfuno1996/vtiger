/**
 * Created by SonTT on 5/4/2016.
 */

(function () {
    'use strict';

    var pluginName = 'quotingtool';
    CKEDITOR.plugins.add(pluginName, {
        init: function (editor) {
            // console.log('editor =', editor);
            var btnDuplicate = 'QuotingTool_Duplicate';
            editor.ui.addButton(btnDuplicate, {
                label: 'Duplicate',
                command: btnDuplicate,
                icon: CKEDITOR.plugins.getPath(pluginName) + 'icons/duplicate.png'
            });

            var cmd = editor.addCommand(btnDuplicate, {exec: duplicate});
            // console.log('cmd =', cmd);
        }
    });

    /**
     * Fn - duplicate
     * @param editor
     */
    function duplicate(editor) {
        // console.log('editor =', editor);

        var thisElement = editor.ui.contentsElement.$;
        var container = jQuery(thisElement.closest('.content-container'));
        // var newElement = container.clone();
        // container.after(newElement);

        // console.log('window.QuotingTool_leak =', window.QuotingTool_leak);
        var needleBlock = null;
        // var headingBlock = window.QuotingTool_leak.blocks.heading;
        // console.log('headingBlock =', headingBlock);
        var containerTemplate = container.data('id');
        var block = null;
        for (var b in window.QuotingTool_leak.blocks) {
            block = window.QuotingTool_leak.blocks[b];
            if (block.template == containerTemplate) {
                needleBlock = block;
                break;
            }
        }

        if (needleBlock) {
            window.QuotingTool_leak.addBlock(needleBlock, 'after', container);
        }

    }
})();
