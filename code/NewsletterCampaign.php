<?php

class NewsletterCampaign extends Page {
	
	static $db = array(
		"Name"=>"Varchar(200)",
		"SendFrom"=>"Varchar(200)",
		"Footer"=>"HTMLText",
		"StyleSheet"=>"Text",
		"TemplateFilename"=>"Varchar(250)",
		"BodyStyle"=>"Varchar(250)",
		"ContentStyle"=>"Varchar(250)",
		"LinkStyle"=>"Varchar(250)",
		"ImageStyle"=>"Varchar(250)",
		"HeadingStyle"=>"Varchar(250)",
		"ParagraphStyle"=>"Varchar(250)",
		"HorizontalRuleStyle"=>"Varchar(250)",
		"TableStyle"=>"Varchar(250)",
		"TableCellAttribute"=>"Varchar(250)",
		"TableCellStyle"=>"Varchar(250)",
		);
		
	static $has_one = array(
		"NewsletterCategory"=>"NewsletterCategory",
		);
		
	static $has_many = array(
		"Recievers"=>"NewsletterReciever",
		);
	
	static $makeRelativeToAbsoluteURLS = true;
		
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$categories = DataObject::get("NewsletterCategory");
		$fields->addFieldsToTab('Root.Content.Newsletter', array(
			new TextField("Name"),
			new TextField("SendFrom"),
			new TextField("TemplateFilename"),
			new TextField("BodyStyle"),
			new TextField("ContentStyle"),
			new TextField("LinkStyle"),
			new TextField("ImageStyle"),
			new TextField("ParagraphStyle"),
			new TextField("HeadingStyle"),
			new TextField("HorizontalRuleStyle"),
			new TextField("TableStyle"),
			new TextField("TableCellAttribute"),
			new TextField("TableCellStyle"),
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
		return $fields;
	}
	
	function sendFrom() {
		return ($this->SendFrom) ? $this->SendFrom : NewsletterHolder::$newsletterEmail;
	}
	
	function ParentNewsletterCategory() {
		$p = $this->Parent();
		if ($p->Class = "NewsletterCategory") return $p;
	}
	
	function renderedNewsletter() {
		return self::getRenderedNewsletterContent($this);
	}
	
	static function getRenderedNewsletterContent($campaignPage) {
		//set temp folder
		$tmpBaseFolder = TEMP_FOLDER . '/newsletter';
		$tmpFolder = (project()) ? "$tmpBaseFolder/" . project() : "$tmpBaseFolder/site";
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
		if (strlen(trim($campaignPage->TableCellStyle))>0) {
			$content = preg_replace('#(<td(.*)[/]?>)#U', '<td \2 style="'.$campaignPage->TableCellStyle.'">', $content); 
		}
		if (strlen(trim($campaignPage->HeadingStyle))>0) {
			$content = preg_replace('#((<h)([0-9].*)[/]?>)#U', '\2\3 style="'.$campaignPage->HeadingStyle.'" >', $content); 
		}
		if (strlen(trim($campaignPage->ParagraphStyle))>0) {
			$content = preg_replace('#(<p(.*)[/]?>)#U', '<p \2 style="'.$campaignPage->ParagraphStyle.'" >', $content); 
		}
		if (strlen(trim($campaignPage->HorizontalRuleStyle))>0) {
			$content = preg_replace('#(<hr (.*)[/]?>)#U', '<img \2 style="'.$campaignPage->HorizontalRuleStyle.'" />', $content); 
		}
		if (self::$makeRelativeToAbsoluteURLS) {
			$base = Director::absoluteBaseURL();
			// exit($base);
			$s = $content;
			$sl = "\'";
			$s = preg_replace('/(\<.*)(src\=)+([\"'.$sl.']+[http\:\/\/|https\:\/\/]{0})(.*\>)/i',"$1$2$3".$base."$4",$s);
			$base = Director::protocolAndHost();
$s=preg_replace('#(href)="([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#','$1="'.$base.'$2$3',$s);
			$content = $s;
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
	
	function onBeforeWrite() {
		//strip .ss extension, if typed for filename
		if ($this->TemplateFilename) $this->TemplateFilename = preg_replace("/(.*)(\.ss)/i","$1",$this->TemplateFilename);
		//only save email, when valid
		if (!self::isValidEmail($this->SendFrom)) $this->SendFrom = null;
		return parent::onBeforeWrite();
	}
	
	static function isValidEmail($email) {
		return eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $email);
	}
	
	function sendTo($to,$from=null,$template="EmailTemplate") {
		if ($from==null) $from = NewsletterHolder::$newsletterEmail;
		if (Permission::check("EDIT_NEWSLETTER")) {
			$content = $this->renderedNewsletter();
			$emailMessage = new Email(NewsletterHolder::$newsletterEmail, $to, $this->Title);
			$emailMessage->setBody($content);
			$emailMessage->setTemplate($template);
			$emailMessage->send();
			return true;//echo "Newsletter-Testmessage was send '".$to."'...";
		} else {
			user_error("No permission ['EDIT_NEWSLETTER'] to send newsletter...");
		}
	}
	
	function subscribers() {
		return DataObject::get("NewsletterMember","NewsletterCategoryID = ".$this->NewsletterCategoryID." AND Confirm=''");
	}
	
}

class NewsletterCampaign_Controller extends ContentController {
	
	function init() {
		parent::init();
		Requirements::customCSS(
			$this->dataRecord->StyleSheet
			);
		if (isset($_REQUEST['send_to'])) {
			$this->dataRecord->sendTo($_REQUEST['send_to']);
		}
		if ($this->isPreviewMode()) {
			echo $this->dataRecord->renderedNewsletter();
			exit();
		}
	}
	
	function index() {
		if ($fn = $this->dataRecord->TemplateFilename) return $this->renderWith($fn); 	
		return array();
	}
	
	function isPreviewMode() {
		return ((isset($_REQUEST['send_to'])) OR (isset($_REQUEST['preview'])) OR (strtolower(Director::urlParam("Action"))=="preview"));
	}
	
	function preview() {
		echo $this->dataRecord->renderedNewsletter();
		exit();
	}
	
	function newsletterNavigator() {
		if (($this->memberIsNewsletterAdmin())) {
			Requirements::JavaScript("newsletter/javascript/jquery.js");
			Requirements::JavaScript("newsletter/javascript/jqDnR.js");
			Requirements::JavaScript("newsletter/javascript/newsletternavi.js");
			Requirements::ThemedCSS("newsletternavigator");
			
			return '
<div id="NewsletterNavigator">
	'.$this->NewsletterNavigatorForm()->forTemplate().'
	<div id="NewsletterNaviMessage">
	
	</div>
</div>';
		}
	}
	
	function NewsletterNavigatorForm() {
		$fields = new FieldSet(
			new EmailField("Email")
		);
		$actions = new FieldSet(new FormAction("send_to", _t("Newsletter.Campaign.SendTestTo","Send testmail to")));
		$form = new Form($this, "NewsletterNavigatorForm", $fields, $actions);
		$form->disableSecurityToken();
		return $form;
	}
	
	function send_to($data) {
		$email = $_REQUEST['email'];
		if (!NewsletterCampaign::isValidEmail($email)) {
			echo("Please choose a valid eMail!");
			exit();
		}
		$this->dataRecord->sendTo($email);
		
		echo "Testversand an '".$email."'...";
		exit();
		
	}
	
	function memberIsNewsletterAdmin() {
		return Permission::check("EDIT_NEWSLETTER");
	}
	
	function isPreviewForAdmin() {
		return (($this->memberIsNewsletterAdmin()) && ($this->isPreviewMode()));
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