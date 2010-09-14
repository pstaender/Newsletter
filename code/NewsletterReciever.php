<?php

class NewsletterReciever extends DataObject {
	
	static $db = array(
		"FirstName"=>"Varchar(100)",
		"Surname"=>"Varchar(100)",
		"Gender"=>"Enum('-,m,f','-')",
		"Email"=>"Varchar(250)",
		"Send"=>"Int",
		);
		
	static $has_one = array(
		"Member"=>"NewsletterMember",
		"Newsletter"=>"NewsletterCampaign",
		);
		
	static $field_types = array(
		'Email' => 'EmailField',
		'FirstName' => 'TextField',
		'Surname' => 'TextField',
		'Send' => 'CheckboxField',
	);
	
	static $summary_fields = array(
		"Email","Send","FirstName","Surname","Newsletter.Title"
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
		if ($this->Gender=="m") return _t("Newsletter.Gender.Male","Mr.");
		if ($this->Gender=="f") return _t("Newsletter.Gender.Female","Mrs.");
	}
	
	function sendStatus() {
		if ($this->Send=0) return _t("Newsletter.SendStatus.False","Not Sended");
		elseif ($this->Send=1) return _t("Newsletter.SendStatus.True","Sended");
		elseif ($this->Send=2) return _t("Newsletter.SendStatus.Backlist","On Blacklist");
		else return _t("Newsletter.SendStatus.Undefined","Undefined");
	}
	
	function sendStatusColor() {
		if ($this->Send=0) return "red";
		elseif ($this->Send=1) return "green";
		elseif ($this->Send=2) return "black";
		else return "grey";
	}
	
}

?>