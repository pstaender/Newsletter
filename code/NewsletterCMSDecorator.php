<?php

class NewsletterCMSDecorator extends LeftAndMainDecorator {
	
	function doRemoveAllRecievers() {
		$id = (int)$_REQUEST['ID'];
		$recievers = DataObject::get("NewsletterReciever","NewsletterID = ".(int) $id);
		$i=0;
		foreach ($recievers as $r) {
			$r->delete();
			$i++;
		}
		FormResponse::add("$('Form_EditForm').getPageFromServer($id);");
		FormResponse::status_message(sprintf(_t("Newsletter.Admin.RemovedAllRecievers","%s recievers removed..."),$i),'good');
		return FormResponse::respond();
	}
		
	function doImportSubscribers() {
		$id = (int) $_REQUEST['ID'];
		$c = DataObject::get_by_id('NewsletterCampaign', $id);
		
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
		FormResponse::add("$('Form_EditForm').getPageFromServer($c->ID);");
		FormResponse::status_message(sprintf(_t("Newsletter.Admin.ImportedSubscribers","Imported %s subscribers to recieverlist..."),$i),'good');
		return FormResponse::respond();
	}
	
	function doImportBatchList() {
		$text = $_REQUEST['RecieverImportList'];
		$id = (int) $_REQUEST['ID'];
		$campaign = DataObject::get_by_id("NewsletterCampaign", $id);
		$text = str_replace(",",";",$text);
		$text = str_replace(";;","; ;",$text);
		if ($lines = explode("\n",$text)) {
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
		}
		
		FormResponse::status_message(
			sprintf(_t("Newsletter.Admin.ImportedAdresses","Imported %s adresses..."),$i),
			'good'
		);
		FormResponse::add("$('Form_EditForm').getPageFromServer($campaign->ID);");
		return FormResponse::respond();
	}
}

?>