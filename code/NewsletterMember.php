<?php

class NewsletterMember extends DataObject {
	
	static $db = array(
		"FirstName"=>"Varchar(160)",
		"Surname"=>"Varchar(160)",
		"Gender"=>"Enum('-,m,f','-')",
		"Email"=>"Varchar(200)",
		"Country"=>"Varchar(50)",
		"HTML"=>"Boolean",
		"Hash"=>"Varchar(32)",
		"Confirm"=>"Varchar(200)",
		"Category"=>"Varchar(200)",
		);
	
	static $has_one = array(
		"NewsletterCategory"=>"NewsletterCategory"
	);
		
	static $belongs_to = array(
		"NewsletterCategory"=>"NewsletterCategory"
	);
		
	static $summary_fields = array(
		"Email","FirstName","Surname","NewsletterCategory.Title"
		);
		
	function gender() {
		if ($this->Gender=="-") return null;
		return $this->Gender;
	}
	
	function salutation() {
		if ($this->Gender=="m") return _t("Newsletter.Gender.Male","Mr.");
		if ($this->Gender=="f") return _t("Newsletter.Gender.Female","Mrs.");
	}
	
}

?>