<?php
/**
 * Newsletter Holder
 * Is a config page in sitetree, to manage all newsletter campaigns
 * should have the urlsegment "newsletter"
 *
 * @author Philipp Staender <philipp.staender@gmail.com>
 */
class NewsletterHolder extends SiteTree {
	
	static $db = array(
		"SendFrom"=>"Varchar(200)",
		"EmailBodyTemplate"=>"Varchar(100)",
		);
	
	static $has_many = array(
		"Blacklist"=>"NewsletterBlacklist"
	);
		
	static $newsletterEmail = "admin@127.0.0.1";
	static $emailBodyTemplate = null;
	static $signupRequiredFields = array("Email");
	static $newsletterTemplate = "NewsletterTemplate";
	
	static $icon = 'newsletter/images/icons/NewsletterHolder';
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldsToTab('Root.Content.Newsletters',array(
			new EmailField("SendFrom",_t("Newsletter.Admin.SendFrom","Send from (eMail-Adress)")),
			new TextField("EmailBodyTemplate", _t("Newsletter.Admin.EmailBodyTemplate", "Use a custom eMail body template"))
			));
		return $fields;
	}
	
	function sendFromEmail() {
		return $this->SendFrom ? $this->SendFrom : self::$newsletterEmail;
	}
	
	function onBeforeWrite() {
		if ($this->EmailBodyTemplate) $this->EmailBodyTemplate = self::stripSSFromFilename($this->EmailBodyTemplate);
		return parent::onBeforeWrite();
	}
	
	function customEmailBodyTemplate() {
		if ($this->EmailBodyTemplate) return $this->EmailBodyTemplate;
		return null;
	}
	
	static function cleanupSignups($olderThanInSecs=604800) {
		$timeLimit = time()-$olderThanInSecs;
		$timeLimit = date("Y-m-d H:i:s",$timeLimit);
		$members = DataObject::get("NewsletterMember","Confirm LIKE '%@%' AND LastEdited < '".$timeLimit."'");
		$i=0;
		foreach ($members as $m) {
			$m->delete();
			$i++;
		}
		// exit($timeLimit."");
		return $i;
	}
	
	function getNewsletterTemplate() {
		return self::$newsletterTemplate;
	}
	
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if($this->class == 'NewsletterHolder') {
			if(!DataObject::get($this->class)) {
				$n = new NewsletterHolder();
				$n->Title = "Newsletter";
				$n->URLSegment = "newsletter";
				$n->SendFrom = self::$newsletterEmail;
				$n->write();
				Database::alteration_message("newsletter holder created","created");		
			}
		}
	}
	
}

class NewsletterHolder_Controller extends Page_Controller {
	
	static $allowed_actions = array(
		"signup",
		"unsubscribe",
		"confirm",
		"preview"=>"EDIT_NEWSLETTER",
		"send"=>"EDIT_NEWSLETTER",
		"admin"=>"EDIT_NEWSLETTER",
		"ImportDefaultsForm"=>"EDIT_NEWSLETTER",
		"edit_recievers"=>"EDIT_NEWSLETTER",
		"RecieverForm"=>"EDIT_NEWSLETTER",
		"SignupForm",
		);
	
	function init() {
		parent::init();
		NewsletterHolder::cleanupSignups(600);
	}
	
	function UrlID() {
		return Director::urlParam("ID");
	}
	
	function SelectedNewsletterCampaign() {
		return DataObject::get_by_id("NewsletterCampaign",(int) $this->UrlID());
	}
	
	function confirm() {
		// ?hash={$hash}&email={$email}
		if (!((isset($_REQUEST['hash'])) && (isset($_REQUEST['email'])))) return array(); 
		$hash = trim(Convert::Raw2SQL($_REQUEST['hash']));
		$email = trim(Convert::Raw2SQL($_REQUEST['email']));
		$this->ConfirmSuccessfull = false;
		if ((strlen($email)>0) && (strlen($hash)>0)) {
			if ($member = DataObject::get_one("NewsletterMember","Hash LIKE '{$hash}' AND Confirm LIKE '{$email}'")) {
				$member->Email = $email;
				$member->Hash = $hash;
				$member->Confirm = "";
				$member->write();
				$this->ConfirmSuccessfull = true;
				$this->Member = $member;
				$this->Title = _t("Newsletter.ThanksForSignup","Thanks for your signup!");
				$others = DataObject::get("NewsletterMember","Confirm LIKE '{$email}' AND NewsletterCategoryID = ".$member->NewsletterCategoryID);
								foreach ($others as $o) $o->delete();
			} else {
				$this->ConfirmSuccessfull = true;
				$this->Title = "Error";
			}
		}
		return array();
	}
		
