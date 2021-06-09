<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\Forms\Form;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\View\Requirements;
use Colymba\BulkManager\BulkManager;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\GridField\GridField;
use Colymba\BulkManager\BulkAction\UnlinkHandler;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use ilateral\SilverStripe\ModelAdminPlus\AutoCompleteField;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ModuleManifest;
use Symbiote\GridFieldExtensions\GridFieldConfigurablePaginator;
use SilverStripe\Forms\GridField\GridFieldFilterHeader as SSGridFieldFilterHeader;

/**
 * Custom version of model admin that adds extra features
 * (such as submitting search results via a POST, saving the query
 * as a session and automatic Bulk Editing support)
 *
 * @author ilateral
 * @package ModelAdminPlus
 */
abstract class ModelAdminPlus extends ModelAdmin
{
    const EXPORT_FIELDS = "export_fields";

    const ACTION_SUGGEST = 'suggest';

    /**
     * Automatically convert date fields on gridfields
     * to use `Date.Nice`.
     *
     * @var boolean
     */
    private static $auto_convert_dates = true;

    /**
     * Automatically convert DB text fields to AutoComplete fields
     *
     * @var boolean
     */
    private static $convert_to_autocomplete = true;

    private static $allowed_actions = [
        "SearchForm",
        self::ACTION_SUGGEST
    ];

    /**
     * List of currently registered ModelAdminSnippets, that is represented as
     * a list of classnames.
     *
     * These snippets are then setup when ModelAdminPlus is initilised and
     * rendered into the ModelAdminPlus content template.
     * 
     * This list can also be a divided up by managed classnames, so that snippets will
     * only be loaded when accessing that class, EG:
     * 
     * $registered_snippets = [
     *      MyObject::class => [
     *          MySnippetOne::class,
     *          MySnippetTwo::class
     *      ]
     * ];
     *
     * @var array
     */
    private static $registered_snippets = [];

    /**
     * Setup snippets for current screen
     */
    public function getSnippets()
    {
        $snippets = ArrayList::create();
        $model_class = $this->getModelClass();

        // Setup any model admin plus snippets
        foreach ($this->config()->registered_snippets as $key => $value) {
            if (is_int($key)) {
                $snippet = $this->createSnippetObject($value);
                $snippets->add($snippet);
            }

            if (is_array($value) && $key == $model_class) {
                foreach ($value as $snippet_class) {
                    $snippet = $this->createSnippetObject($snippet_class);
                    $snippets->add($snippet);
                }
            }
        }

        $snippets = $snippets->sort("Order", "DESC");

        $this->extend("updateSnippets", $snippets);

        return $snippets;
    }

    protected function createSnippetObject(string $class): ModelAdminSnippet
    {
        $snippet = new $class('snippets-before');
        return $snippet;
    }

    public function init()
    {
        parent::init();

        /** @var ModuleManifest */
        $modules = new ModuleManifest(BASE_PATH);

        if ($modules->moduleExists('silverstripe/cms')) {
            Requirements::add_i18n_javascript('silverstripe/cms: client/lang', false, true);
        }

        $clear = $this->getRequest()->getVar("clear");

        if (isset($clear) && $clear == 1) {
            $this->clearSearchSession();
            // Remove clear flag
            return $this->redirect(
                $this->Link(
                    $this->sanitiseClassName($this->modelClass)
                )
            );
        }
    }

    /**
     * Get the default export fields for the current model.
     *
     * First this checks if there is an `export_fields` config variable set on
     * the model class, if not, it reverts to the default behaviour.
     *
     * @return array
     */
    public function getExportFields()
    {
        $export_fields = Config::inst()->get(
            $this->modelClass,
            self::EXPORT_FIELDS
        );

        if (isset($export_fields) && is_array($export_fields)) {
            $fields = $export_fields;
        } else {
            $fields = parent::getExportFields();
        }

        $this->extend("updateExportFields", $fields);

        return $fields;
    }

    /**
     * Get the name of the session to be useed by this model admin's search
     * form.
     *
     * @return string
     */
    public function getSearchSessionName()
    {
        $curr = $this->sanitiseClassName(self::class);
        $model = $this->sanitiseClassName($this->modelClass);
        return $curr . "." . $model;
    }

    /**
     * Empty the current search session
     *
     * @return Session
     */
    public function clearSearchSession()
    {
        $session = $this->getRequest()->getSession();
        return $session->clear($this->getSearchSessionName());
    }

    /**
     * Get the current search session
     *
     * @return Session
     */
    public function getSearchSession()
    {
        $session = $this->getRequest()->getSession();
        return $session->get($this->getSearchSessionName());
    }

    /**
     * Set some data to a search session. This needs to be an array of
     * data (like the data submitted by a form).
     *
     * @param array $data An array of data to store in the session
     *
     * @return self
     */
    public function setSearchSession($data)
    {
        $session = $this->getRequest()->getSession();
        return $session->set($this->getSearchSessionName(), $data);
    }

    /**
     * Get the current search results, combined with any saved
     * search results and resturn (as an array).
     *
     * @return array
     */
    public function getSearchData()
    {
        $data = $this->getSearchSession();

        if (!$data || $data && !is_array($data)) {
            $data = [];
        }

        return $data;
    }

    protected function getGridField(): GridField
    {
        $field = parent::getGridField();

        if ($this->config()->auto_convert_dates) {
            GridFieldDateFinder::create($field)->convertDateFields();
        }

        return $field;
    }

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        // Add bulk editing to gridfield
        $manager = new BulkManager();
        $manager->removeBulkAction(UnlinkHandler::class);

        $config
            ->addComponent(new GridFieldSnippetRow(), GridFieldButtonRow::class)
            ->removeComponentsByType(GridFieldPaginator::class)
            ->addComponent($manager)
            ->addComponent(new GridFieldConfigurablePaginator());

        // Switch to custom filter header
        if ($config->getComponentsByType(SSGridFieldFilterHeader::class)->exists()) {
            $config
            ->removeComponentsByType(SSGridFieldFilterHeader::class)
            ->addComponent(new GridFieldFilterHeader(
                false,
                function ($context) {
                    $this->extend('updateSearchContext', $context);
                },
                function ($form) {
                    $this->extend('updateSearchForm', $form);
                }
            ));
        }

        // Add custom snippets
        foreach ($this->getSnippets() as $snippet) {
            $config->addComponent($snippet);
        }

        return $config;
    }

    /**
     * Find and return the recommended suggestion for an autocomplete
     * field
     *
     * @param array $data Submitted form
     * @param Form  $form The current form
     *
     * @return HTTPResponse
     */
    public function suggest(HTTPRequest $request)
    {
        $name = $request->param('n');
        $grid = $this->getGridField();

        // Manually re-assign gridfield to edit form
        $form = $this->getEditForm();
        $grid->setForm($form);

        $config = $grid->getConfig();
        /** @var GridFieldFilterHeader */
        $search = $config->getComponentByType(GridFieldFilterHeader::class);
        $form = isset($search) ? $search->getSearchForm($grid) : null;
        
        /** @var AutoCompleteField */
        $field = isset($form) ? $form->Fields()->fieldByName($name) : null;

        if (isset($field)) {
            return $field->Suggest($request);
        }

        // the response body
        return json_encode([]);
    }
}
