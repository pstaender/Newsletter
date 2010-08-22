<?php
/**
 * Newsletter Holder
 * Verwaltet die Newsletter
 *
 * @author Philipp Staender
 */
class NewsletterHolder extends SiteTree {
	
	static $db = array(
		"ConfirmMessage"=>"HTMLText",
		"ConfirmMessageTitke"=>"Varchar(200)",
		"UnsubscribeMessage"=>"HTMLText",
		"SendFrom"=>"Varchar(200)",
		);
	
	static $has_many = array(
		"Blacklist"=>"NewsletterBlacklist"
	);
		
	static $field_labels = array(
		"SendFrom"=>"Absender eMail (z.B. newsletter@example.com)",
		"ConfirmMessageTitle"=>"Betreff des Bestätigungsnachricht",
		"ConfirmMessage"=>"Bestätigungsnachricht",
		"UnsubscribeMessage"=>"Abmeldungsnachricht",
	);
	
	static $newsletterEmail = "newsletter@hd-healthsystem.com";
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldsToTab('Root.Content.Newsletter',array(
			new EmailField('SendFrom',_t("Newsletter.CMS.SendFrom","Send From")),
			new LiteralField('ConfirmLegend','
			<h4>Placeholders:</h4>
			<p>%FIRSTNAME%</p>
			<p>%SURNAME%</p>
			<p>%GENDER%</p>
			<p>%NEWSLETTER_TITLE%</p>
			<p>%NEWSLETTER_DESCRIPTION%</p>
			<p>%CONFIRM_URL%</p>
			'),
			new TextField('ConfirmMessageTitle',self::$field_labels['ConfirmMessageTitle']),
			new HtmlEditorField('ConfirmMessage',self::$field_labels['ConfirmMessage']),
			new HtmlEditorField('UnsubscribeMessage',self::$field_labels['UnsubscribeMessage']),
			));
		return $fields;
	}
	
	function sendFrom() {
		return $this->SendFrom ? $this->SendFrom : self::$newsletterEmail;
	}
	
}

class NewsletterHolder_Controller extends Page_Controller {
	
	static $allowed_actions = array(
		"signup",
		"unsubscribe",
		"confirm",
		"send"=>"EDIT_NEWSLETTER",
		"admin"=>"EDIT_NEWSLETTER",
		"ImportDefaultsForm"=>"EDIT_NEWSLETTER",
		"edit_recievers"=>"EDIT_NEWSLETTER",
		"RecieverForm"=>"EDIT_NEWSLETTER",
		"NewsletterSignupForm",
		);
	
	function init() {
		parent::init();
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
				$this->Title = "Vielen Dank";
				$this->Content = "<p>Vielen Dank, dass Sie sich für unseren Newsletter eingetragen haben.</p>";
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
		$this->Title = "Newsletter abbestellen";
		if (($email = Director::urlParam("ID")) && ($otherID = Director::urlParam("OtherID"))) {
			$email = Convert::Raw2SQL($email);
			$otherID = (int) $otherID;
			if (!DataObject::get_one("NewsletterBlacklist","Email LIKE '{$email}' AND NewsletterCategoryID = {$otherID}")) {
				$bl = new NewsletterBlacklist();
				$bl->Email = $email;
				$bl->NewsletterCategoryID = $otherID;
				$bl->write();
			}
			$this->Title = "Newsletter abbestellt";
			$this->Content .= "Sie wurden ausgetragen. ";
			if ($m = DataObject::get_one("NewsletterMember","Email LIKE '{$email}' AND NewsletterCategoryID = {$otherID}")) {
				if ($bl) {
					$bl->RecieverID = $m->ID;
					$bl->write();
				}
				
				$m->delete();
				$this->Content = "Bestätigung: Sie haben den Newsletter abbestellt.";
			} else {
				// $this->Content .= "Sie waren in keiner festen Liste eingetragen...";
			}
			return array();
			
		}
		$this->Content = "Wählen Sie einen Newsletter aus, für den Sie sich abmelden möchten";
		$this->Content = "Sie erhalten in jedem Newsletter einen individuellen abmelde Link.";
		return array();
	}
		
