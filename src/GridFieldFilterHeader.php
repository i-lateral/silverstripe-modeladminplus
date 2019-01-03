<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldFilterHeader as SSGridFieldFilterHeader;

/**
 * Custom filter header that calls custom model admin search context
 */
class GridFieldFilterHeader extends SSGridFieldFilterHeader
{
    public function getSearchContext(GridField $gridField)
    {
        if (!$this->searchContext) {
            $this->searchContext = singleton($gridField->getModelClass())->getModelAdminSearchContext();

            if ($this->updateSearchContextCallback) {
                call_user_func($this->updateSearchContextCallback, $this->searchContext);
            }
        }

        return $this->searchContext;
    }
}
