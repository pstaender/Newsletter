<?php

Object::add_extension('CMSMain', 'NewsletterCMSDecorator');


//do some config stuff here, if you like
//otherwise delete / comment the following lines
NewsletterHolder::$newsletterEmail = "admin@127.0.0.1";
//use a custom body template for email, otherwise its used an default Sapphire template
NewsletterHolder::$emailBodyTemplate = "EmailTemplate";
NewsletterHolder::$newsletterTemplate = "NewsletterTemplate";
NewsletterHolder::$signupRequiredFields = array("Email");
//sendings per click, keep it between 5 - 20, if ou have a slow webserver, try below 5
NewsletterHolder::$sendingsPerClick = 10;
//transform pictures + links to absolue urls (better for newsletters)
NewsletterCampaign::$makeRelativeToAbsoluteURLS = true;
?>