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
		"EmailBodyTemplate"=>"Varchar(100)",
		);
		
	static $has_one = array(
		"NewsletterCategory"=>"NewsletterCategory",
		);
		
	static $has_many = array(
		"Recievers"=>"NewsletterReciever",
		);
		
	static $icon = 'newsletter/images/icons/NewsletterCampaign';
	
	static $makeRelativeToAbsoluteURLS = true;
		
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$categories = DataObject::get("NewsletterCategory");
		$fields->addFieldsToTab('Root.Content.Newsletter', array(
			new TextField("Name",_t("Newsletter.Campaign.Name","Name of the Newsletter Campaign")),
			new EmailField("SendFrom",_t("Newsletter.Campaign.SendFrom","Send from eMail")),
			new TextField("TemplateFilename", _t("Newsletter.Campaign.TemplateFilename","Name of template (no .ss)")),
			new TextField("BodyStyle",_t("Newsletter.Campaign.BodyStyle","Body StyleSheet")),
			new TextField("ContentStyle",_t("Newsletter.Campaign.Content","Content StyleSheet")),
			new TextField("LinkStyle", _t("Newsletter.Campaign.LinkStyle","Style for every link [a]")),
			new TextField("ImageStyle", _t("Newsletter.Campaign.ImageStyle","Style for every image [img]")),
			new TextField("ParagraphStyle", _t("Newsletter.Campaign.ParagraphStyle","Style for every paragraph [p]")),
			new TextField("HeadingStyle", _t("Newsletter.Campaign.HeadingStyle","Style for heading [h2]")),
			new TextField("HorizontalRuleStyle", _t("Newsletter.Campaign.HorizontalRuleStyle","Style for every [hr]")),
			new TextField("TableStyle", _t("Newsletter.Campaign.TableStyle","Style for every table [table]")),
			new TextField("TableCellAttribute", _t("Newsletter.Campaign.TabelCellAttribute","[td] CellAtribute")),
			new TextField("TableCellStyle", _t("Newsletter.Campaign.Style","[td] Stylesheet")),
			new DropdownField(
				'NewsletterCategoryID',
				_t("Newsletter.Campaign.NewsletterCategory","Belongs to this newsletter category"),
				$categories->toDropdownMap('ID', 'Title', 'Bitte Newsletterkategorie eintragen', true)
				),
			));
		$tablefield = new ComplexTableField(
			$controller = $this,
			$name = 'Recievers',
			'NewsletterReciever',
			$fieldList = array(
				'FirstName'=>_t("Newsletter.Member.FirstName","FirstName"),
				'Surname'=>_t("Newsletter.Member.Surname","Surame"),
				'Email'=>_t("Newsletter.Member.Email","eMail"),
				'Send'=>_t("Newsletter.Admin.MailSended","eMail sended (0=no/1=yes)"),
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
		$fields->addFieldToTab("Root.Content."._t("Newsletter.Admin.RecieverList","List of recievers"), $tablefield);
		
		//Blacklist
		$tablefield = new ComplexTableField(
			$controller = $this,
			$name = 'Blacklist',
			'NewsletterBlacklist',
			$fieldList = array(
				'Email'=>_t("Newsletter.Member.Email","eMail"),
				'NewsletterCategory.Title'=>_t("Newsletter.NewsletterCategory","Newsletter Category"),
			),
			null,
			$sourceFilter = "NewsletterCategoryID = $this->NewsletterCategoryID"
		);
		$tablefield->setPermissions(
				array(
					"show",
					"edit",
					"delete",
					"add",
				)
			);
		$tablefield->setParentClass(false);
		$fields->addFieldToTab("Root.Content."._t("Newsletter.Admin.BlackList","Blacklist"), $tablefield);
		
		//Subscribers
		$tablefield = new ComplexTableField(
			$controller = $this,
			$name = 'Subscribers',
			'NewsletterMember',
			$fieldList = array(
				'Email'=>_t("Newsletter.Member.Email","eMail"),
				'NewsletterCategory.Title'=>_t("Newsletter.NewsletterCategory","Newsletter Category"),
			),
			null,
			$sourceFilter = "NewsletterCategoryID = $this->NewsletterCategoryID"
		);
		$tablefield->setPermissions(
				array(
					"show",
					"edit",
					"delete",
					"add",
				)
			);
		$tablefield->setParentClass(false);
		$fields->addFieldToTab("Root.Content."._t("Newsletter.Admin.Subscribers","Subscribers"), $tablefield);
		return $fields;
	}
	
	function parentHolderPage() {
		//search three levels max. above
		$page = null;
		if ($this->ParentID>0) {
			$page = $this->Parent();
			if ($page->ClassName=="NewsletterHolder") return $page;
			if ($page->ParentID>0) {
				$page = $page->Parent();
				if ($page->ClassName=="NewsletterHolder") return $page;
				if ($page->ParentID>0) {
					$page = $page->Parent();
					if ($page->ClassName=="NewsletterHolder") return $page;
				}
			}
		}
		return $page;
	}
	
	function getSendFromParent() {
		$sendFrom = NewsletterHolder::$newsletterEmail;
		if ($h=$this->parentHolderPage()) {
			$sendFrom = $h->sendFromEmail();
		}
		return $sendFrom;
	}
	
	function sendFromEmail() {
		return ($this->SendFrom) ? $this->SendFrom : $this->getSendFromParent();
	}
	
	function getNewsletterTemplate() {
		return ($this->TemplateFilename) ? $this->TemplateFilename : $this->parentHolderPage()->getNewsletterTemplate();
	}
	
	function parentNewsletterCategory() {
		$p = $this->parentHolderPage();
		if ($p->Class = "NewsletterCategory") return $p;
	}
	
	function renderedNewsletter($member = null) {
		return self::getRenderedNewsletterContent($this, $member);
	}
	
	function renderedNewsletterOnlyText($member = null) {
		return str_replace("\n","<br/>",strip_tags(self::getRenderedNewsletterContent($this, $member)));
	}
	
	static function stripSSFromFilename($filename) {
		return preg_replace("/(.*)(\.ss)/i","$1",$filename);
	}
	
	static function getRenderedNewsletterContent($campaignPage, $member = null) {
		$param = ($member) ? "memberid=".$member->ID."&mail=".$member->Email : "";
		$link = Director::makeRelative($campaignPage->Link());
		$response = Director::test($link."/render/?".$param);
		$content = $response->getBody();
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
			$content = preg_replace('#(<hr(.*)[/]?>)#U', '<hr \2 style="'.$campaignPage->HorizontalRuleStyle.'" />', $content); 
		}
		if (self::$makeRelativeToAbsoluteURLS) {
			$base = Director::absoluteBaseURL();
			$s = $content;
			$sl = "\'";
			$s = str_replace('src="assets/','src="'.ViewableData::baseHref().'assets/',$s);
			$s = str_replace('href="assets/','href="'.ViewableData::baseHref().'assets/',$s);
			$s = str_replace('/render/?','?',$s);
			$base = Director::protocolAndHost();
			$s=preg_replace('#(href)="([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#','$1="'.$base.'$2$3',$s);
			$content = $s;
		}
		return $content;
	}
	
	function onBeforeWrite() {
		//strip .ss extension, if used for filenames
		if ($this->TemplateFilename) $this->TemplateFilename = self::stripSSFromFilename($this->TemplateFilename);
		if ($this->EmailBodyTemplate) $this->EmailBodyTemplate = self::stripSSFromFilename($this->EmailBodyTemplate);
		return parent::onBeforeWrite();
	}
	
	//Not in use
	static function isValidEmail($email) {
		return eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $email);
	}
	
	function sendTo($to,$from=null) {
		if ($from==null) $from = $this->sendFromEmail();
		if (Permission::check("EDIT_NEWSLETTER")) {
			$content = $this->renderedNewsletter();
			$emailMessage = new Email($this->sendFromEmail(), $to, $this->Title);
			$emailMessage->setBody($content);
			if ($this->customEmailBodyTemplate()) $emailMessage->setTemplate($this->customEmailBodyTemplate());
			$emailMessage->send();
			//newsletter is sended
			return true;
		} else {
			user_error("No permission ['EDIT_NEWSLETTER'] to send newsletter...");
		}
	}
	
	function customEmailBodyTemplate() {
		if ($this->EmailBodyTemplate) return $this->EmailBodyTemplate;
		if ($p=$this->parentHolderPage()) {
			if ($p->customEmailBodyTemplate()) return $p->customEmailBodyTemplate(); 
		}
		return null;
	}
	
	function subscribers() {
		return DataObject::get("NewsletterMember","NewsletterCategoryID = ".$this->NewsletterCategoryID." AND LENGTH(Email)>0");
	}
		
}