	function unsubscribe() {
		//unsubcribe/email/categoryid
		$this->Title = _t('Newsletter.Unsubscribe','Unsubscribe newsletter');
		if (($email = Director::urlParam("ID")) && ($otherID = Director::urlParam("OtherID"))) {
			$email = Convert::Raw2SQL($email);
			$otherID = (int) $otherID;
			if (!DataObject::get_one("NewsletterBlacklist","Email LIKE '{$email}' AND NewsletterCategoryID = {$otherID}")) {
				$bl = new NewsletterBlacklist();
				$bl->Email = $email;
				$bl->NewsletterCategoryID = $otherID;
				$bl->write();
				$this->EmailOnBlacklist = true;
			}
			if ($m = DataObject::get_one("NewsletterMember","Email LIKE '{$email}' AND NewsletterCategoryID = {$otherID}")) {
				if ($bl) {
					$bl->RecieverID = $m->ID;
					$bl->write();
				}
				
				$m->delete();
				$this->NewsletterUnsubscribed = true;
			} else {
				//not in list
				$this->EmailNotInList = true;
			}
		}
		if ((!$this->EmailNotInList) && (!$this->NewsletterUnsubscribed)) $this->NoValidRequest = true;
		return array();
	}
	
	function selectedNewsletterCategory() {
		if (Director::urlParam("ID")) {
			$param = Director::urlParam("ID");
			if (((int) $param)>0) return DataObject::get_by_id("NewsletterCategory",(int)$param);
			if ($category = DataObject::get_one("NewsletterCategory","Title LIKE '".Convert::raw2SQL($param)."'")) return $category; 
		}
		return DataObject::get("NewsletterCategory");
	}
	
	function SignupForm() {
		$n = $this->selectedNewsletterCategory();
		if ($n->ClassName!='NewsletterCategory') $newsletter = new DropdownField("NewsletterCategoryID","Newsletter",$n->toDropdownMap('ID', 'Title', 'Bitte Newsletter auswählen', true));
		else $newsletter = new HiddenField("NewsletterCategoryID","NewsletterCategoryID",$n->ID);
		$g = singleton('NewsletterMember')->dbObject('Gender')->enumValues();
		$gender = array();
		foreach ($g as $value => $field) {
			$gender[$field]=_t("Newsletter.Gender.$value",$value);
		}
		$fields = new FieldSet(
				new EmailField("Email","<strong>"._t("Newsletter.Email","eMail")."</strong>"),
				new TextField("FirstName",_t("Newsletter.Member.FirstName","Firstname")),
				new TextField("Surname",_t("Newsletter.Member.Surname","Surname")),
				new DropdownField("Gender",_t("Newsletter.Member.Salutation","Salutation"),$gender),
				$newsletter
			);
		return new Form(
			$this,
			"SignupForm",
			$fields,
			new FieldSet(new FormAction("doSubmitSignupForm", "Eintragen")),
			new RequiredFields(
				NewsletterHolder::$signupRequiredFields
			)
		);
	}
	
