jQuery(document).ready(function() {
	jQuery('#newsletter-form').ajaxForm({
		dataType: 'json',
		data: {action: 'newsletter'},
		timeout: 15000,
		error: function() { jQuery('#newsletter-email').removeClass('preloading'); alert('MailChimp is currently unavailable. Please try again later.'); },
		success: newsletterResponseMailchimp
	});
	jQuery('#newsletter-submit').click(function() { jQuery('#newsletter-email').addClass('preloading'); jQuery('#newsletter-form').submit(); return false; });
});

function newsletterResponseMailchimp(response)
{
	if (response.responseStatus == 'err') {
	if (response.responseMsg == 'ajax') {
	alert('Error - this script can only be invoked via an AJAX call.');
	} else if (response.responseMsg == 'name') {
	alert('Please enter a valid name.');
	} else if (response.responseMsg == 'email') {
	alert('Please enter a valid email address.');
	} else if (response.responseMsg == 'listid') {
	alert('Invalid MailChimp list name');
	} else if (response.responseMsg == 'duplicate') {
	alert('You are already subscribed to our newsletter.');
	} else {
	alert('Undocumented error (' + response.responseMsg + '). Please refresh the page and try again.');
	}
	} else if (response.responseStatus == 'ok') {
	alert(success_message);
	} else {
	alert('Undocumented error. Please refresh the page and try again.');
	}
	jQuery('#newsletter-email').removeClass('preloading');
}
