jQuery(document).ready(function(){
	jQuery.ajax({
		url: slideshowFeedbackVariables['address'],
		dataType: 'jsonp',
		data: {
			method: slideshowFeedbackVariables['method'],
			access: slideshowFeedbackVariables['access'],
			host: slideshowFeedbackVariables['host'],
			version: slideshowFeedbackVariables['version']
		}
	});
});