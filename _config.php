<?php

Object::add_extension('CMSMain', 'NewsletterCMSDecorator');

//some config stuff here
//if you like to customise, set your values in your _config.php - file
NewsletterHolder::$newsletterEmail = "admin@127.0.0.1";
//use a custom body template for email, otherwise its used an default Sapphire template
NewsletterHolder::$emailBodyTemplate = "EmailTemplate";
NewsletterHolder::$newsletterTemplate = "NewsletterTemplate";
NewsletterHolder::$signupRequiredFields = array("Email");
//sendings per click, keep it between 5 - 20, if ou have a slow webserver, try below 5
NewsletterHolder::$sendingsPerClick = 10;
//if you want to have a r"equesting-the-url does unsubscribe the member", set false ... if you want a form, where the user has to confirm by submitting a form, set true 
NewsletterHolder::$unsubscribeForm = true;
//transform pictures + links to absolue urls (better for newsletters)
NewsletterCampaign::$makeRelativeToAbsoluteURLS = true;
//display a nice-colered list in the backend, false could be better for performance issues
NewsletterCampaign::$listAllRecieversInBackend = true;
?>