<?php

namespace PlasticStudio\SilverstripePopups\GridField;

use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_ActionMenuItem;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\AbstractGridFieldComponent;

class GridFieldCloneAction extends AbstractGridFieldComponent implements
    GridField_ActionProvider,
    GridField_ActionMenuItem
{
    public function getTitle($gridField, $record, $columnName)
    {
        return 'Clone';
    }

    public function getExtraData($gridField, $record, $columnName)
    {
        $field = $this->getCustomAction($gridField, $record);
        if ($field) {
            return array_merge($field->getAttributes(), [
                'classNames' => 'font-icon-page-multiple action-detail',
            ]);
        }
        return [];
    }

    public function getGroup($gridField, $record, $columnName)
    {
        return GridField_ActionMenuItem::DEFAULT_GROUP;
    }

    private function getCustomAction($gridField, $record)
    {
        if (!$record->hasMethod('canEdit') || !$record->canEdit()) {
            return;
        }

        return GridField_FormAction::create(
            $gridField,
            'Clone' . $record->ID,
            'Clone',
            'doclone',
            ['RecordID' => $record->ID]
        )->addExtraClass(
            'action-menu--handled btn btn-outline-dark'
        );
    }

    public function getActions($gridField)
    {
        return ['doclone'];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName === 'doclone') {

            $item = $gridField->getList()->byID($arguments['RecordID']);

            // Check if the item exists
            if ($item) {
                $item = DataObject::get_by_id($item->ClassName, $arguments['RecordID']);
                $clone = $item->duplicate();
                if ($clone->hasField('Label')) {
                    $clone->Label = $clone->Label . ' (copy)';
                } else {
                    $clone->Title = $clone->Title . ' (copy)';
                }
                $clone->write();

                // Add the cloned item to the gridfield
                $gridField->getList()->add($clone);

                // Optionally, you can redirect to the detail view of the cloned record
                $redirectURL = $gridField->Link('item/' . $clone->ID);
                return Controller::curr()->redirect($redirectURL);
            } else {
                // Handle the case where the item is not found
                $response = Controller::curr()->getResponse();
                $response->setStatusCode(
                    404,
                    "Record not found."
                );

                return $response;
            }
        }

        // Return null if no action is handled
        return null;
    }
}
