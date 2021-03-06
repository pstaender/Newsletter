<?php

class NewsletterBlacklist extends DataObject {
	
	static $db = array(
		"Email"=>"Varchar(200)",
		);
		
	static $has_one = array(
		"NewsletterCategory"=>"NewsletterCategory",
		);
	static $belongs_to = array(
		"NewsletterCategory"=>"NewsletterCategory",
		);
	static $summary_fields = array(
		"Email","NewsletterCategory.Title","NewsletterCategory.Description"
		);
	
}

?>