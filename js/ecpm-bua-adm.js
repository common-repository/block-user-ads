(function( $ ) {
    $(function() {
        $( '.ecpm-bua-color-field' ).wpColorPicker();
    });
})( jQuery );

(function($) {
	$(document).on( 'click', '.bua-nav-tab-wrapper a', function() {
		$('[id=ecpm_bua_section]').hide();
    $('.bua-nav-tab-wrapper a').removeClass('bua-nav-tab-active');
		$('[id=ecpm_bua_section]').eq($(this).index()).show();
    $(this).addClass('bua-nav-tab-active');
		return false;
  })
})( jQuery );
