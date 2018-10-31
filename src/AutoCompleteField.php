<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use TractorCow\AutoComplete\AutoCompleteField as SSAutoCompleteField;
use SilverStripe\View\Requirements;

/**
 * Custom version of autocomplete field with less requirements calls
 * (that can be used on ModedlAdmin without causing conflicts).
 */
class AutoCompleteField extends SSAutoCompleteField
{

    /**
     * @param array $properties
     *
     * @return string
     */
    public function Field($properties = [])
    {
        $field = parent::Field($properties);

        // Block requirements that cause issues
        Requirements::block('silverstripe/admin:thirdparty/jquery/jquery.js');
        Requirements::block('silverstripe/admin:thirdparty/jquery-ui/jquery-ui.js');
        Requirements::block('silverstripe/admin:thirdparty/jquery-entwine/dist/jquery.entwine-dist.js');

        return $field;
    }
}
