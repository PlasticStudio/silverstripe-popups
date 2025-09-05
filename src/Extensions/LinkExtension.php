<?php

namespace PlasticStudio\SilverstripePopups\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;

class LinkExtension extends Extension
{
    private static $db = [
        'ExtraClasses' => 'Varchar(255)',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create('ExtraClasses', 'Extra CSS Classes')
                ->setDescription('Add any additional CSS classes to the link for tracking or styling purposes.')
        );
    }
}