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

$lang['de_DE']['Newsletter.Member']['FirstName'] = "Vorname";
$lang['de_DE']['Newsletter.Member']['Gender'] = "Geschlecht";
$lang['de_DE']['Newsletter.Member']['Surname'] = "Nachname";
$lang['de_DE']['Newsletter.Campaign']['SendTestTo'] = "Testmail versenden";
$lang['de_DE']['Newsletter.Admim']['DoImportDefaults'] = "Importieren";
$lang['de_DE']['Newsletter.Admim']['ImportDefaults'] = "Es wurden insg. %s Adressen importiert.";
?>