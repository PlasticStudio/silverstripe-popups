<?php

namespace PlasticStudio\SilverstripePopups\DataObjects;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\Page;
use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Versioned\Versioned;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\LinkField\Form\MultiLinkField;

class Popup extends DataObject
{
    private static string $table_name = 'Popup';

    private static $extensions = [
        Versioned::class,
    ];

    private static $db = [
        'Title' => 'Varchar',
        'Content' => 'Varchar',

        'ExtraPopupClasses' => 'Varchar(255)',
        'ExtraMinimizeClasses' => 'Varchar(255)',
        'ExtraCloseClasses' => 'Varchar(255)',

        'AlwaysShow' => 'Boolean',
        'ActiveStart' => 'DBDatetime',
        'ActiveEnd' => 'DBDatetime',

        'CollapseOnMobile' => 'Boolean',

        'AllPages' => 'Boolean',
        'LinkBy' => "Enum('page, pageType', 'page')",

        'PopupSortOrder' => 'Int',
    ];

    private static $defaults = [
        'Enabled' => false,
        'AllPages' => true,
        'CollapseOnMobile' => false,
    ];
    
    private static $has_one = [
        'Image' => Image::class,
    ];

    private static $has_many = [
        'Links' => Link::class
    ];

    private static $many_many = [
        'Pages' => SiteTree::class,
    ];

    private static $owns = [
        'Image',
        'Links',
    ];

    private static $summary_fields = [
        'Enabled.Nice' => 'Enabled',
        'Image.CMSThumbnail' => 'Image',
        'Trigger' => 'Trigger',
    ];

    private static $default_sort = 'PopupSortOrder';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Main',
            'Links',
            'Pages',
            'PopupSortOrder',
        ]);

        $fields->addFieldsToTab('Root.Content', [
            TextField::create('Title', 'Title'),
            TextareaField::create('Content', 'Content'),
            UploadField::create('Image', 'Image')
                ->setFolderName('PopupImages')->setAllowedFileCategories('image'),
                MultiLinkField::create(
                    'Links',
                    'Links',
                    null,
                    null,
                    false,
                    'getCMSFields_forPopup'
                )->setDescription('Add one or more buttons/links to the popup.'),
        ]);

        // Manage associated pages
        $pages = SiteTree::get()->map('ID', 'Title')->toArray();
        $pagesField = ListBoxField::create(
            'Pages',
            'Show on these pages',
            $pages
        );
        $pagesField->displayIf('AllPages')->isNotChecked()->andIf('LinkBy')->isEqualTo('page')->end();

        // Manage associated page types
        $pageTypes = ClassInfo::subclassesFor(SiteTree::class);
        $filteredPageTypes = [];

        $exclusions = [
            'SiteTree',
            'Page',
            'ErrorPage',
            'RedirectorPage',
            'HTMLSitemap'
        ];
 
        foreach ($pageTypes as $pageType) {
            $shortName = ClassInfo::shortName($pageType);
            if (in_array($shortName, $exclusions) || in_array($pageType, $exclusions)) {
                continue;
            }
           $filteredPageTypes[] = $pageType;
        }

        $pageTypesField = ListboxField::create(
            'PageTypes',
            'Show on these page types',
            $filteredPageTypes
        );

        $pageTypesField->displayIf('AllPages')->isNotChecked()->andIf('LinkBy')->isEqualTo('pageType')->end();
        
        $fields->addFieldsToTab('Root.DisplaySettings', [
            CheckboxField::create(
                'AllPages',
                'All Pages'
            )->setDescription('Show this popup on all pages. Will override "Associated Pages" options below if any are set. If you\'re manually triggering the modal, this option gets ignored.'),
            DropdownField::create(
                'LinkBy',
                'Link by',
                [
                    'page' => 'Page',
                    'pageType' => 'Page Type',
                ]
            )->displayIf('AllPages')->isNotChecked()->end(),
            $pagesField,
            $pageTypesField,
            TextField::create(
                'MinimizedTitle',
                'Minimized title'
            )->displayIf('EnableMinimize')->isChecked()->end(), // TODO: fix the conditional

            CheckboxField::create(
                'CollapseOnMobile',
                'Collapse on mobile'
            )->setDescription('If the popup is too large for a mobile screen, it will be shown as minimized. The user can then tap to expand it.'),

        ]);

        $fields->addFieldsToTab('Root.Schedule', [
            // TODO: should we replace with embargo/expiry module?
            DatetimeField::create('ActiveStart', 'Active Start')
                ->setDescription('The date and time when the popup should start being shown. Leave blank to start immediately.'),
            DatetimeField::create('ActiveEnd', 'Active End')
                ->setDescription('The date and time when the popup should stop being shown. Leave blank to show indefinitely.'),
        ]);

        $fields->addFieldsToTab('Root.CustomClasses', [
            LiteralField::create('html', "<p class='message notice'>Add extra classes for tracking or styling purposes.</p>"),
            TextField::create('ExtraPopupClasses', 'Extra CSS Classes')
                ->setDescription('Add any additional CSS classes to the popup wrapper.'),
            TextField::create('ExtraMinimizeClasses', 'Extra Minimize Button Classes')
                ->setDescription('Add any additional CSS classes to the minimize button.'),
            TextField::create('ExtraCloseClasses', 'Extra Close Button Classes')
                ->setDescription('Add any additional CSS classes to the close button.')
        ]);


        return $fields;
    }

    /**
     * Determines whether the popup should be shown on a specific page.
     *
     * @param Page $page The page object to check against.
     * @return bool Returns true if the popup should be shown on the page, false otherwise.
     */
    public function shouldShowOnPage($page)
    {
        // Check the current date against the active start and end dates
        $now = DBDatetime::now()->getValue();

        // Check if ActiveStart is set and the current datetime is after ActiveStart
        if ($this->ActiveStart && $now < $this->dbObject('ActiveStart')->getValue()) {
            return false;
        }

        // Check if ActiveEnd is set and the current datetime is before ActiveEnd
        if ($this->ActiveEnd && $now > $this->dbObject('ActiveEnd')->getValue()) {
            return false;
        }

        // Conditional page logic
        if ($this->AllPages) {
            return true;
        }
        $selectedTypes = $this->PageTypes ? explode(',', $this->PageTypes) : [];
        if (in_array(get_class($page), $selectedTypes)) {
            return true;
        }
        if ($this->Pages()->exists() && $this->Pages()->find('ID', $page->ID)) {
            return true;
        }

        return false;
    }

    public function AlignMinimizedRight()
    {
        $position = $this->Position;
        $customPosition = $this->PositionCustom;
    
        if ($position === 'top-right' || $position === 'center-right' || $position === 'bottom-right') {
            return true;
        }
    
        if ($customPosition !== null && strpos($customPosition, 'right') !== false) {
            return true;
        }
    
        return false;
    }

    /**
     * Returns a comma-separated list of titles from the associated Pages.
     *
     * @return string The comma-separated list of titles.
     */
    public function getPagesList()
    {
        return implode(', ', $this->Pages()->column('Title'));
    }
}
