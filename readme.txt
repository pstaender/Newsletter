=======================================
Newsletter module for SilverStripe 2.4+
=======================================
v0.4

Requirements

You need a SilverStripe-Version greater than 2.4 - maybe it will run on 2.3.x, but I've not tested it on that.
Installation

Copy the module as module-folder named "newsletter" in the SilverStripe-root-directory and run a dev/build/?flush ...
A NewsletterHolderPage will be generated on default as draft in your SiteTree (URL "newsletter").
Configuration

In _config.php some Configurationsettings are made, but can be replaces / overwritten with your own preferences:
NewsletterHolder::$newsletterEmail = $emailAddress

Default sender / return address, if none is specified in the HolderPage oder Campaign
NewsletterHolder::$emailBodyTemplate = $nameOfTheTemplate

Normaly, SilverStripe uses an '<html><head>...</head><body>$Body</body></html>'-Template for every HTML-Email, wich is not usefull for sending user defined HTML-Newsletters, so its replaced by an empty .ss-template-file by default, but can be changed
NewsletterHolder::$newsletterTemplate = $nameOfTheTemplate

Default NewsletterCampaign template if none is specified in the CampaignPage - alway without .ss
(e.g. NewsletterCampaignTemplate.ss => NewsletterCampaignTemplate)
NewsletterHolder::$signupRequiredFields = array()

Set required field for the signup-form
NewsletterCampaign::$makeRelativeToAbsoluteURLS = true

Normally SilverStripe works with relative links according to the <base>-tag, wich is best practice.
But for newsletter its better to work with absolute links, so every <img /> and <a></a> link will be replaced by an absolute link, if a relative is used.
NewsletterHolder::$sendingsPerClick = 10

Set a number for sending per click. Use 10 or less if you're woking on a slow webserver.
How does it work

There is one NewsletterHolderPage in the SiteTree, wich is for organizing and managing all NewsletterCampaigns. The URL of the NewsletterHolderPage is important for managing, signup, unsubscribe newsletter... so keep the URL simple and if you use nested-URLs, keep at teh bottom of the tree (e.g. "yourpage.com/newsletter")
Create as much NewsletterCampaigns as you wish, inside the NewsletterHolder. Each campaign include specific information for the NewsletterCampaign: content, name of the template file, look for links, tables, cells ...
NewsletterCategory, can be managed trough die Admin-CMS -> "newsletter" ('admin/newslettermodule'). Create a category for your newsletter (e.g. "TShirtShopNewsletter"). Each campaign should be related with a NewsletterCategory for managing subscribers.
Models, Pages and what they do
NewsletterHolderPage (SiteTree)

Page in a Sitetree for managing and holding all NewsletterCampaigns
NewsletterCampaign (SiteTree)

NewslletterCampaign contains all data for a newsletter campaign, uncluding the reciever list
NewsletterCategory (DataObject)

NewsletterCategory can be assigned to NewsletterCampaigns and subscribers (NewsletterMember)
NewsletterMember (DataObject)

A subscriber for a NewsletterCategory (doesn't extend from Member, but could be if you want to)
NewsletterReciever (DataObject)

Every NewsletterCampaign can have Recievers, wich are not obligatory subscribers... these are just all people wich will recieve this  newsletter(campaign). You have first to "import" the subscribers to the current NewsletterCampaign-Reciever-list. Every reciever has a flag (sended, not senden, not sended because on blacklist),
NewsletterBlacklist (DataObject)

If someone unscubscribes a newsletter (NewsletterCategory, to be exact), he will be add to the blacklist. So he will recieve no further mails/newsletter under any circumstances (except he will signup again with his email through the signup form).

The subscribers (NewsletterMember), recievers (NewsletterReciever), categories (NewsletterCategory) and blacklist (NewsletterBlacklist) can be manged in 'admin/newslettermodule'. Recievers can also be managed trough NewsletterCampaign in the SiteTree.

Managing + adding subscribers for a campaign

For subscribers, you have to create a NewsletterCategory - because every subscriber (NewsletterMember) are realated to a NewsletterCategory.
The signup + unsubscribe pages are in the NewsletterHolderPage.

Here 'yourpage.com/newsletter/' is the full path to your NewsletterHolderPage:

yourpage.com/newsletter/signup
yourpage.com/newsletter/unsubscribe/$EmailOfTheReciever/$NewsletterCategoryID/

If you have created a campaign, call the "Frontend"-Admin-Interface (must be logged in with permission EDIT_NEWSLETTER and, if necessary SEND_NEWSLETTER --> admin/security --> permissions).
You'll see a list with all newsletter campaigns. Select the one you want to manage.
Import subscribers to reciever list

Use the button Import Subscribers... all subscribers will be copied to the reciever list.

Import any othe recievers in a batch

Use the textarea-form to import a batch-list of recievers. It's orientated to the csv-format, wich means:

    * first line can be used for defining the columns (if not, every line can only be an email-address)
    * sperate columns with ; or ,
    * the following columns can be defined: gender;firstname;surname;email -> shortversions sex;first;sur;mail are accepted as well
    * all lines must follow this definition
    * if not: only a column, wich will be detected as a valid email will be applied
    * e.g.
      gender;first;sur;email
      m;Steve;Jobs;steve@apple.com
      f;Natalie;Portman;natalie.portman@hotmail.com

After you got your reciever list completed, you can start sending your newsletter. Click on the red labeled SEND NEWSLETTER button and be patient :)
Userdefines template and test-sending

By default it will use NewsletterTemplate.ss, wich is inside the module/template/ - folder ... this is just a skeleton template... make your own an create a template folder for newsletter with your theme (e.g. with using theme "BlackCandy": themes/blackcandy_newsletter/template/MyNewsletterTemplate.ss ). Make sure, that you set (in this case MyNewsletterTemplate) as template name in your newsletter campaign page!

If everythings fine, you can send some tests to any email-addres you wish... just add ?send_to=email.to@send.to to your newsletter campaign page in the frontend ... (e.g. http://server/newsletter/newsletter-campaign-in-spring/?send_to=email@anyone.com ... quite simple.
Security

Every mail will be sended only once - if it's sended, it will be marked with a flag. If you want to send it again to a reciever / many recievers, you have to delete them from the reciever list. If someone unsubscribed a newsletter, they will be added to the blacklist... if they are on the blacklist, they will recieve no newsletter, even if they are on the reciever list. They will be deleted from the blacklist, if they subscribe to a newsletter again (or if you delete them from the blacklist manually -> but this is deprecated!)
Improvements and future versions

I wrote this modul just for myself getting some newsletter jobs for clients done - that's why I just made this temporarly newsletter-admin-frontend-interface... it will take me some times to figure out, how to integerate all the features adequately. The next version, v0.5, will have to following new / different features than v0.4x:

    * newsletter-campaign management (adding recievers, import subscribers + sending newsletters) will be available in the SilverStripe backend
    * newslettercategory will be extended from SiteTree, not from a DataObject... that means, newsletter categories will be subpages of NewsletterHolderPage:
      NewsletterHolderPage
      +-NewsletterCategoryPage#1
      |  +-NewsletterCampaign#1
      |  +-NewsletterCampaing#2
      |
      +-NewsletterCategory#2
      ....
    * there will be a fu function for migrating NewsletterCategory into the SiteTree
    * look for a method, to check that email is sended ... doesn't exists, yet... maybe some limitations in php?!

Thats all for now... I hope the module can help you. Feel free to extend / redistribute it :)

Philipp, Sept. 2010
philipp.staender@gmail.com
