/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Potentials_Edit_Js",{ },{
    
    
    /**
	 * Function to get popup params
	**/
    getPopUpParams : function(container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);

        if(sourceFieldElement.attr('name') == 'contact_id' ) {
        
            var form = this.getForm();
            var parentIdElement  = form.find('[name="related_to"]');
        
            if(parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
                var closestContainer = parentIdElement.closest('td');
                params['related_parent_id'] = parentIdElement.val();
                params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
            }
        }
     
        return params;
    },

    updateProbability : function(container) {
        $('[name="probability"]').readOnly = true;
        var thisInstance = this;
        jQuery("select[name='sales_stage']", container).change(function(data){
            var salesStage = jQuery("select[name='sales_stage']").val();
            thisInstance.autoUpdateProbabilty(data, container,salesStage);
        });

    },


    autoUpdateProbabilty :  function(data, container,salesStage) {
        var thisInstance = this;

        var salesStage = salesStage;
        switch (salesStage){
            case 'Initial phase' : {
                jQuery("input[name='probability']").val(10);
                break;
            }
            case 'Product Explanation' : {
                jQuery("input[name='probability']").val(20);
                break;
            }
            case 'Detailed Product Info' : {
                jQuery("input[name='probability']").val(40);
                break;
            }
            case 'Demonstration' : {
                jQuery("input[name='probability']").val(60);
                break;
            }
            case 'Proposal / Quote' : {
                jQuery("input[name='probability']").val(70);
                break;
            }
            case 'Negotiation' : {
                jQuery("input[name='probability']").val(30);
                break;
            }
            default:{
                jQuery("input[name='probability']").val(0);
                break;
            }
        }
        console.log(salesStage);




    },


    registerEvents: function(container){
        this._super();
		this.updateProbability(container);
    }

});
