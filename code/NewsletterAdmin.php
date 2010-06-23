<?php

class NewsletterAdmin extends ModelAdmin {
	
	public static $managed_models = array(
		'NewsletterCategory',
		'NewsletterMember',
		'NewsletterBlacklist',
		'NewsletterAdvertisement',
	);

	static $url_segment = 'newslettermodule';
	static $menu_title = 'Newsletter';
	
}

?>