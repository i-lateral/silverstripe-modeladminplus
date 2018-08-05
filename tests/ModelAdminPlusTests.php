<?php

namespace ilateral\SilverStripe\ModelAdminPlus\Tests;

use SilverStripe\Control\Session;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\Permission;
use ilateral\SilverStripe\ModelAdminPlus\Tests\Contact;
use ilateral\SilverStripe\ModelAdminPlus\ModelAdminPlus;
use ilateral\SilverStripe\ModelAdminPlus\Tests\ContactAdmin;

class ModelAdminPlusTests extends FunctionalTest
{
    protected static $fixture_file = 'ModelAdminPlusTests.yml';

    protected static $extra_dataobjects = [
        Contact::class
    ];

    protected static $extra_controllers = [
        ContactAdmin::class
    ];

    /**
     * The currently instantiated ModeAdmin instance
     *
     * @var ModelAdminPlus
     */
    protected $curr_admin;

    /**
     * Add some extra functionality on construction
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $admin = ContactAdmin::create();
        $request = new HTTPRequest('GET', '/');
        $request->setSession(new Session([]));
        $admin->setRequest($request);
        $admin->doInit();
        $this->curr_admin = $admin;
    }

    public function testExportFields()
    {
        $this->assertEquals(
            $this->curr_admin->getExportFields(),
            Config::inst()->get(Contact::class, ModelAdminPlus::EXPORT_FIELDS)
        );
    }

    public function testGetSearchSessionName()
    {
        $this->assertEquals(
            $this->curr_admin->getSearchSessionName(),
            ModelAdminPlus::class . "." . Contact::class
        );
    }

    public function testSearchSession()
    {
        // First check session is empty
        $this->assetNull(
            $this->curr_admin->getSearchSession()
        );

        // Now set the search session
        $data = ["Name" => "Mark"];
        $this->curr_admin->setSearchSession($data);

        $this->assetEquals(
            $this->curr_admin->getSearchSession(),
            $data
        );

        // Finally check clearing the session works
        $this->curr_admin->clearSearchSession();
        
        $this->assetNull(
            $this->curr_admin->getSearchSession()
        );
    }

    public function testSearchData()
    {
        // First check session is empty
        $this->assetEquals(
            $this->curr_admin->getSearchData(),
            []
        );

        // Now set the search session
        $data = ["Name" => "Mark"];
        $this->curr_admin->setSearchSession($data);

        $this->assetEquals(
            $this->curr_admin->getSearchData(),
            $data
        );
    }
}
