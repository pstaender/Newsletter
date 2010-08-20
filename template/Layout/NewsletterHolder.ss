<% include FitContentMainStyle %>


<div id="ContentAdditional" class="typography">
	<ol class="overviewSnippetList" style="margin-top: 8px">
	<% control Children %>
	<% if First %>
	<li>
		<% if TeaserImage %><img src="<% control TeaserImage %>$SetWidth(100).Url<% end_control %>" class="teaserImageForOverview" alt="$Title" title="$Title"/><% end_if %>
		<a href="$Link"><h2>$Title</h2></a>
		<h3>$Subtitle</h3>
		<br/><p>$ShortSummary.HTML</p>
		<ul><a href="$Link" class="readMore">Mehr</a></ul>
	</li>
	<% end_if %>
	<% end_control %>
	</ol>
</div>


<div id="ContentMain" class="typography">
	<h2>$MetaTitle</h2>
	<br />
	<% control Children %><p><a href="{$Link}?preview"><h4>$Subtitle</h4>$Title</a></p><% end_control %>
	$Content
	
</div>



