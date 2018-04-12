/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Potentials_Detail_Js",{},{
	
	detailViewRecentContactsLabel : 'Contacts',
	detailViewRecentProductsTabLabel : 'Products',
	
	/**
	 * Function which will register all the events
	 */
	registerEventConvertProject : function () {
        $('[name="convertProject"]').click(function(data){
        	var source_record = app.getRecordId();
        	var params = {
        		module: 'Project',
        		action: 'ActionsAjax',
        		mode: 'CreateProject',
				source_record : source_record
			};
			AppConnector.request(params).then(
				function (data) {
					if (data.result != ''){
                        window.location.href = data.result;
                    }

				});
        });

    },
    registerEvents : function() {
		this._super();
		var detailContentsHolder = this.getContentHolder();
		var thisInstance = this;
		
		detailContentsHolder.on('click','.moreRecentContacts', function(){
			var recentContactsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentContactsLabel);
			recentContactsTab.trigger('click');
		});
		
		detailContentsHolder.on('click','.moreRecentProducts', function(){
			var recentProductsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentProductsTabLabel);
			recentProductsTab.trigger('click');
		});
        this.registerEventConvertProject();
	}
})