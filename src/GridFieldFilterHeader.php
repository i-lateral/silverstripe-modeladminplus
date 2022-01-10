<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\ORM\SS_List;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use TractorCow\AutoComplete\AutoCompleteField;
use ilateral\SilverStripe\ModelAdminPlus\SearchContext;
use SilverStripe\ORM\Search\SearchContext as SSSearchContext;
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

    /**
     * Overwrite the default data manipulation and load
     * in default filters if there any available
     * 
     * @inheritDoc
     */
    public function getManipulatedData(GridField $gridField, SS_List $dataList)
    {
        if (!$this->checkDataType($dataList)) {
            return $dataList;
        }

        /** @var SearchContext */
        $context = $this->getSearchContext($gridField);

        /** @var Filterable $dataList */
        /** @var GridState_Data $columns */
        $columns = $gridField->State->GridFieldFilterHeader->Columns(null);

        if (empty($columns)) {
            $filterArguments = $context->getDefaultFilter();
        } else {
            $filterArguments = $columns->toArray();
        }

        if (count($filterArguments) === 0) {
            return $dataList;
        }

        $dataListClone = clone($dataList);
        $results = $context->getQuery(
            $filterArguments,
            false,
            false,
            $dataListClone
        );

        return $results;
    }

    public function setSearchContext(SSSearchContext $context)
    {
        $this->searchContext = $context;

        return $this;
    }
}
