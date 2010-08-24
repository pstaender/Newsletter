<?php

/**
 * German (Germany) language pack
 * @package shopsystem
 * @subpackage i18n
 */

global $lang;

if(array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
	$lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
	$lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['Newsletter.Gender']['m']=$lang['de_DE']['Newsletter.Gender']['Male'] = "Herr";
$lang['de_DE']['Newsletter.Gender']['f']=$lang['de_DE']['Newsletter.Gender']['Female'] = "Frau";
$lang['de_DE']['Newsletter.Gender']['-'] = "Nicht angegeben";
$lang['de_DE']['Newsletter.Member']['FirstName'] = "Vorname";
$lang['de_DE']['Newsletter.Member']['Gender'] = "Geschlecht";
$lang['de_DE']['Newsletter.Member']['Salutation'] = "Anrede";
$lang['de_DE']['Newsletter.Member']['Surname'] = "Nachname";
$lang['de_DE']['Newsletter.Campaign']['SendTestTo'] = "Testmail versenden";
$lang['de_DE']['Newsletter.Admim']['DoImportDefaults'] = "Importieren";
$lang['de_DE']['Newsletter.Admim']['ImportDefaults'] = "Es wurden insg. %s Adressen importiert.";
$lang['de_DE']['Newsletter.Admin']['SendFrom']="Absender eMail-Adresse";
$lang['de_DE']['Newsletter.Admin']['EmailBodyTemplate']="eMail Body Template (ohne .ss)";
$lang['de_DE']['Newsletter.Admin']['RecieverList']="Empfängerliste";
$lang['de_DE']['Newsletter.Admin']['MailSended']="Email gesendet (0=nein/1=ja)";
$lang['de_DE']['Newsletter.Admin']['SendTestmailTo']="Testmail wurde versand an";
$lang['de_DE']['Newsletter.Mail']['SignupTitle'] = "Vielen Dank für Ihre Newsletteranmeldung";
$lang['de_DE']['Newsletter.Admin']['NoValidEmail'] = "Bitte geben Sie eine gültige eMail-Adresse an";
$lang['de_DE']['Newsletter.Campaign']['Name']="Name der Newsletterversendung";
$lang['de_DE']['Newsletter.Campaign']['SendFrom']="Absender eMail-Adresse";
$lang['de_DE']['Newsletter.Campaign']['TemplateFilename']="Template Dateiname (ohne .ss)";
$lang['de_DE']['Newsletter.Campaign']['BodyStyle']="Bodystylesheet";
$lang['de_DE']['Newsletter.Campaign']['ContentStyle']="Contentstylesheet";
$lang['de_DE']['Newsletter.Campaign']['LinkStyle']="Link Stylesheet für alle [a]";
$lang['de_DE']['Newsletter.Campaign']['ImageStyle']="Image Stylesheet für alle [img]";
$lang['de_DE']['Newsletter.Campaign']['ParagraphStyle']="Absatz Stylesheet für alle [p]";
$lang['de_DE']['Newsletter.Campaign']['HeadingStyle']="Überschr. Stylesheet für aller [h2]";
$lang['de_DE']['Newsletter.Campaign']['HorizontalRuleStyle']="Linien Stylesheet für alle [hr]";
$lang['de_DE']['Newsletter.Campaign']['TableStyle']="Tabellen Stylsheet für alle [table]";
$lang['de_DE']['Newsletter.Campaign']['TableCellAttribute']="Spaltenattribute für alle [td]";
$lang['de_DE']['Newsletter.Campaign']['TableCellStyle']="Spalten Stylesheet für alle [td]";
$lang['de_DE']['Newsletter.Campaign']['NewsletterCategory']="Gehört zu diesem Newsletter";
$lang['de_DE']['Newsletter']['Unsubscribe']= "Newsletter abbestellen";
$lang['de_DE']['Newsletter']['ThanksForSignup']="Vielen Dank für Ihre Anmeldung";
?>