	function signup() {
		$this->Newsletter = null;
		if ($nl=DataObject::get("NewsletterCategory")) {
			if ($nl->Count()==1) {
				$nl=DataObject::get_one("NewsletterCategory");
				$newsletter = new HiddenField("NewsletterCategoryID","NewsletterCategoryID",$nl->ID);
				$this->Newsletter = $nl;
			} else {
					$newsletter = $nl->toDropdownMap('ID', 'Title', 'Bitte Newsletter auswählen', true);
					$newsletter = new DropdownField('NewsletterCategoryID', 'Newsletter', $newsletter);
			}
		} else {
			//No newsletter to signup for
		}
		$fields = new FieldSet(
				new EmailField("Email","<strong>"._t("Newsletter.Email","eMail")."</strong>"),
				new TextField("FirstName",_t("Newsletter.Member.FirstName","Firstname")),
				new TextField("Surname",_t("Newsletter.Member.Surname","Surname")),
				new TextField("Gender",_t("Newsletter.Member.Gender","Gender")),
				$newsletter
			);
		$this->Form = new Form(
			$this,
			"NewsletterSignupForm",
			$fields,
			new FieldSet(new FormAction("ProceedSignup", "Eintragen")),
			new RequiredFields(
				"Email", "FirstName", "Surname"
			)
		);
		return array();
	}
	
	function NewsletterSignupForm($data) {
		$email =  Convert::Raw2SQL($data['Email']);
		$firstName = Convert::Raw2SQL($data['FirstName']);
		$surname = Convert::Raw2SQL($data['Surname']);
		$gender = Convert::Raw2SQL($data['Gender']);
		$id = (int) $data['NewsletterCategoryID'];
		$newsletterCategory = DataObject::get_by_id("NewsletterCategory",(int) $id);
		$sql = "Email LIKE '{$email}' AND NewsletterCategoryID = ".$id;
		if ($m = DataObject::get("NewsletterMember", $sql)) {
			$this->AlreadySignedUp = true;
		} else {
			$newsletterPage = DataObject::get_one("NewsletterHolder");
			$n = new NewsletterMember();
			$hash = $n->Hash = substr(md5(time().rand(0,10000).$email),0,8);
			$n->Email = "";
			$n->Confirm = $email;
			$n->Surname = $surname;
			$n->FirstName = $firstName;
			$n->NewsletterCategoryID = $id;
			$n->write();
			if ($m = DataObject::get("NewsletterBlacklist",$sql)) {
				foreach($m as $mm) $mm->delete();
			}
			
			$content = $newsletterPage->ConfirmMessage;
			
			$searches = array(
				'/%FIRSTNAME%/',
				'/%SURNAME%/',
				'/%EMAIL%/',
				'/%CONFIRM_URL%/',
				'/%NEWSLETTER_TITLE%/',
				'/%NEWSLETTER_DESCRIPTION%/',
				);
				
			$replaces = array(
				$firstName,
				$surname,
				$email,
				Director::baseURL().$newsletterPage->URLSegment."/confirm/?hash={$hash}&email={$email}",
				$newsletterCategory->Title,
				$newsletterCategory->Description,
				);
			
			$content = preg_replace($searches,$replaces,$content);
			$title = preg_replace($searches,$replaces,$newsletterPage->ConfirmMessageTitle);
			
			$emailMessage = new Email(DataObject::get_one("NewsletterHolder")->sendFrom(), $email, $title, $content);
			if ($emailMessage->send()) {
				$this->ConfirmMailSended = true;
				$this->Email = $email;
			}
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
		if (!$this->isAjax()) echo "<h2>send() only via ajax-request</h2>Security issue...";
		$id = (int) Director::urlParam("ID");
		$count = (int) Director::urlParam("OtherID");
		if(!(($id>0) && ($count>0))) exit('<h2>Syntax</h2>'.$this->URL().'/send/$newsletter_campaign_id/$numbers_of_sendings_per_request/');
		if ($camp = DataObject::get_by_id("NewsletterCampaign",$id)) {
			$newsletterCategory = DataObject::get_by_id("NewsletterCategory",$camp->NewsletterCategoryID);
			if ($recievers = DataObject::get("NewsletterReciever", "NewsletterID = {$id} AND Send = 0")) {
				if ($content=NewsletterCampaign::getRenderedNewsletterContent($camp)) {
					//send emails
					$i=0;
					foreach ($recievers as $r) {
						//send only, if sended items of this session are smaller than given in the url
						if ($i<$count) {
							$mailContent= $content;
							$mailContent = str_replace("%FIRSTNAME%",$r->FirstName, $mailContent);
							$mailContent = str_replace("%SURNAME%",$r->Surname, $mailContent);
							$mailContent = str_replace("%SALUTATION%",$r->Salutation(), $mailContent);
							$mailContent = str_replace("%USER_EMAIL%",$r->Email, $mailContent);
							$mailContent = str_replace("%NEWSLETTER_ID%",$newsletterCategory->ID, $mailContent);
							if (DataObject::get("NewsletterBlacklist","Email LIKE '".$r->Email."' AND NewsletterCategoryID = ".$camp->NewsletterCategoryID)) {
									$r->Send = 2;
									$r->write();
									//do not send
								} else {
									$email = new Email($camp->sendFrom(), $r->Email, $camp->Title, $mailContent);
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
				
			} else {
				
			}
		} else {
			//no id selected
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