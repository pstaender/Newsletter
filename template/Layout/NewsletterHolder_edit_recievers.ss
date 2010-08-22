<% require JavaScript(newsletter/javascript/jquery.js) %> 
<% require JavaScript(newsletter/javascript/newsletteradmin.js) %>
<% require ThemedCSS(newsletteradmin) %>

<h4><a href="$SelectedNewsletterCampaign.Link"> Newsletter ansehen</a></h4>

$Content
$Form
$RecieverForm

<h2>Empfänger hinzufügen</h2>
<div>
	<h3>Erste Zeile</h3>
	<p>Felder definieren (firstname;surname;gender;email)</p>
	<p>Trennzeichen ; oder , - Geschlecht (m/f)</p>
	<h3>Datensätze</h3>
	<h4>Beispiel</h4>
	<p>max;mustermann;m;max@mustermann.com</p>
</div>
<table class="recieverList">
	<tr><td><strong>Email</strong></td><td>Geschlecht&nbsp;</td><td>Vorname&nbsp;&nbsp;</td><td>Nachname</td></tr>
<% control SelectedNewsletterCampaign.Recievers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$Gender</td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
	<tr><td colspan="4"><h4>Abonierte Adressen (noch nicht unbed. eingetragen)</h4></td></tr>
<% control SelectedNewsletterCampaign.Subscribers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$Gender</td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
</table>

<p>
<h3>Alle $Recievers.Count Empfänger löschen</h3>
$DeleteForm

</p>



<br/>
<h4>$Recievers.Count Empfänger eingetragen</h4>
<h4>$Subscribers.Count Abonomenten gefunden</h4>
<p>$ImportDefaultsForm</p>
<br/>
<h4>Newsletter an $Recievers.Count Empfänger verschicken</h4>
<br/>
<div id="NewsletterSendingProgress"></div>
<div id="SendEmails" class="loading">
	<a href="javascript:void();" link="$SendLink" id="ActionSendEmails">Verschicken</a>
	<div id="EmailSendStatus"></div>
</div>
<br/>
