jQuery(document).ready(function(){

	var currentlyEdited = '.' + jQuery('.style-list').val();
	setVisible(currentlyEdited, true);

	jQuery('.style-list').change(function(){
		setVisible(currentlyEdited, false);

		currentlyEdited = '.' + jQuery('.style-list').val();
		setVisible(currentlyEdited, true);
	});

	function setVisible(element, visible){
		if(visible)
			jQuery(element).css({'display': 'table-row'});
		else
			jQuery(element).css({'display': 'none'});
	}
});