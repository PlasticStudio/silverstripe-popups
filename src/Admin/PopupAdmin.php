<?php

namespace PlasticStudio\SilverstripePopups\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridField;
use PlasticStudio\SilverstripePopups\DataObjects\Popup;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class PopupAdmin extends ModelAdmin
{
    private static $managed_models = [
        Popup::class
    ];

    private static $url_segment = 'popups';

    private static $menu_title = 'Popups';

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        // Popups
        if($this->modelClass == 'Popup' && $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass))) {
            if($gridField instanceof GridField) {
                $gridField->getConfig()->addComponent(new GridFieldSortableRows('PopupSortOrder'));
            }
        }

        return $form;
    }
}