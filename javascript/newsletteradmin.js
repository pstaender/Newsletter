// Standard jQuery header

;(function($) {
$(document).ready(function() {
	
	var ajaxURL = '';
	var onLoading = false;//verhindert mehrfach laden
	
	$("#ActionSendEmails").bind('click', function(){
		
		function sendNewsletterRequest() {
			if (!onLoading) {
				onLoading=true;
				$.ajax({
					// type: "POST",
					url: ajaxURL,
					success: function(msg){
						msg = msg.trim();
						number = parseInt(msg);
						count=count+number;
						onLoading = false;
						if (!(number>0)) {
							$("#EmailSendStatus").html($("#EmailSendStatus").html()+" ... fertig");
							window.clearInterval(aktiv);
							$("SendEmails").removeClass('loading');
						} else {
							$("#EmailSendStatus").html(count+" verschickt");
						}
				      }
				});
			}
			
		}
		
		var number = 1;
		var count = 0;
		$("SendEmails").addClass('loading');
		
		ajaxURL = $(this).attr('link');
		var aktiv = window.setInterval(sendNewsletterRequest, 200);
		// number = sendNewsletterRequest(ajaxURL);
		$(this).removeClass('loading');
		return false;
	});
	
	
	
})
})(jQuery);
