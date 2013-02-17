jQuery(document).ready(function(){

	/**
	 * Loop through fields that depend on another field's value for showing, register change event
	 */
	jQuery('.depends-on-field-value').each(function(key, value){
		var attributes = jQuery(this).attr('class').split(' ');

		// Check if field should be shown
		var element = jQuery(this).closest('tr');
		if(jQuery('input[name="' + attributes[1] + '"]:checked').val() == attributes[2])
			jQuery(element).show();
		else
			jQuery(element).hide();

		// On change, set field's visibility
		jQuery('input[name="' + attributes[1] + '"]').change(attributes, function(){
			var element = jQuery('.' + attributes[3]).closest('tr');

			if(jQuery(this).val() == attributes[2])
				animateElementVisibility(element, true);
			else
				animateElementVisibility(element, false);
		});
	});

	/**
	 * Animate to element's visibility
	 *
	 * @param element
	 * @param visible
	 */
	function animateElementVisibility(element, visible){
		if(visible){
			jQuery(element)
				.show()
				.css('background-color', '#c0dd52')

			setTimeout(
				function(){
					jQuery(element).stop(true, true).animate({ 'background-color': 'transparent' }, 1500);
				},
				500
			);
		}else{
			jQuery(element).stop(true, true).hide();
		}
	}
});