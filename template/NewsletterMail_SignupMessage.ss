
<% control Member %>
<p>Guten Tag<% if Salutation %>$Salutation $Surname<% end_if %>,</p>
<p>vielen Dank, dass Sie sich für unseren Newsletter anmelden möchten:</p>
<% end_control %>
<p><h3>$Newsletter.Description</h3></p>
<p>Klicken Sie als Bestätigung auf den unten stehenden Link:</p>
<a href="{$BaseHref}{$ConfirmURL}">Bestätigungslink</a>
<p>Sie können den Newsletter jerdezeit wieder abbestellen. In jedem Newsletter finden Sie einen individuellen Abbestelllink für Sie.</p>