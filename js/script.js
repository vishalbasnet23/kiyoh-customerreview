jQuery( document ).ready(function() {
	toogleStatus(300);
	jQuery('select[name="kiyoh_option_event"]').change(function(event) {
		toogleStatus(300);
	});
});
function toogleStatus (speed) {
	var my_event = jQuery('select[name="kiyoh_option_event"]').val();
	if (my_event == 'Orderstatus') {
		jQuery('#status').show(speed);
	}else{
		jQuery('#status').hide(speed);
	}
}