<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldFilterHeader as SSGridFieldFilterHeader;
use TractorCow\AutoComplete\AutoCompleteField;

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

    public function getSearchForm(GridField $gridField)
    {
        $form = parent::getSearchForm($gridField);

        foreach ($form->Fields() as $field) {
            if (is_a($field, AutoCompleteField::class)) {
                $url = Controller::join_links(
                    $gridField->getForm()->getController()->Link(),
                    ModelAdminPlus::ACTION_SUGGEST,
                    '?n=' . $field->getName()
                );

                $field->setSuggestURL($url);
            }
        }

        return $form;
    }
}
