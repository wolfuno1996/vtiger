(function () {
    'use strict';

    var constants = angular.module('AppConstants', []);

    constants.constant({
        AppConstants: {
            ORDER: {
                ASC: 'asc',
                DESC: 'desc'
            },
            FOCUS_TYPE: {
                INPUT: 'input',
                TEXTAREA: 'textarea',
                CKEDITOR: 'CKEditor',
                CONTENTEDITABLE: 'contenteditable'
            },
            COMPONENT_TYPE: {
                BLOCK: 'block',
                WIDGET: 'widget'
            },
            TABLE_BLOCK: {
                THEMES: [
                    {
                        id: 1,
                        name: 'Theme 1',
                        image: 'layouts/vlayout/modules/QuotingTool/resources/img/icons/QuotingTool-table-1.png',
                        settings: {
                            caption: {
                                enable: false,
                                text: ''
                            },
                            thead: {
                                enable: true,
                                style: {
                                    'background-color': '#000000',
                                    'color': '#FFFFFF'
                                }
                            },
                            tbody: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#D0CCC7'
                                    }
                                }
                            },
                            tfoot: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                }
                            },
                            cell: {
                                style: {
                                    border: '1px solid #666460'
                                }
                            },
                            style: {
                                border: '1px solid #666460'
                            }
                        }
                    },
                    {
                        id: 2,
                        name: 'Theme 2',
                        image: 'layouts/vlayout/modules/QuotingTool/resources/img/icons/QuotingTool-table-2.png',
                        settings: {
                            caption: {
                                enable: false,
                                text: ''
                            },
                            thead: {
                                enable: true,
                                style: {
                                    'background-color': '#389BD2',
                                    'color': '#FFFFFF'
                                }
                            },
                            tbody: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#DFEBF5'
                                    }
                                }
                            },
                            tfoot: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                }
                            },
                            cell: {
                                style: {
                                    border: '1px solid #8EC4E3'
                                }
                            },
                            style: {
                                border: '1px solid #8EC4E3'
                            }
                        }
                    },
                    {
                        id: 3,
                        name: 'Theme 3',
                        image: 'layouts/vlayout/modules/QuotingTool/resources/img/icons/QuotingTool-table-3.png',
                        settings: {
                            caption: {
                                enable: false,
                                text: ''
                            },
                            thead: {
                                enable: true,
                                style: {
                                    'background-color': '#FF7322',
                                    'color': '#FFFFFF'
                                }
                            },
                            tbody: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFE4D0'
                                    }
                                }
                            },
                            tfoot: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                }
                            },
                            cell: {
                                style: {
                                    border: '1px solid #FFAB77'
                                }
                            },
                            style: {
                                border: '1px solid #FFAB77'
                            }
                        }
                    },
                    {
                        id: 4,
                        name: 'Theme 4',
                        image: 'layouts/vlayout/modules/QuotingTool/resources/img/icons/QuotingTool-table-4.png',
                        settings: {
                            caption: {
                                enable: false,
                                text: ''
                            },
                            thead: {
                                enable: true,
                                style: {
                                    'background-color': '#A7A39E',
                                    'color': '#FFFFFF'
                                }
                            },
                            tbody: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#EEEDEB'
                                    }
                                }
                            },
                            tfoot: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                }
                            },
                            cell: {
                                style: {
                                    border: '1px solid #CDC9C4'
                                }
                            },
                            style: {
                                border: '1px solid #CDC9C4'
                            }
                        }
                    },
                    {
                        id: 5,
                        name: 'Theme 5',
                        image: 'layouts/vlayout/modules/QuotingTool/resources/img/icons/QuotingTool-table-5.png',
                        settings: {
                            caption: {
                                enable: false,
                                text: ''
                            },
                            thead: {
                                enable: true,
                                style: {
                                    'background-color': '#FFBE00',
                                    'color': '#FFFFFF'
                                }
                            },
                            tbody: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFF1C7'
                                    }
                                }
                            },
                            tfoot: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                }
                            },
                            cell: {
                                style: {
                                    border: '1px solid #FFD85F'
                                }
                            },
                            style: {
                                border: '1px solid #FFD85F'
                            }
                        }
                    },
                    // {
                    //     id: 6,
                    //     name: 'Theme 6',
                    //     image: 'layouts/vlayout/modules/QuotingTool/resources/img/icons/QuotingTool-table-6.png'
                    // },
                    {
                        id: 7,
                        name: 'Theme 7',
                        image: 'layouts/vlayout/modules/QuotingTool/resources/img/icons/QuotingTool-table-7.png',
                        settings: {
                            caption: {
                                enable: false,
                                text: ''
                            },
                            thead: {
                                enable: true,
                                style: {
                                    'background-color': '#58AD4B',
                                    'color': '#FFFFFF'
                                }
                            },
                            tbody: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#E2F0D7'
                                    }
                                }
                            },
                            tfoot: {
                                enable: true,
                                even: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                },
                                odd: {
                                    style: {
                                        'background-color': '#FFFFFF'
                                    }
                                }
                            },
                            cell: {
                                style: {
                                    border: '1px solid #9ED288'
                                }
                            },
                            style: {
                                border: '1px solid #9ED288'
                            }
                        }
                    }
                ]
            }
        }
    });

})();
