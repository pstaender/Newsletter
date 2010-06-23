<h4><a href="$SelectedNewsletterCampaign.Link"> Newsletter ansehen</a></h4>


$Content
$Form


<h2>Empfänger hinzufügen</h2>
<div>
	<b>Bitte auf folgende Regel achten:</b><br/>
	<p>(CSV-Format)</p>
	<p><b>Beispiel:</b><br/>
	philipp.staender@gmail.com<br/>
	wolfgang.kornfeld@b-p-p.info<br/>
	etc...</p>
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
	<tr><td><strong>Email</strong></td><td>Vorname&nbsp;&nbsp;</td><td>Nachname</td></tr>
<% control Recievers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
	<tr><td><h4>Abonierte Adressen (noch nicht unbed. eingetragen)</h4></td></tr>
<% control Subscribers %>
	<tr><td class="userStatus{$Send}"><strong>$Email</strong></td><td>$FirstName</td><td>$Surname</td></tr>
<% end_control %>
</table>



<br/>
<h4>$Recievers.Count Empfänger eingetragen</h4>
<h4>$Subscribers.Count Abonomenten gefunden</h4>
<p><a href="$URL/import_defaults/$UrlID">Alle $Subscribers.Count Aboadressen reinladen</a></p>
<br/>
<h4>Newsletter an $Recievers.Count Empfänger verschicken</h4>
<br/>
<p>Sind sie sicher?</p>
<a href="$SendLink" onclick="javascript: if (confirm('Sicher?')) return true; else return false;">Ja, bin ich</a>
