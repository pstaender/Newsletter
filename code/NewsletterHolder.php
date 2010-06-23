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
		"ReturnAddress"=>"Varchar(200)",
		);
		
	// static $allowed_children = array(
		// "NewsletterCategory",
		// "NewsletterCampaign",
		// );
	
	static $has_many = array(
		"Blacklist"=>"NewsletterBlacklist"
		);
		
	static $field_labels = array(
			"ReturnAddress"=>"Absender eMail (z.B. newsletter@example.com)",
			"ConfirmMessageTitle"=>"Betreff des Bestätigungsnachricht",
			"ConfirmMessage"=>"Bestätigungsnachricht",
			"UnsubscribeMessage"=>"Abmeldungsnachricht",
		);
	
	static $newsletterEmail = "newsletter@b-p-p.info";
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldsToTab('Root.Content.Newsletter',array(
			new EmailField('ReturnAddress',self::$field_labels['ReturnAddress']),
			new LiteralField('ConfirmLegend','
			<h4>Placeholders:</h4>
			<p>%FIRSTNAME%</p>
			<p>%SURNAME%</p>
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
	
	function getManagedReturnAddress() {
		return $this->ReturnAddress ? $this->ReturnAddress : self::$newsletterEmail;
	}
	
}

class NewsletterHolder_Controller extends Page_Controller {
	
	static $allowed_actions = array(
		"signup",
		"unsubscribe",
		"confirm",
		"send"=>"ADMIN",
		"admin"=>"ADMIN",
		"import_defaults"=>"ADMIN",
		"edit_recievers"=>"ADMIN",
		"RecieverForm"=>"ADMIN",
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
		$hash = trim(Convert::Raw2SQL($_REQUEST['hash']));
		$email = trim(Convert::Raw2SQL($_REQUEST['email']));
		if ((strlen($email)>0) && (strlen($hash)>0)) {
			if ($member = DataObject::get_one("NewsletterMember","Hash LIKE '{$hash}' AND Confirm LIKE '{$email}'")) {
				$member->Email = $email;
				$member->Hash = $hash;
				$member->Confirm = "";
				$member->write();
				$this->Title = "Vielen Dank";
				$this->Content = "<p>Vielen Dank, dass Sie sich für unseren Newsletter eingetragen haben.</p>";
				$others = DataObject::get("NewsletterMember","Confirm LIKE '{$email}' AND NewsletterCategoryID = ".$member->NewsletterCategoryID);
								foreach ($others as $o) $o->delete();
			} else {
				$this->Title = "Fehler";
				$this->Content = "<p>Die angegeben Daten stimme nicht.</p>";
			}
		} else {
			$this->Content = "<p>Es muss eine eMail und ein passender Hash angefordert werden.</p>";
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
		$this->Title = "Für einen Newsletter anmelden";
		if ($nl=DataObject::get("NewsletterCategory")) {
			if ($nl->Count()==1) {
				$nl=DataObject::get_one("NewsletterCategory");
				$newsletter = new HiddenField("NewsletterCategoryID","NewsletterCategoryID",$nl->ID);
				$this->Content .= "Sie melden sich für folgenden Newsletter an:<h3>".$nl->Description."</h3>";
			} else {
					$this->Content = "Wählen Sie einen Newsletter aus, für den Sie sich anmelden möchten";
					$newsletter = $nl->toDropdownMap('ID', 'Title', 'Bitte Newsletter auswählen', true);
					$newsletter = new DropdownField('NewsletterCategoryID', 'Newsletter', $newsletter);
			}
		} else {
			$this->Content = "Es sind keine Newsletter zum anmelden vorhanden.";
			return array();
		}
		$fields = new FieldSet(
				new EmailField("Email","<strong>".NewsletterMember::$field_names['Email']."</strong>"),
				new TextField("FirstName",NewsletterMember::$field_names['FirstName']),
				new TextField("Surname",NewsletterMember::$field_names['Surname']),
				$newsletter
			);
		$this->MetaDescription = new Form(
			$this,
			"NewsletterSignupForm",
			$fields,
			new FormAction("ProceedSignup", "Eintragen"),
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
		$id = (int) $data['NewsletterCategoryID'];
		$newsletterCategory = DataObject::get_by_id("NewsletterCategory",(int) $id);
		$sql = "Email LIKE '{$email}' AND NewsletterCategoryID = ".$id;
		if ($m = DataObject::get("NewsletterMember", $sql)) {
			$this->Title = "Anmeldung bereits erfolgt";
			$this->Content = "
			<p>Sie sind bereits für diesen Newsletter eingetragen.</p>
			<p>Wenn Sie sich für den Newsletter abmelden möchten, benutzen Sie bitte den Abbestellungslink, der in jeder eMail in der Fußzeile steht.</p>
			";
			return array();
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
			
			$emailMessage = new Email(DataObject::get_one("NewsletterHolder")->getManagedReturnAddress(), $email, $title, $content);
			if ($emailMessage->send()) {
				$this->Content = "
				<p>Es wurde eine Bestätigungsmail an <strong>{$email}</strong> gesendet.</p>
				";
			}
		}
		return array();
	}
	
	function URL() {
		return "newsletter";
	}
	
	function SendLink() {
		return $this->URL()."/send/".Director::urlParam("ID");
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
		$this->Title = "Newsletter verschicken";
		$id = (int) Director::urlParam("ID");
		if ($camp = DataObject::get_by_id("NewsletterCampaign",$id)) {
			$newsletterCategory = DataObject::get_by_id("NewsletterCategory",$camp->NewsletterCategoryID);
			if ($recievers = DataObject::get("NewsletterReciever", "NewsletterID = {$id} AND Send = 0")) {

				if ($content=NewsletterCampaign::getRenderedNewsletterContent($camp)) {
					//send emails
					$i=0;
					foreach ($recievers as $r) {
						// todo
						// Send eMail
						$mailContent = str_replace("%USER_EMAIL%",$r->Email, $content);
						$mailContent = str_replace("%NEWSLETTER_ID%",$newsletterCategory->ID, $mailContent);
						if (DataObject::get("NewsletterBlacklist","Email LIKE '".$r->Email."' AND NewsletterCategoryID = ".$camp->NewsletterCategoryID)) {
							$r->Send = 2;
							$r->write();
							//do not send
						} else {
							$email = new Email(DataObject::get_one("NewsletterHolder")->getManagedReturnAddress(), $r->Email, $camp->Title, $mailContent);
							if ($email->send()) {
								$r->Send = 1;
								$r->write();
							}
						}
						$i++;
					}
					$this->Content = "Es wurden {$i} Newsletter verschickt.";
					return array();
				}
				
			} else {
				
			}
		} else {
			$this->Content = "Sie haben keine gültige Newsletterversendung ausgewählt...";
		}
		return array();
	}
	
	function import_defaults() {
		//import all subscribers into reciever list
		if ($id = Director::urlParam("ID")) {
			if ($c = DataObject::get_by_id("NewsletterCampaign", (int) $id)) {
				$recievers = DataObject::get("NewsletterReciever","NewsletterID = ".(int) $id);
				$subscribers = DataObject::get("NewsletterMember","NewsletterCategoryID = ".(int) $c->NewsletterCategoryID);
				$i=0;
				foreach ($subscribers as $s) {
					//check for duplicates
					if (!$recievers->find('Email', $s->Email)) {
						$r = new NewsletterReciever();
						$r->Email = $s->Email;
						$r->FirstName = $s->FirstName;
						$r->Surname = $s->Surname;
						$r->NewsletterID = $id;
						$r->write();
						$i++;
					}//$recievers->push($s);
				}
				$this->Title = "Aboadressen reinladen";
				$this->Content = "Insg. {$i} Adressen reingeladen...";
				// Debug::show($recievers);
			}
		} else {
			$this->Title = "Error";
			$this->Content = "Es ist keine gültige Newsletter Aktion ausgewählt...";
		}
		return array();
	}
	
	function edit_recievers() {
		if ($id = Director::urlParam("ID")) {
			if ($c = DataObject::get_by_id("NewsletterCampaign", (int) $id)) {
				$this->Recievers = DataObject::get("NewsletterReciever","NewsletterID = ".(int) $id);
				$this->Subscribers = DataObject::get("NewsletterMember","NewsletterCategoryID = ".(int) $c->NewsletterCategoryID);
				$fields = new FieldSet(
						new TextareaField("Text","Adressenliste",10),
						new HiddenField("ID","ID",$id)
					);
				$this->Content = new Form(
					$this,
					"RecieverForm",
					$fields,
					new FormAction("ProceedRecieverForm", "Hinzufügen"),
					new RequiredFields(
						"Text"
					)
				);
			} else {
				$this->Content = "Kein gültiger Datensatz ausgewählt";
			}
		} else {
			$this->Content = "Es muss eine Newsletteraktion gewählt werden!";
		}
		return array();
	}
	
	function RecieverForm($data) {
		if ($lines = explode("\n",$data['Text'])) {
			$id = (int) $data['ID'];
			$str = "";
			$i=0;
			foreach($lines as $line) {
				$line = trim($line);
				if ($segments = explode(";",$line)) {
					// $firstname = Convert::Raw2SQL(trim($s[0]));
					// $surname = Convert::Raw2SQL(trim($s[1]));
					foreach ($segments as $s) {
						$email = trim($s);
						// if (strlen($email)>0) $i++;
						if (eregi("^[a-z0-9]+([-_\.]?[a-z0-9])+@[a-z0-9]+([-_\.]?[a-z0-9])+\.[a-z]{2,4}", $email)) {
							if (!DataObject::get("NewsletterReciever","NewsletterID = {$id} AND Email LIKE '{$email}'")) {
								$i++;
								$r = new NewsletterReciever();
								$r->NewsletterID = $id;
								$r->Send = 0;
								$r->Email = $email;
								$r->write();
								$str .= "<strong>".$r->Email."</strong>  hinzugefügt...<br/>\n";								
							}
						}
					}
				}
			}
			$this->Content = $str."<p>{$i} neu eingetragen</p>";
			return array();
		}
	}

}

?>