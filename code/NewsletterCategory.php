<?php

class NewsletterCategory extends DataObject {
	
	static $db = array(
		"Title"=>"Varchar(200)",
		"Description"=>"Text",
		);
		
	static $allowed_children = array(
		"NewsletterCampaign",
		// "NewsletterBlacklist",
		);
		
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if($this->class == 'NewsletterCategory') {
			if(!DataObject::get($this->class)) {
				$n = new NewsletterCategory();
				$n->Title = "bpp";
				$n->Description = "bpp newsletter";
				$n->write();
				Database::alteration_message("bpp newsletter category created","created");		
			}
		}
	}
}

?>