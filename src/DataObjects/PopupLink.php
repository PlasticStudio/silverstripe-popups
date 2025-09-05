<?php

namespace PlasticStudio\SilverstripePopups\DataObjects;

use SilverStripe\LinkField\Models\Link;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;

class PopupLink extends Link
{
    private static string $table_name = 'PopupLink';

    private static $db = [
        'ExtraClasses' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Popup' => Popup::class,
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', 
            TextField::create('ExtraClasses', 'Extra CSS Classes')
                ->setDescription('Add any additional CSS classes to the link for tracking or styling purposes.')
        );

        return $fields;
    }
}