(function( $ ){
	$( document ).on( 'change', '#product_gallery_type', function() {
		if( $( this ).val() == 'okzoom' ) {
			$( '#okzoom-table' ).removeClass( 'hide' ).addClass( 'show' );
			$( '#elevatezoom-table' ).removeClass( 'show' ).addClass( 'hide' );
		} else if( $( this ).val() == 'elevatezoom' ) {
			$( '#okzoom-table' ).removeClass( 'show' ).addClass( 'hide' );
			$( '#elevatezoom-table' ).removeClass( 'hide' ).addClass( 'show' );
		} else {
			$( '#okzoom-table' ).removeClass( 'show' ).addClass( 'hide' );
			$( '#elevatezoom-table' ).removeClass( 'show' ).addClass( 'hide' );			
		}
	});
	
	$colorPickers =   {
                        '1': 'okzoom_bg_color', '2': 'okzoom_shadow_color', '3': 'okzoom_border_color', '4': 'elevatezoom_bg_color', 
						'5': 'elevatezoom_border_color'
                    };
	$.each( $colorPickers, function( $key, $value ) {				
		var initLayout = function() {
			$( '#' + $value ).ColorPicker({
				color: '#0000ff',
				onShow: function ( colpkr ) {
					$( colpkr ).fadeIn( 500 );
					return false;
				},
				onHide: function ( colpkr ) {
					$( colpkr ).fadeOut( 500 );
					return false;
				},
				onChange: function ( hsb, hex, rgb ) {
					$( '#' + $value ).val( '#' + hex );
				}
			});
		}
		EYE.register( initLayout, 'init' );
	});
	
})( jQuery )