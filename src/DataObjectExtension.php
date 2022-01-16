<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\ORM\DataExtension;

class DataObjectExtension extends DataExtension
{
    public function getDefaultSearchFilter(): array
    {
        return (array)$this->getOwner()->config()->get("default_search_filter");
    }

    public function getModelAdminSearchContext()
    {
        return SearchContext::create(
            get_class($this->getOwner()),
            $this->getOwner()->scaffoldSearchFields(),
            $this->getOwner()->defaultSearchFilters()
        );
    }
}
