(function($){

	$.entwine('ss', function($) {
		$('.ss-gridfield').entwine({

			onreload: function() {

				// activate "Save & Publish" button
				if( $(this).hasClass('publish-with-me') ){
					$('#Form_EditForm').find('button[name=action_publish]').button('option', 'showingAlternate', true);
				}

				// force Preview pane to reload (could not get this working via entwine)
				$( 'iframe[name=cms-preview-iframe]' ).attr( 'src', function ( i, val ) { return val; });

			}
		});
	});

}(jQuery));
