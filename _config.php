<?php
//do some config stuff here, if you like
//otherwise delete / comment the following lines
NewsletterHolder::$newsletterEmail = "admin@127.0.0.1";
//use a custom body template for email, otherwise its used an default Sapphire template
NewsletterHolder::$emailBodyTemplate = "EmailTemplate";
NewsletterHolder::$newsletterTemplate = "NewsletterTemplate";
NewsletterHolder::$signupRequiredFields = array("Email");
//transform pictures + links to absolue urls (better for newsletters)
NewsletterCampaign::$makeRelativeToAbsoluteURLS = true;
?>