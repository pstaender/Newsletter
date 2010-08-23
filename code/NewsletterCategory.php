<?php

class NewsletterCategory extends DataObject {
	
	static $db = array(
		"Title"=>"Varchar(200)",
		"Description"=>"Text",
		);
		
	static $allowed_children = array(
		"NewsletterCampaign",
		);
		
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if($this->class == 'NewsletterCategory') {
			if(!DataObject::get($this->class)) {
				$n = new NewsletterCategory();
				$n->Title = "Newsletter";
				$n->Description = "Default Newsletter";
				$n->write();
				Database::alteration_message("default newsletter category created","created");		
			}
		}
	}
}

?>