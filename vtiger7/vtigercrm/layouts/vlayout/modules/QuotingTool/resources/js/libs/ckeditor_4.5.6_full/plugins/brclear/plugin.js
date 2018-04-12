/**
* BrClear : plugin pour CKEditor
 */


(function() {
	var brclearCmd = {
		canUndo: true,
		exec: function( editor ) {
			var brcl = editor.document.createElement( 'br', { attributes: {style : "clear:both;"} });
			editor.insertElement( brcl );
		},

		html:'<br style="clear:both;" />'
		
	};


	CKEDITOR.plugins.add('brclear',{ 
		init : function(editor) {
			editor.addCommand('brclear', brclearCmd );
			editor.ui.addButton && editor.ui.addButton( 'brclear', {
				icon:this.path+"brclear.png",
				command: 'brclear',
				title:'Retour ligne sur toute la largeur'
			});
		}
	});
})();
