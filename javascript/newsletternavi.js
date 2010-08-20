// Standard jQuery header
;(function($) {
$(document).ready(function() {
	$('#NewsletterNavigator').bind('dblclick', function() {
		$(this).toggleClass('hidden');
		if ($(this).hasClass('hidden')) $(this).fadeTo(200,0.2);
		else $(this).fadeTo(200,1);
	})
	var text = 'eMail';
	$("input#Form_NewsletterNavigatorForm_Email").val(text);
	$("input#Form_NewsletterNavigatorForm_Email").bind('focus', function() {
		if ($(this).val()==text) $(this).val('');
	});
	$("input#Form_NewsletterNavigatorForm_Email").bind('blur', function() {
		if ($(this).val()=='') $(this).val(text);
	});
	$("#Form_NewsletterNavigatorForm").bind('submit', function(){
		var email = $("input#Form_NewsletterNavigatorForm_Email").val();
		$.ajax({
			type: "POST",
			data: "email="+email,
			url: $(this).attr('action'),
			// context: document.body,
			success: function(msg){
				box = $("#NewsletterNaviMessage");
		        box.fadeTo(0,1).html('<p>'+msg+'</p>');
				box.delay(1000).fadeTo(1000,0);
		      }
		});
		return false;
	});
})
})(jQuery);
