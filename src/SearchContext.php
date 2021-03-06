<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\Forms\TextField;
use SilverStripe\Core\Config\Config;
use ilateral\SilverStripe\ModelAdminPlus\AutoCompleteField;
use SilverStripe\ORM\Search\SearchContext as SSSearchContext;
use SilverStripe\Core\ClassInfo;

class SearchContext extends SSSearchContext
{
    /**
     * Use autocomplete fields instead of text fields
     * 
     * @var boolean
     */
    protected $convert_to_autocomplete = true;

    /**
     * Extend default search fields and add extra functionality.
     * 
     * @return \SilverStripe\Forms\FieldList
     */
    public function getSearchFields()
    {
        $fields = parent::getSearchFields();
        $class = $this->modelClass;
        $use_autocomplete = $this->getConvertToAutocomplete();

        $db = Config::inst()->get($class, "db");
        $has_one = Config::inst()->get($class, "has_one");
        $has_many = Config::inst()->get($class, "has_many");
        $many_many = Config::inst()->get($class, "many_many");
        $belongs_many_many = Config::inst()->get($class, "belongs_many_many");
        $associations = array_merge(
            $has_one,
            $has_many,
            $many_many,
            $belongs_many_many
        );

        // Change currently scaffolded query fields to use conventional
        // field names
        /*foreach ($fields as $field) {
            $field_class = $this->modelClass;
            $name = $field->getName();
            $title = $field->Title();
            $db_field = $name;
            $in_db = false;

            // Find any text fields an replace with autocomplete fields
            if (ClassInfo::class_name($field) == TextField::class && $use_autocomplete) {
                // If this is a relation, switch class name
                if (strpos($name, "__")) {
                    $parts = explode("__", $db_field);
                    $field_class = isset($associations[$parts[0]]) ? $associations[$parts[0]] : null;
                    $db_field = $parts[1];
                    $in_db = ($field_class) ? true : false;
                }

                // If this is in the DB (not casted)
                if (in_array($db_field, array_keys($db))) {
                    $in_db = true;
                }

                if ($in_db) {
                    $fields->replaceField(
                        $name,
                        $field = AutoCompleteField::create(
                            $name,
                            $title,
                            $field->Value(),
                            $field_class,
                            $db_field
                        )->setDisplayField($db_field)
                        ->setLabelField($db_field)
                        ->setStoredField($db_field)
                    );
                }
            }

            $field->setName($name);
        }*/

        return $fields;
    }

    /**
     * Get use autocomplete fields instead of text fields
     *
     * @return  boolean
     */ 
    public function getConvertToAutocomplete()
    {
        return $this->convert_to_autocomplete;
    }

    /**
     * Set use autocomplete fields instead of text fields
     *
     * @param boolean $convert Convert?
     *
     * @return self
     */ 
    public function setConvertToAutocomplete(boolean $convert)
    {
        $this->convert_to_autocomplete = $convert;
        return $this;
    }
}
