/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'en';
    // config.uiColor = '#AADC6E';
    config.skin = 'office2013';
    config.title = false;
    //config.autoParagraph = false;
    //config.fillEmptyBlocks = false;
    //config.enterMode = CKEDITOR.ENTER_BR;
    //config.shiftEnterMode = CKEDITOR.ENTER_P;
    //config.resize_enabled = true;
    config.toolbarCanCollapse = true;
    config.allowedContent = true;
    // config.extraPlugins = 'sharedspace';
    //config.removePlugins = 'magicline,scayt,menubutton,contextmenu';

    config.toolbar = [
        {name: 'document', items: ['Source']},
        {
            name: 'clipboard',
            groups: ['clipboard', 'undo'],
            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
        },
        {
            name: 'editing',
            groups: ['find', 'selection', 'spellchecker'],
            items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt']
        },
        {
            name: 'paragraph',
            groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
            items: ['Blockquote', 'CreateDiv', '-', 'BidiLtr', 'BidiRtl']
        },
        {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
        {name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak']},
        {name: 'about', items: ['About']},
        '/',
        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
        {
            name: 'basicstyles',
            groups: ['basicstyles', 'cleanup'],
            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent']
        },

        {name: 'colors', items: ['TextColor', 'BGColor']},
        {name: 'tools', items: ['Maximize', 'ShowBlocks']},
        {name: 'others', items: ['-']}
    ];

    //config.extraAllowedContent = 'p(*)[*]{*};div(*)[*]{*};li(*)[*]{*};ul(*)[*]{*}';
    //CKEDITOR.dtd.$removeEmpty.i = 0;
    //CKEDITOR.dtd.$removeEmpty['i'] = false
    config.protectedSource.push(/<code>[\s\S]*?<\/code>/gi); // Code tags

    // Integrate KCfinder (vtiger core) to CKEditor
    /** @link http://kcfinder.sunhater.com/integrate */
    config.filebrowserBrowseUrl = 'kcfinder/browse.php?opener=ckeditor&type=files';
    config.filebrowserImageBrowseUrl = 'kcfinder/browse.php?opener=ckeditor&type=images';
    config.filebrowserFlashBrowseUrl = 'kcfinder/browse.php?opener=ckeditor&type=flash';
    config.filebrowserUploadUrl = 'kcfinder/upload.php?opener=ckeditor&type=files';
    config.filebrowserImageUploadUrl = 'kcfinder/upload.php?opener=ckeditor&type=images';
    config.filebrowserFlashUploadUrl = 'kcfinder/upload.php?opener=ckeditor&type=flash';

    // Add custom fonts
    if (typeof mpdf != 'undefined') {
        if (mpdf && mpdf.CKEditorConfig && mpdf.CKEditorConfig.custom_fonts) {
            // Define changes to default configuration here:
            if (mpdf.CKEditorConfig.custom_fonts.contentsCss) {
                /**
                 * Check if a variable is a string
                 * @link http://stackoverflow.com/questions/4059147/check-if-a-variable-is-a-string#answer-9436948
                 */
                if (typeof config.contentsCss === 'string' || config.contentsCss instanceof String) {
                    config.contentsCss = [config.contentsCss];
                }

                config.contentsCss = config.contentsCss.concat(mpdf.CKEditorConfig.custom_fonts.contentsCss);
            }

            //the next line add the new font to the combobox in CKEditor
            //config.font_names = '<Cutsom Font Name>/<YourFontName>;' + config.font_names;
            if (mpdf.CKEditorConfig.custom_fonts.font_names) {
                config.font_names += ';' + mpdf.CKEditorConfig.custom_fonts.font_names;
            }
        }
    }

};

/**
 * Custom default dialog values
 * @link http://docs.cksource.com/CKEditor_3.x/Howto/Default_Field_Values
 */
CKEDITOR.on('dialogDefinition', function (ev) {
    // Take the dialog name and its definition from the event data.
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    // Get a reference to the "Info" tab.
    var infoTab = dialogDefinition.getContents('info');

    // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).
    if (dialogName == 'link') {
        // Set the default value for the URL field.
        var url = infoTab.get('url');
        url['default'] = 'www.example.com';
    } else if (dialogName == 'image') {
        //var txtWidth = infoTab.get('txtWidth');
        //txtWidth['default'] = contentWidth;
        var txtHSpace = infoTab.get('txtHSpace');
        txtHSpace['default'] = 10;
        var txtVSpace = infoTab.get('txtVSpace');
        txtVSpace['default'] = 2;
    } else if (dialogName == 'table') {
        // Default value
        var txtWidth = infoTab.get('txtWidth');
        txtWidth['default'] = '100%';
        var txtCellSpace = infoTab.get('txtCellSpace');
        txtCellSpace['default'] = '0';
        var txtCellPad = infoTab.get('txtCellPad');
        txtCellPad['default'] = '0';

        // Customize properties
        for (var i in dialogDefinition.contents) {
            if (!dialogDefinition.contents.hasOwnProperty(i)) {
                continue;
            }

            var contents = dialogDefinition.contents[i];
            if (contents.id == "info") {
                var eLength = contents.elements.length;
                contents.elements.splice(eLength, 0, {
                    type: 'checkbox',
                    id: 'info_header',
                    label: 'Show headers'
                });
            }
        }
    }
});