class NewsletterCampaign_Controller extends ContentController {
	
	static $allowed_actions = array(
		"render",
		"NewsletterNavigatorForm"=>"EDIT_NEWSLETTER",
		"send_to" => "EDIT_NEWSLETTER",
		);
	
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
		exit(NewsletterCampaign::getRenderedNewsletterContent($this->dataRecord));
	}
	
	function render() {
		//checks email+id
		if (isset($_REQUEST['memberid'])) {
			if ($id=(int) $_REQUEST['memberid']) {
				$this->Member = DataObject::get_by_id("NewsletterReciever",$id);
				if (!(trim($_REQUEST['mail'])==trim($this->Member->Email))) exit("<p>Your mail  <b>doesn't match</b> to the member email...</p><p><h4>Syntax:</h4>?memberid=<i>\$MemberID</i>&mail=<i>\$MemberEmail</i>");
			}
		}
		if ($template = $this->dataRecord->getNewsletterTemplate()) return $this->renderWith($template); 
	}
	
	function isPreviewMode() {
		return ((isset($_REQUEST['send_to'])) OR (isset($_REQUEST['preview'])) /*OR (strtolower(Director::urlParam("Action"))=="preview")*/);
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
			echo(_t("Newsletter.Admin.NoValidEmail","Please choose a valid eMail address..."));
			exit();
		}
		$this->dataRecord->sendTo($email);
		
		echo _t("Newsletter.Admin.SendTestmailTo","Send testmail to")." '".$email."'...";
		exit();
		
	}
	
	function memberIsNewsletterAdmin() {
		return Permission::check("EDIT_NEWSLETTER");
	}
	
	function isPreviewForAdmin() {
		return (($this->memberIsNewsletterAdmin()) && ($this->isPreviewMode()));
	}
	
	function NewsletterURL() {
		return ($this->dataRecord->parentHolderPage()->URLSegment);
	}
				
}

?>