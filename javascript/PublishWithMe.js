(function($){

	$.entwine('ss', function($) {

		$('.ss-gridfield').entwine({
			onreload: function() {
				// grid has been reloaded (eg, row deleted) mark form as changed
				if( $(this).hasClass('publish-with-me') ){
					$('#Form_EditForm').trigger('dirty.changetracker');
				}
				// force Preview pane to reload (could not get this working via entwine)
				$( 'iframe[name=cms-preview-iframe]' ).attr( 'src', function ( i, val ) { return val; });
			}
		});

		$(".ss-gridfield.ss-gridfield-editable.publish-with-me").entwine({
			onaddnewinline: function (e) {
				this._super(e);
				// inline row added, mark form as changed
				$('#Form_EditForm').trigger('dirty.changetracker');
			}
		});

	});

}(jQuery));
