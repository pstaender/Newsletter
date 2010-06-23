<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>$Title</title>
	<meta name="author" content="bpp">
</head>
<body>

<style type="text/css">
body, #MainContent {
	font-family: 'Lucida Grande', 'Lucida Sans Unicode', Arial;
	font-size: 14px;
	color: #222;
}
.clear {
	clear: both;
}
#MainContent {
}
body {
	background: #eee;
	margin: 0px;
	padding: 0px;
}
#ContentFrame {

}
.content {
	padding: 30px;
}
#NewsletterHeader {
	height: 160px;
}
img.right {
	float:right;
	margin-left: 10px;
}
#MainContent a {
	$LinkStyle
}
#MainContent img {
	$ImageStyle
}
#MainLogo {
	margin: 25px;
}
hr {
	border: 0px;
	border-top: 3px solid #ddd;
	margin-bottom: 10px;
	margin-top: 10px;
}
</style>

<table width="100%" bgColor="#eeeeee">
	<table id="MainContent" bgColor="#ffffff" align="center" width="800" style="font-family: 'Lucida Grande', 'Lucida Sans Unicode', Arial; font-size: 14px; color: #222;">
		<tr style="font-family: 'Lucida Grande', 'Lucida Sans Unicode', Arial; font-size: 14px; color: #222; line-height: 1.4em;">
			<td width="20">
			</td>
			<td>
			<div id="NewsletterHeader" style="position: relative;">
				<table width="100%">
					<tr>
						<td>
							<a href="$BaseHref"><img src="http://b-p-p.info/assets/newsletter/bbp_2010.png" alt="bppLogo" id="MainLogo" style="" border="0" style="margin-top: 8px;"/></a>
						</td>
						<td align="right" valign="top">
							<h2>$Title</h2>
							<h3 style="padding: 0px;margin:0px;color:#999999;">$Subtitle</h3>
						</td>
					</tr>
				</table>
			</div>
			<div id="ContentFrame" class="content">
				$Content
				<br/>
				<hr style="border: 0px; border-top: 3px solid #ddd; margin-top: 25px; margin-bottom: 5px;"/>
				
				<!--<h2>Übersicht</h2>-->
				<br/>
				<% control Children %>
				<div>
					<strong style="font-size:1.3em"><a href="#$URLSegment">$Title</a></strong>
					<div style="font-size:1.2em;"><a href="#$URLSegment">$Subtitle</a></div>
					<p>
					<% if MetaDescription %>$MetaDescription.Parse(BBCodeParser)
					<% else %>$Content.FirstParagraph
					<% end_if %>
					</p>
					<p><a href="#$URLSegment">mehr...</a></p>
				</div>
				<% end_control %>
				<br />
				<hr style="border: 0px; border-top: 3px solid #ddd; margin: 0px; padding:0px;"/>
			</div>
			<div id="ContentTable" class="content">
				<% control Children %>
				<a name="$URLSegment"></a>
				<div><h2>$Title</h2>
					<h3>$Subtitle</h3>
					$Content</div>
					
				<hr style="border: 0px; border-top: 3px solid #ddd; margin-top: 25px; margin-bottom: 25px;"/>
				<% end_control %>
				<% if Ads %>
				<% control Ads %>
				<a name="$ID"></a>
				<div><h2>$Title</h2>
					<h3>$Key</h3>
					<p>$Content.Parse(BBCodeParser)</p>
					<p>Mehr Informationen / Kontakt:</p>
					<p>
						<div style="font-weight: bold;">$CompanyName</div>
						<div>$PersonName</div>
						<div>$Street</div>
						<div><% if Country %>$Country - <% end_if %>$ZipCode $City</div>
					</p>
				</div>
					
				<hr style="border: 0px; border-top: 3px solid #ddd; margin-top: 25px; margin-bottom: 25px;"/>
				<% end_control %>
				<% end_if %>
			</div>
			<div id="NewsletterHeader" class="content">
				<img src="http://b-p-p.info/assets/newsletter/images/NewsletterSponsoren2010.png" style="position: absolute; padding-top: 40px;" usemap="#Map" border="0"/>
				<map name="Map" id="Map"><area shape="rect" coords="100,54,201,82" href="http://www.manfrotto.de" target="_blank" />
				<area shape="rect" coords="211,54,311,83" href="http://eu.asukabook.com/" target="_blank" />
				<area shape="rect" coords="317,52,419,77" href="http://www.fotowerkstatt-belz.de" target="_blank" />
				<area shape="rect" coords="426,49,485,73" href="http://www.sander.de/" target="_blank" />
				<area shape="rect" coords="489,50,553,75" href="http://www.allcop.de/" target="_blank" />
				<area shape="rect" coords="555,47,622,76" href="http://www.epson.de" target="_blank" />
				<area shape="rect" coords="626,49,654,80" href="http://www.nikon.de" target="_blank" />
				</map>
				<table width="100%">
					<tr valign="top" align="right">
						<td>
							
							<table cellpadding="10" style="color:#999999;font-size:0.8em;line-height:1.3em;">
								<tr valign="top"><td>
									<strong>bpp Geschäftsstelle</strong><br/>Engeldorfer Str. 25<br/>D-50321 Br&uuml;hl<br/>
								</td>
								<td><br/>
									Tel.: +49 (0) 22 32 / 57 93 99 - 15<br/>
									Fax: &nbsp;+49 (0) 2232 / 57 93 99-29<br/>
								</td>
								<td><br/>
									<a href="mailto:info@b-p-p.info" target="_blank">info@b-p-p.info</a><br/>
									<a href="http://www.b-p-p.info" target="_blank">www.b-p-p.info</a><br/>
								</td>
								</tr>
								
							</table>
							</td>
					</tr>
				</table>
				<!--<a href="{$BaseHref}{$NewsletterURL}/unsubscribe/%USER_EMAIL%/%NEWSLETTER_ID%/" style="color: #999999; font-weight: normal; font-size: 0.9em; margin-top: 10px;">Newsletter abbestellen</a>-->
			</div>
		</td>
		<td width="20">
		</td>
		</tr>
	</table>
</table>
</body>
</html>
