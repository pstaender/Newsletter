<?php

class NewsletterAdvertisement extends DataObject {
	
	static $db = array(
		"Title"=>"Varchar(200)",
		"Key"=>"Varchar(100)",
		// "Featured"=>"Boolean",
		"Content"=>"Text",
		"CompanyName"=>"Varchar(100)",
		"PersonName"=>"Varchar(80)",
		"Street"=>"Varchar(200)",
		"ZipCode"=>"Varchar(100)",
		"City"=>"Varchar(100)",
		"Country"=>"Varchar(50)",
		"Email"=>"Varchar(150)",
		"Phone"=>"Varchar(100)",
		"Fax"=>"Varchar(100)",
		"Homepage"=>"Varchar(100)",
		"SortOrder"=>"Int",
			"Category"=>"Enum('Job,Immobilie,Ausbildung,Material,Verschiedenes','Verschiedenes')",
		);
		
	static $field_labels = array(
		"Title"=>"Überschrift",
		"Key"=>"Kennziffer",
		"Featured"=>"Hervorhebung",
		"Content"=>"Inhalt",
		"CompanyName"=>"Firmenname",
		"PersonName"=>"Ansprechpartner",
		"Street"=>"Straße/HausNr",
		"ZipCode"=>"PLZ",
		"City"=>"Stadt",
		"Country"=>"Land",
		"Email"=>"eMail",
		"Phone"=>"Telefon",
		"Fax"=>"Fax",
		"Homepage"=>"Webseite",
		"Category"=>"Kategorie",
		"SortOrder"=>"Sortierreihenfolge (aufsteigend)",
		);
		
		static $belongs_to = array(
			'Newsletter' => 'NewsletterCampaign'
			);

		static $has_one = array(
			'Newsletter' => 'NewsletterCampaign'
			);

		static $default_sort = 'SortOrder ASC';
		
		function getCMSFields_forPopup() {
			$fields = $this->scaffoldFormFields();
			$fields->push(new HiddenField("ID", null, $this->ID));
			return $fields;
		}
		
	
}

?>