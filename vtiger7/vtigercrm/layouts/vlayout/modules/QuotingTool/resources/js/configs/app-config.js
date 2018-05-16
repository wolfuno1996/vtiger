(function () {
    'use strict';

    var configs = angular.module('AppConfig', []);

    configs.constant({
        GlobalConfig: {
            DOMAIN: 'index.php',
            BASE: '/',
            XDEBUG: 'XDEBUG_SESSION_START=PHPSTORM',
            MODULE_NAME: 'QuotingTool',
            APP_NAME: 'Quoting Tool',
            PUBLIC_KEY: 'XP6RLEBJ2ilY3gJvuDvr1420189516434',
            DEBUG_MODE: 1,
            DEBUG_KEY: 'QuotingTool',
            DEFAULT_BACKGROUND_IMAGE: 'layouts/vlayout/modules/QuotingTool/resources/img/placeholder.png',
            INVENTORY_MODULES: ['Quotes', 'PurchaseOrder', 'SalesOrder', 'Invoice'],
            PRODUCT_MODULES: ['Products', 'Services']
        },
        AppToolbar: {
            menu: {},
            tokens: {
                product_blocks: [
                    {
                        id: 0,
                        name: 'productbloc_start',
                        token: '#PRODUCTBLOC_START#',
                        label: 'Block start'
                    },
                    {
                        id: 0,
                        name: 'productbloc_end',
                        token: '#PRODUCTBLOC_END#',
                        label: 'Block end'
                    }
                ],
                related_blocks: [
                    {
                        id: 0,
                        name: 'relatedblock_start',
                        token: '#RELATEDBLOCK_START#',
                        label: 'Related block start'
                    },
                    {
                        id: 0,
                        name: 'relatedblock_end',
                        token: '#RELATEDBLOCK_END#',
                        label: 'Related block end'
                    }
                ]
            },
            base_editor: {
                settings: {
                    toolbar: [
                        {name: 'clipboard', items: ['Undo', 'Redo']},
                        {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                        {name: 'links', items: ['Link', 'Unlink']},
                        {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                        {name: 'about', items: ['About']}
                    ],
                    removePlugins: 'magicline,scayt',
                    // To enable source code editing in a dialog window, inline editors require the "sourcedialog" plugin.
                    extraPlugins: 'sharedspace,doNothing,youtube,sourcedialog,quotingtool,confighelper',
                    //removePlugins: 'floatingspace,maximize,resize',
                    sharedSpaces: {
                        top: 'quoting_tool-header',
                        bottom: 'quoting_tool-footer'
                    }
                }
            },
            blocks: {
                init: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/init.html',
                    settings: {},
                    layout: {
                        id: 'init',
                        name: '',
                        icon: '',
                        enable_setting: false,
                        enable_remove: false,
                        enable_move: false
                    }
                },
                heading: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/heading.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_heading.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize', 'Format']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']
                            },
                            {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                            {name: 'about', items: ['About']}
                        ],
                        format_tags: 'h1;h2;h3;h4;h5;h6',
                        keystrokes: [[13 /*Enter*/, 'doNothing']]
                    },
                    layout: {
                        id: 'heading',
                        name: 'Heading',
                        icon: 'icon--block-heading',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                text: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/text.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'insert', items: ['Image', 'Youtube', 'Table']},
                            {name: 'links', items: ['Link', 'Unlink']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                //groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                                //items: ['Blockquote', 'CreateDiv', '-', 'BidiLtr', 'BidiRtl']
                                items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                    'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'text',
                        name: 'Text',
                        icon: 'icon--block-text',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                image: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/image.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_image.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'image',
                        name: 'Image',
                        icon: 'icon--block-image',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                video: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/video.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'video',
                        name: 'Video',
                        icon: 'icon--block-video',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                table: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/table.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'insert', items: ['Image', 'Table']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                    'JustifyRight', 'JustifyBlock']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'table',
                        name: 'Table',
                        icon: 'icon--block-table',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                pricing_table: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/pricing_table.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_pricing_table.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'insert', items: ['Image', 'Table']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                    'JustifyRight', 'JustifyBlock']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'pricing_table',
                        name: 'Pricing table',
                        icon: 'icon--block-pricingtable',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                pricing_table_idc: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/pricing_table_idc.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_pricing_table_idc.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'insert', items: ['Image', 'Table']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                    'JustifyRight', 'JustifyBlock']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'pricing_table_idc',
                        name: 'Pricing table (IDC)',
                        icon: 'icon--block-pricingtable',
                        icon_supplement: 'IDC',
                        icon_helptext: 'i',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                related_module: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/related_module.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_related_module.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'insert', items: ['Image', 'Table']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                    'JustifyRight', 'JustifyBlock']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'related_module',
                        name: 'Related module',
                        icon: 'icon--block-pricingtable',
                        icon_helptext: 'i',
                        icon_supplement: 'REL',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                toc: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/toc.html',
                    settings: {
                        toolbar: [
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']}
                        ]
                    },
                    layout: {
                        id: 'toc',
                        name: 'Toc',
                        icon: 'icon--block-toc',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                page_break: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/page_break.html',
                    settings: {
                        toolbar: [
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']}
                        ]
                    },
                    layout: {
                        id: 'page_break',
                        name: 'Page break',
                        icon: 'icon--block-pagebreak',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                line_break: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/line_break.html',
                    settings: {
                        toolbar: [
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']}
                        ]
                    },
                    layout: {
                        id: 'line_break',
                        name: 'Line break',
                        icon: 'fa fa-level-down force-fa',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                line_hr: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/line_hr.html',
                    settings: {
                        toolbar: [
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']}
                        ]
                    },
                    layout: {
                        id: 'line_hr',
                        name: 'Line',
                        icon: 'fa-arrows-h force-fa',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                cover_page: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/cover_page.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline']},
                            {
                                name: 'paragraph',
                                items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'cover_page',
                        name: 'Cover page',
                        icon: 'icon--block-cover',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                page_header: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/page_header.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'insert', items: ['Image']},
                            {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'page_header',
                        name: 'Page header',
                        icon: 'icon--block-header',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                page_footer: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/page_footer.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'insert', items: ['Image']},
                            {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'page_footer',
                        name: 'Page footer',
                        icon: 'icon--block-footer',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                tbl_one_column: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/tbl_one_column.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_tbl_one_column.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'insert', items: ['Image', 'Table']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                    'JustifyRight', 'JustifyBlock']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'tbl_one_column',
                        name: 'Table(1 col)',
                        icon: 'icon--block-table',
                        icon_supplement: '1',
                        icon_helptext: 'i',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                tbl_two_columns: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/tbl_two_columns.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_tbl_two_columns.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'tools', items: ['Sourcedialog', 'QuotingTool_Duplicate']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                            {name: 'insert', items: ['Image', 'Table']},
                            {
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                    'JustifyRight', 'JustifyBlock']
                            },
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'tbl_two_columns',
                        name: 'Table(2 cols)',
                        icon: 'icon--block-table',
                        icon_supplement: '2',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
            },
            widgets: {
                init: {
                    template: '',
                    settings: {},
                    layout: {
                        id: 'init',
                        name: '',
                        icon: '',
                        enable_setting: false,
                        enable_remove: false,
                        enable_move: false
                    }
                },
                text_field: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/widgets/text_field.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_text_field.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'about', items: ['About']}
                        ],
                        enterMode: CKEDITOR.ENTER_BR
                    },
                    layout: {
                        id: 'text_field',
                        name: 'Textfield',
                        icon: 'icon--text-field',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                signature: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/widgets/signature.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_signature.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'signature',
                        name: 'Signature',
                        icon: 'icon--sign',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                initials: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/widgets/initials.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_initials.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'initials',
                        name: 'Initials',
                        icon: 'icon--initials',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                date: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/widgets/date.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_date.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'date',
                        name: 'Date',
                        icon: 'icon--date',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                datetime: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/widgets/datetime.html',
                    setting_template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/settings_datetime.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'datetime',
                        name: 'Date Time',
                        icon: 'icon--date',
                        enable_setting: true,
                        enable_remove: true,
                        enable_move: true
                    }
                },
                checkbox: {
                    template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/widgets/checkbox.html',
                    settings: {
                        toolbar: [
                            {name: 'clipboard', items: ['Undo', 'Redo']},
                            {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                            {name: 'colors', items: ['TextColor', 'BGColor']},
                            {name: 'links', items: ['Link', 'Unlink']},
                            {name: 'about', items: ['About']}
                        ]
                    },
                    layout: {
                        id: 'checkbox',
                        name: 'Checkbox',
                        icon: 'icon--checkbox',
                        enable_setting: false,
                        enable_remove: true,
                        enable_move: true
                    }
                }
            },
            source_editor: {
                settings: {
                    toolbar: [
                        {
                            name: 'document',
                            groups: ['mode', 'tools'],
                            items: ['Source', 'Maximize']
                        },
                        {name: 'clipboard', items: ['Undo', 'Redo']},
                        {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                        {name: 'colors', items: ['TextColor', 'BGColor']},
                        {name: 'insert', items: ['Image']},
                        {name: 'links', items: ['Link', 'Unlink']},
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl']
                        },
                        {name: 'about', items: ['About']}
                    ]
                }
            },
            email_template: {
                template: 'layouts/vlayout/modules/QuotingTool/resources/js/views/popups/email_template.html',
                settings: {
                    fullPage: true,
                    height: 320,
                    toolbar: [
                        {name: 'clipboard', items: ['Undo', 'Redo']},
                        {name: 'tools', items: ['Source', 'Maximize', 'Preview']},
                        {
                            name: 'editing',
                            groups: ['find', 'selection', 'spellchecker'],
                            items: ['Find', 'Replace', 'SelectAll', 'Scayt']
                        },
                        /*'/',*/
                        {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                        {name: 'colors', items: ['TextColor', 'BGColor']},
                        '/',
                        {name: 'insert', items: ['Image', 'Table']},
                        {name: 'links', items: ['Link', 'Unlink']},
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                                'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl']
                        },
                        {name: 'about', items: ['About']}
                    ]
                },
                css: {
                    'width': '796px'
                }
            }
        },
        SECTIONS: {
            // tabs
            BLOCKS: 'blocks',
            WIDGETS: 'widgets',
            TOKENS: 'tokens',
            DECISION: 'decision',
            THEMES: 'themes',
            HISTORIES: 'histories',
            // Content tab
            CONTENT_PROPERTIES: 'properties',
            CONTENT_PRODUCT_BLOCK: 'product_block',
            CONTENT_RELATED_BLOCK: 'related_block',
            CONTENT_OTHERS: 'other_information',
            // Properties tab
            PROPERTIES_BASIC: 'basic',
            PROPERTIES_ADVANCE: 'advance',
            PROPERTIES_OTHERS: 'others',
            // General tab
            GENERAL_ACCEPT: 'proposal_accept',
            GENERAL_DECLINE: 'proposal_decline',
            GENERAL_BACKGROUND: 'themes_background_image',
            GENERAL_OTHERS: 'proposal_others',
            GENERAL_ANWIDGET: 'proposal_anwidget',
            GENERAL_CREATENEWRECORDS: 'proposal_createnewrecords',
            // History tab
            HISTORY_TAB1: 'history_tab1'
        }

    });

})();
