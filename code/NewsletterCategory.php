<?php

class NewsletterCategory extends SiteTree {
	
	static $db = array(
		);
		
	static $has_many = array(
		"Subscribers"=>"NewsletterMember"
		);
		
	static $allowed_children = array(
		"NewsletterCampaign",
		);
		
	static $icon = 'newsletter/images/icons/NewsletterCategory';
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		//Subscribers
		$tablefield = new ComplexTableField(
			$controller = $this,
			$name = 'Subscribers',
			'NewsletterMember',
			$fieldList = array(
				'Email'=>_t("Newsletter.Member.Email","Email"),
				'Gender'=>_t("Newsletter.Member.Gender","Gender"),
				'FirstName'=>_t("Newsletter.Member.FirstName","Firstname"),
				'Surname'=>_t("Newsletter.Member.Surname","Surname"),
			),
			null,
			$sourceFilter = "NewsletterCategoryID = $this->ID"
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
		
		//Blacklist
		$tablefield = new ComplexTableField(
			$controller = $this,
			$name = 'Blacklist',
			'NewsletterBlacklist',
			$fieldList = array(
				'Email'=>_t("Newsletter.Member.Email","Email"),
				'Gender'=>_t("Newsletter.Member.Gender","Gender"),
				'FirstName'=>_t("Newsletter.Member.FirstName","Firstname"),
				'Surname'=>_t("Newsletter.Member.Surname","Surname"),
			),
			null,
			$sourceFilter = "NewsletterCategoryID = $this->ID"
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
		
		return $fields;
	}
	
}

class NewsletterCategory_Controller extends Page_Controller {
	// 
	// static $allowed_actions = array(
	// 	"importDBObjectNewsletterCategories"=>"ADMIN"
	// 	);
	// 	
	// function importDBObjectNewsletterCategories() {
	// 	if ($cats = DataObject::get("_obsolete_NewsletterCategory")) {
	// 		echo "Ok";
	// 	}
	// }
	
}

?>