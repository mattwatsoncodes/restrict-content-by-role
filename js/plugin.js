// Document Ready
// -------------------------------------
jQuery( function( $ ) {

	var override                = $('#mkdo_rcbr .field-override-parent-permissions');
	var override_input          = $('#mkdo_rcbr input[name=mkdo_rcbr_override]');
	var override_input_selected = $('#mkdo_rcbr input[name=mkdo_rcbr_override]:radio:checked');
	var override_group          = $('#mkdo_rcbr .field-group-override');
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

jQuery( function( $ ) {

	var override                = $('#mkdo_rcbr_admin .field-override-parent-permissions');
	var override_input          = $('#mkdo_rcbr_admin input[name=mkdo_rcbr_admin_override]');
	var override_input_selected = $('#mkdo_rcbr_admin input[name=mkdo_rcbr_admin_override]:radio:checked');
	var override_group          = $('#mkdo_rcbr_admin .field-group-override');
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
