<?php

class NewsletterMember extends DataObject {
	
	static $db = array(
		"FirstName"=>"Varchar(160)",
		"Surname"=>"Varchar(160)",
		"Email"=>"Varchar(200)",
		"Country"=>"Varchar(50)",
		"HTML"=>"Boolean",
		"Hash"=>"Varchar(32)",
		"Confirm"=>"Varchar(200)",
		"Category"=>"Varchar(200)",
		);
		
	static $field_names = array(
		'FirstName' => 'Vorname',
		'Surname' => 'Nachame',
		'Email' => 'eMail',
		);	
	
	static $has_one = array(
		"NewsletterCategory"=>"NewsletterCategory"
		);
	
}

?>