<% require JavaScript(newsletter/javascript/jquery.js) %> 
<% require JavaScript(newsletter/javascript/newsletteradmin.js) %>
<% require ThemedCSS(newsletteradmin) %>

<h1>Edit recievers</h1>
<h2>from Newsletter '$Title'</h2>
<h3>Send From: $SelectedNewsletterCampaign.SendFrom</h3>

<h3><a href="$SelectedNewsletterCampaign.Link">Newsletter Preview</a></h3>

$Content
$Form
$RecieverForm

<h2>Add recievers</h2>
<div>
	<h3>First Line</h3>
	<p>Define fields (firstname;surname;gender;email) [works also with shortversions]</p>
	<p>Seperator ; or , </p>
	<p>Gender: (m/f/-)</p>
	<h4>e.g.</h4>
	<p>first;sur;mail;sex</p>
	<h3>Records</h3>
	<h4>e.g.</h4>
	<p>max;maximus;m;max@maximus.com</p>
</div>
<table class="recieverList">
	<tr><td><strong>Email</strong></td><td>Gender&nbsp;</td><td>FirstName&nbsp;&nbsp;</td><td>Surname</td></tr>
<% control SelectedNewsletterCampaign.Recievers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$Gender</td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
	<tr><td colspan="4"><h4>Subscribers (must be imported in the actual reciever list, if needed)</h4></td></tr>
<% control SelectedNewsletterCampaign.Subscribers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$Gender</td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
</table>

<p class="userStatus0">Not sended</p>
<p class="userStatus1">Sended</p>

<p>
	<h3>Delete $Recievers.Count recievers</h3>
	$DeleteForm
</p>

<h4>$Recievers.Count recievers found</h4>
<h4>$Subscribers.Count subscribers found</h4>
<p>$ImportDefaultsForm</p>
<br/>
<h4>Send Newsletter to $Recievers.Count recievers</h4>
<br/>
<div id="NewsletterSendingProgress"></div>
<div id="SendEmails" class="loading">
	<a href="javascript:void();" link="$SendLink" id="ActionSendEmails">Send Newsletters</a>
	<div id="EmailSendStatus"></div>
</div>
<br/>
