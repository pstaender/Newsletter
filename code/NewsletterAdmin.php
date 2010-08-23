<?php

class NewsletterAdmin extends ModelAdmin {
	
	public static $managed_models = array(
		'NewsletterCategory',
		'NewsletterMember',
		'NewsletterReciever',
		'NewsletterBlacklist',
	);

	static $url_segment = 'newslettermodule';
	static $menu_title = 'Newsletter';
	
}

?>