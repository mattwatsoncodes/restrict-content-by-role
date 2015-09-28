// Document Ready
// -------------------------------------
jQuery( function( $ ) {

	var override                = $('.field-override-parent-permissions');
	var override_input          = $('input[name=mkdo_rcbr_override]');
	var override_input_selected = $('input[name=mkdo_rcbr_override]:radio:checked');
	var override_group          = $('.field-group-override');
	var is_override             = ( override.length > 0 );

	if( is_override && 'override' != override_input_selected.prop('value')  ) {
		override_group.hide();
		override.css( 'margin-bottom', '1px' );
	}

	override_input.on( 'change', function(){
		if( 'override' == $(this).prop('value') ) {
			override_group.slideDown();
		} else {
			override_group.slideUp();
		}
	});

}( jQuery ) );
