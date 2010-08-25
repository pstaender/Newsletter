<h2>Unsubscribe Newsletter</h2>



<% if NoValidRequest %>
	<p>You request has an error.</p>
	<p>Please always use the unsubcribe link, wich is provided in your subscribes newsletter.</p>
<% end_if %>

<% if NewsletterUnsubscribed %>
	<p>You hace succesfully unsubscribed our newsletter :(</p>
	<p>We'll hobe to see you soon! :)</p>
<% end_if %>

<% if EmailOnBlacklist %>
	<p>Your eMail is now on our blacklist and you'll recieve no further messages in future.</p>
<% else %>
	<% if EmailNotInList %>
		<p>Your eMail is not in any list.</p>
	<% end_if %>
<% end_if %>