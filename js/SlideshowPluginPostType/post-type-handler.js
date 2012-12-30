jQuery(document).ready(function(){

	/**
	 * Loop through fields that depend on another field's value for showing, register change event
	 */
	jQuery('.depends-on-field-value').each(function(key, value){
		var attributes = jQuery(this).attr('class').split(' ');

		// Check if field should be shown
		var element = jQuery(this).closest('tr');
		if((jQuery('input[name="' + attributes[1] + '"]').val() == attributes[2] && jQuery('input[name="' + attributes[1] + '"]').prop('checked')) ||
			jQuery('select[name="' + attributes[1] + '"]').val() == attributes[2])
			setElementVisibility(element, true);
		else
			setElementVisibility(element, false);

		// On change, set field's visibility
		jQuery('input[name="' + attributes[1] + '"], select[name="' + attributes[1] + '"]').change(attributes, function(){
			var element = jQuery('.' + attributes[3]).closest('tr');

			if(jQuery(this).val() == attributes[2])
				setElementVisibility(element, true);
			else
				setElementVisibility(element, false);
		});
	});

	/**
	 * Set element visibility
	 *
	 * @param element
	 * @param visible
	 */
	function setElementVisibility(element, visible){
		if(visible)
			jQuery(element).css({'display': 'table-row'});
		else
			jQuery(element).css({'display': 'none'});
	}
});