<% require JavaScript(newsletter/javascript/jquery.js) %> 
<% require JavaScript(newsletter/javascript/newsletteradmin.js) %>
<% require ThemedCss(newsletteradmin) %>

<h4><a href="$SelectedNewsletterCampaign.Link"> Newsletter ansehen</a></h4>

$Content
$Form


<h2>Empfänger hinzufügen</h2>
<div>
	<h3>Erste Zeile</h3>
	<p>Felder definieren (firstname;surname;gender;email)</p>
	<p>Trennzeichen ; oder , - Geschlecht (m/f)</p>
	<h3>Datensätze</h3>
	<h4>Beispiel</h4>
	<p>max;mustermann;m;max@mustermann.com</p>
</div>

<style type="text/css">
.userStatus1 {
	color: #2B9100;
}
.userStatus0 {
	color: #E52020;
}
.userStatus2 {
	color: #AAA;
}
table.recieverList {
	margin: 20px 0px;
}
table.recieverList td {
	background: #ddd;
	padding: 2px 5px;
}
</style>

<table class="recieverList">
	<tr><td><strong>Email</strong></td><td>Geschlecht&nbsp;</td><td>Vorname&nbsp;&nbsp;</td><td>Nachname</td></tr>
<% control Recievers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$Gender</td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
	<tr><td colspan="4"><h4>Abonierte Adressen (noch nicht unbed. eingetragen)</h4></td></tr>
<% control Subscribers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$Gender</td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
</table>

<p><a href="$URL/delete_all/$UrlID">Alle $Recievers.Count Empfänger löschen</a></p>



<br/>
<h4>$Recievers.Count Empfänger eingetragen</h4>
<h4>$Subscribers.Count Abonomenten gefunden</h4>
<p><a href="$URL/import_defaults/$UrlID">Alle $Subscribers.Count Aboadressen reinladen</a></p>
<br/>
<h4>Newsletter an $Recievers.Count Empfänger verschicken</h4>
<br/>
<div id="NewsletterSendingProgress"></div>
<div id="SendEmails" class="loading">
	<a href="javascript:void();" link="$SendLink" id="ActionSendEmails">Verschicken</a>
	<div id="EmailSendStatus"></div>
</div>
<br/>