	function doSubmitSignupForm($data,$form) {
		$email =  Convert::Raw2SQL($data['Email']);
		// exit($email);
		$firstName = Convert::Raw2SQL($data['FirstName']);
		$surname = Convert::Raw2SQL($data['Surname']);
		$gender = Convert::Raw2SQL($data['Gender']);
		$id = (int) $data['NewsletterCategoryID'];
		$newsletterCategory = DataObject::get_by_id("NewsletterCategory",(int) $id);
		$sql = "Email LIKE '{$email}' AND NewsletterCategoryID = ".$id;
		if ($m = DataObject::get("NewsletterMember", $sql)) {
			$this->AlreadySignedUp = true;
		} else {
			$this->ConfirmMailSended = true;
			$newsletterPage = DataObject::get_one("NewsletterHolder");
			$n = new NewsletterMember();
			$hash = $n->Hash = substr(md5(time().rand(0,10000).$email),0,8);
			$n->Email = "";
			$n->Confirm = $email;
			$n->Surname = $surname;
			$n->FirstName = $firstName;
			$n->Gender = $gender;
			$n->NewsletterCategoryID = $id;
			$n->write();
			$this->Member = $n;
			if ($m = DataObject::get("NewsletterBlacklist",$sql)) {
				foreach($m as $mm) $mm->delete();
			}
			$this->Title = $title = _t("Newsletter.Mail.SignupTitle", "Thanks for you signup for our newlsetter");
			$emailMessage = new Email(DataObject::get_one("NewsletterHolder")->sendFromEmail(), $email, $title);
			$emailMessage->setTemplate('NewsletterMail_SignupMessage');
			$emailMessage->populateTemplate(array(
				"Member" => $n,
				"ConfirmURL" => $url = $newsletterPage->URLSegment."/confirm/?hash={$hash}&email={$email}",
				"ConfirmLink" => '<a href="'.ViewableData::baseHref().$url.'">'.ViewableData::baseHref().$url.'</a>',
				"Newsletter" => $newsletterCategory,
				"NewsletterCategory" => $newsletterCategory
				));
			$emailMessage->send();
		}
		return array();
	}
	
	function URL() {
		return $this->URLSegment;
	}
	
	function SendLink($send=10) {
		return $this->URL()."/send/".Director::urlParam("ID")."/".$send;
	}
	
	function Campaigns() {
		return DataObject::get("NewsletterCampaign");
	}
	
	/**
	 * ADMIN STUFF
	 *
	 * @author Philipp Staender
	 */
		
	function send() {
		if (!$this->isAjax()) exit("<h2>send() only via ajax-request</h2>Security issue...");
		$id = (int) Director::urlParam("ID");
		$count = (int) Director::urlParam("OtherID");
		if(!(($id>0) && ($count>0))) exit('<h2>Syntax</h2>'.$this->URL().'/send/$newsletter_campaign_id/$numbers_of_sendings_per_request/');
		if ($camp = DataObject::get_by_id("NewsletterCampaign",$id)) {
			$newsletterCategory = DataObject::get_by_id("NewsletterCategory",$camp->NewsletterCategoryID);
			if ($recievers = DataObject::get("NewsletterReciever", "NewsletterID = {$id} AND Send = 0")) {
					//send emails
					$i=0;
					//to each reciever
					foreach ($recievers as $r) {
						//send only, if sended items of this session are smaller than given in the url
						$content=NewsletterCampaign::getRenderedNewsletterContent($camp,$r);
						if ($i<$count) {
							if (DataObject::get("NewsletterBlacklist","Email LIKE '".$r->Email."' AND NewsletterCategoryID = ".$camp->NewsletterCategoryID)) {
									$r->Send = 2;
									$r->write();
									//do not send
								} else {
									$email = new Email($camp->sendFromEmail(), $r->Email, $camp->Title, $content);
									if (NewsletterHolder::$emailBodyTemplate) $email->setTemplate(NewsletterHolder::$emailBodyTemplate);
									if ($email->send()) {
										$r->Send = 1;
										$r->write();
										$i++;
									}
								}
						}
						
					}
					exit($i."");
			}
		}
		return array();
	}
	
	function ImportDefaultsForm() {
		return new Form(
			$this,
			"ImportDefaultsForm",
			new Fieldset(
				new HiddenField("ID", "ID", Director::urlParam("ID"))
			),
			new FieldSet(
				new FormAction('doSubmitImportDefaults', _t("Newsletter.Admim.DoImportDefaults","Do import"))
			),
			new RequiredFields('ID')
		);
	}
	
	function doSubmitImportDefaults($data, $form) {
		//import all subscribers into reciever list
		if ($id = $data['ID']) {
			if ($c = DataObject::get_by_id("NewsletterCampaign", (int) $id)) {
				$recievers = $c->Recievers();
				$subscribers = $c->Subscribers();
				$i=0;
				//delete status=2 mails
				foreach ($recievers as $r) {
					echo($r->Send."");
					if ($r->Send==2) $r->delete();
				}
				foreach ($subscribers as $s) {
					//check for duplicates
					if ((!$recievers->find('Email', $s->Email)) && ($s->Email)) {
						$r = new NewsletterReciever();
						$r->Email = $s->Email;
						$r->FirstName = $s->FirstName;
						$r->Gender = $s->Gender;
						$r->Surname = $s->Surname;
						$r->NewsletterID = $id;
						$r->write();
						$i++;
					}
				}
				$form->sessionMessage(
					sprintf(_t("Newsletter.Admin.ImportDefaults","Imported %s adresses..."),$i),
					'good'
				);
			}
	      	Director::redirectBack();
		}
		return array();
	}
	
