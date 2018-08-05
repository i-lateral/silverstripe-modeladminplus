<?php

namespace ilateral\SilverStripe\ModelAdminPlus\Tests;

use SilverStripe\Dev\TestOnly;
use SilverStripe\Control\Controller;
use ilateral\SilverStripe\ModelAdminPlus\Tests\Contact;
use ilateral\SilverStripe\ModelAdminPlus\ModelAdminPlus;

class ContactAdmin extends ModelAdminPlus implements TestOnly
{
    private static $url_segment = 'contactadmin';

    private static $managed_models = [
        Contact::class,
    ];
}
