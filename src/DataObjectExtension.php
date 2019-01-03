<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\ORM\DataExtension;

class DataObjectExtension extends DataExtension
{
    /**
     * Get a custom search context for model admin plus
     */
    public function getModelAdminSearchContext()
    {
        return SearchContext::create(
            get_class($this->getOwner()),
            $this->getOwner()->scaffoldSearchFields(),
            $this->getOwner()->defaultSearchFilters()
        );
    }
}