	function DeleteForm() {
		return new Form(
			$this,
			"ImportDefaultsForm",
			new Fieldset(
				new HiddenField("ID", "ID", Director::urlParam("ID"))
			),
			new FieldSet(
				new FormAction('doSubmitDeleteForm', _t("Newsletter.Admim.DoDeleteAllRecievers","Delete all"))
			),
			new RequiredFields('ID')
		);
	}
	
	function doSubmitDeleteForm($data,$form) {
		if ($id = $data['ID']) {
			if ($c = DataObject::get_by_id("NewsletterCampaign", (int) $id)) {
				$recievers = DataObject::get("NewsletterReciever","NewsletterID = ".(int) $id);
				$i=0;
				foreach ($recievers as $r) {
					$r->delete();
					$i++;
				}
				$form->sessionMessage(
					sprintf(_t("Newsletter.Admin.DeleteAllRecievers","%s recievers deleted..."),$i),
					'good'
				);
			}
		} else {
			$form->sessionMessage(
				_t("Newsletter.Admin.NoRecordSelected","No valid record selected..."),
				'bad'
			);
		}
		Director::redirectBack();
	}
	
	function edit_recievers() {
		if (!$id = Director::urlParam("ID")) user_error("Please choose an ID");
		return array();
	}
	
	function RecieverForm() {
		return new Form(
			$this,
			"RecieverForm",
			$fields = new FieldSet(
				new TextareaField("Text","Adressenliste",10),
				new HiddenField("ID","ID",$this->UrlID())
			),
			new FieldSet(new FormAction("doSubmitRecieverForm", "Hinzufügen")),
			new RequiredFields(
				"Text"
			)
		);
	}
	
	function doSubmitRecieverForm($data,$form) {
		$text = $data['Text'];
		$text = str_replace(",",";",$text);
		$text = str_replace(";;","; ;",$text);
		if ($lines = explode("\n",$text)) {
			
			$id = (int) $data['ID'];
			$str = "";

			$firstLine = $lines[0];
			$fields = explode(";",$firstLine);
			
			$j=0;
			$table = array();
			foreach($fields as $f) {
				$f = strtolower(trim($f));
				if (($f=="sex")||($f=="gender")||($f=="geschlecht")) $table['gender']=$j;
				if (($f=="first")||($f=="firstname")||($f=="vorname")) $table['firstname']=$j;
				if (($f=="sur")||($f=="surname")||($f=="nachname")) $table['surname']=$j;
				if (($f=="email")||($f=="mail")) $table['mail']=$j;
				$j++;
			}
			$i=0;
			foreach($lines as $line) {
				$line = trim($line);
				if ($segments = explode(";",$line)) {
					$firstname = $surname = $gender = null;
					if (isset($table['firstname'])) $firstname = Convert::Raw2SQL(trim($segments[$table['firstname']]));
					if (isset($table['surname'])) $surname = Convert::Raw2SQL(trim($segments[$table['surname']]));
					if (isset($table['gender'])) $gender = Convert::Raw2SQL(trim($segments[$table['gender']]));
					//serach all segments for email
					foreach ($segments as $s) {
						$email = trim($s);
						if (eregi("^[a-z0-9]+([-_\.]?[a-z0-9])+@[a-z0-9]+([-_\.]?[a-z0-9])+\.[a-z]{2,4}", $email)) {
							if (!DataObject::get("NewsletterReciever","NewsletterID = {$id} AND Email LIKE '{$email}'")) {
								$i++;
								$r = new NewsletterReciever();
								$r->FirstName = $firstname;
								$r->Surname = $surname;
								$r->Gender = $gender;
								$r->NewsletterID = $id;
								$r->Send = 0;
								$r->Email = $email;
								$r->write();
								$str .= "<strong>".$r->Email."</strong> ($gender $firstname $surname)  hinzugefügt...<br/>\n";								
							}
						}
					}
				}
			}
			$form->sessionMessage(
				$str."<p>{$i} neu eingetragen</p>",
				'good'
			);
		}
      	Director::redirectBack();
	}

}

?>