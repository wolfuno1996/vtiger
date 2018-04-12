(function () {
    // get path of directory ckeditor
    var basePath = CKEDITOR.basePath;
    basePath = basePath.substr(0, basePath.indexOf("ckeditor/"));
    var externalPluginPath = basePath + '../../layouts/v7/modules/QuotingTool/resources/js/libs/ckeditor_4.5.6_full/plugins/';

    // load external plugins
    var externalPlugins = ['sharedspace','doNothing','youtube','sourcedialog','quotingtool','confighelper'];
    var plugin = null;

    for (var i = 0; i < externalPlugins.length; i++) {
        plugin = externalPlugins[i];
        // console.log('externalPluginPath + plugin =', externalPluginPath + plugin);
        CKEDITOR.plugins.addExternal(plugin, externalPluginPath + plugin + '/', 'plugin.js');
    }
})();
