<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\ORM\DataExtension;

class DataObjectExtension extends DataExtension
{
<<<<<<< HEAD
=======
    /**
     * Return an array of default filters to load for this object
     *
     * @return array
     */
>>>>>>> 51f34a4d5b526cbf202d8d500c8d89fa5b006c99
    public function getDefaultSearchFilter(): array
    {
        return (array)$this->getOwner()->config()->get("default_search_filter");
    }

<<<<<<< HEAD
=======
    /**
     * Get a custom search context for model admin plus
     */
>>>>>>> 51f34a4d5b526cbf202d8d500c8d89fa5b006c99
    public function getModelAdminSearchContext()
    {
        return SearchContext::create(
            get_class($this->getOwner()),
            $this->getOwner()->scaffoldSearchFields(),
            $this->getOwner()->defaultSearchFilters()
        );
    }
}
