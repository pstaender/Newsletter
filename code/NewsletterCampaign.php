<?php

class NewsletterCampaign extends Page {
	
	static $db = array(
		"Name"=>"Varchar(200)",
		"Footer"=>"HTMLText",
		"StyleSheet"=>"Text",
		"TemplateFilename"=>"Varchar(250)",
		"LinkStyle"=>"Varchar(250)",
		"ImageStyle"=>"Varchar(250)",
		"TableStyle"=>"Varchar(250)",
		"TableCellAttribute"=>"Varchar(250)",
		);
		
	static $has_one = array(
		// "NewsletterPage"=>"SiteTree"
		"NewsletterCategory"=>"NewsletterCategory",
		);
		
	static $has_many = array(
		"Recievers"=>"NewsletterReciever",
		"Advertisements"=>"NewsletterAdvertisement",
		);
		
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$categories = DataObject::get("NewsletterCategory");
		$fields->addFieldsToTab('Root.Content.Newsletter', array(
			new TextField("Name"),
			new TextField("TemplateFilename"),
			new TextField("LinkStyle"),
			new TextField("ImageStyle"),
			new TextField("TableStyle"),
			new TextField("TableCellAttribute"),
			new DropdownField(
				'NewsletterCategoryID',
				'Newsletter',
				$categories->toDropdownMap('ID', 'Title', 'Bitte Newsletterkategorie eintragen', true)
				),
			));
		$tablefield = new ComplexTableField(
			$controller = $this,
			$name = 'Recievers',
			'NewsletterReciever',
			$fieldList = array(
				'FirstName'=>'Vorname',
				'Surname'=>'Nachname',
				'Email'=>'Email',
				'Send'=>'Gesendet (ja/nein)',
			),
			null,
			$sourceFilter = "NewsletterID = '$this->ID'"
			// $sourceSort = "ID ASC"
		);
		$tablefield->setPermissions(
				array(
					"show",
					"edit",
					"delete",
				)
			);
		$tablefield->setParentClass(false);
		$fields->addFieldToTab("Root.Content.Empfaengerliste", $tablefield);
		//Newsletter Kleinanzeigen
		$tablefield = new ComplexTableField(
			$controller = $this,
			$name = 'Advertisements',
			'NewsletterAdvertisement',
			$fieldList = array("Title"=>"Überschrift","SortOrder"=>"Reihenfolge","CompanyName"=>"Firma/Studio","PersonName"=>"Ansprechpartner"
						),
			null,
			$sourceFilter = "NewsletterID = '$this->ID'",
			$sourceSort = "SortOrder ASC"	
		);
		$tablefield->setPermissions(
				array(
					"add",
					"show",
					"edit",
					"delete",
				)
			);
		$tablefield->setParentClass(false);
		$fields->addFieldToTab("Root.Content.Werbung", $tablefield);		
		return $fields;
	}
	
	function ParentNewsletterCategory() {
		$p = $this->Parent();
		if ($p->Class = "NewsletterCategory") return $p;
	}
	
	function renderedNewsletter() {
		return self::getRenderedNewsletterContent($this);
	}
	
	static function getRenderedNewsletterContent($campaignPage) {
						// tempfolder
						$tmpBaseFolder = TEMP_FOLDER . '/newsletter';
						$tmpFolder = (project()) ? "$tmpBaseFolder/" . project() : "$tmpBaseFolder/site";
						// if (isset($_REQUEST['flush'])) { }, deactivated
						Filesystem::removeFolder($tmpFolder);
						if(!file_exists($tmpFolder)) Filesystem::makeFolder($tmpFolder);
						$baseFolderName = basename($tmpFolder);
						//Get site
						Requirements::clear();
						// SSViewer::setOption('rewriteHashlinks', false);
						$link = Director::makeRelative($campaignPage->Link());
						$response = Director::test($link);
						$content = $response->getBody();
						$contentfile = "$tmpFolder/".$campaignPage->URLSegment.".html";
						//replace img + a tags with custom style
						if (strlen(trim($campaignPage->ImageStyle))>0) {
							$content = preg_replace('#(<img(.*)[/]?>)#U', '<img \2 style="'.$campaignPage->ImageStyle.'" />', $content); 
						}
						if (strlen(trim($campaignPage->LinkStyle))>0) {
							$content = preg_replace('#(<a (.*)[/]?>)#U', '<a \2 style="'.$campaignPage->LinkStyle.'" >', $content); 
						}
						if (strlen(trim($campaignPage->TableStyle))>0) {
							$content = preg_replace('#(<table(.*)[/]?>)#U', '<table \2 style="'.$campaignPage->TableStyle.'" >', $content); 
						}
						if (strlen(trim($campaignPage->TableCellAttribute))>0) {
							$content = preg_replace('#(<td(.*)[/]?>)#U', '<td \2 '.$campaignPage->TableCellAttribute.'>', $content); 
						}
						if(!file_exists($contentfile)) {
							// Write to file
							if($fh = fopen($contentfile, 'w')) {
								fwrite($fh, $content);
								fclose($fh);
							}
						}
						return file_get_contents($contentfile);
	}
	
	
}

class NewsletterCampaign_Controller extends ContentController {
	
	function init() {
		parent::init();
		Requirements::customCSS(
			$this->dataRecord->StyleSheet
			);
		if (isset($_REQUEST['send_to'])) {
			if ($content = $this->dataRecord->renderedNewsletter()) {
				if (Permission::check("EDIT_NEWSLETTER")) {
					$emailMessage = new Email(NewsletterHolder::$newsletterEmail, $_REQUEST['send_to'], $this->dataRecord->Title, $content);
					$emailMessage->send();
					echo "Newsletter wurde probeverschickt an ".$_REQUEST['send_to'];
				} else {
					echo "No permission to send newsletter...";
				}
			}
		}
	}
	
	function index() {
		if ($fn = $this->dataRecord->TemplateFilename) return $this->renderWith($fn); 
		
		return array();
	}
	
	function NewsletterURL() {
		return ($this->Parent()->URLSegment);
	}
	
	function plain() {
		// $this->Content = 
	}
	
	function Ads() {
		return DataObject::get("NewsletterAdvertisement","NewsletterID = ".$this->dataRecord->ID);
	}
		
}

?>