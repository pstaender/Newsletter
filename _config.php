<?php

Director::addRules(100, array(
	'newsletter-tool/$Action/$ID/$OtherID' => 'NewsletterHolder_Controller',
));

?>