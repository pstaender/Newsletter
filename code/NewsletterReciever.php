<?php

class NewsletterReciever extends DataObject {
	
	static $db = array(
		"FirstName"=>"Varchar(100)",
		"Surname"=>"Varchar(100)",
		"Gender"=>"Enum('m,f,-','-')",
		"Email"=>"Varchar(250)",
		"Send"=>"Int",
		);
		
	static $has_one = array(
		"Member"=>"NewsletterMember",
		"Newsletter"=>"NewsletterCampaign",
		);
		
	static $field_names = array(
		'FirstName' => 'Vorname',
		'Surname' => 'Nachame',
		'Email' => 'eMail-Adresse',
		);
		
	static $field_types = array(
		'Email' => 'EmailField',
		'FirstName' => 'TextField',
		'Surname' => 'TextField',
		'Send' => 'CheckboxField',
	);
	
	static $belongs_to = array(
		'Newsletter' => 'NewsletterCampaign'
		);
	
	function getCMSFields_forPopup() {
		$fields = $this->scaffoldFormFields(array(
				'restrictFields' => array("FirstName", "Surname", "Email"),
					));
		$fields->push(new HiddenField("ID", null, $this->ID));		
		return $fields;
	}
	
	function gender() {
		if ($this->Gender=="-") return null;
		return $this->Gender;
	}
	
	function salutation() {
		if ($this->Gender=="m") return "Mr.";
		if ($this->Gender=="f") return "Mrs.";
	}
	
}

?>