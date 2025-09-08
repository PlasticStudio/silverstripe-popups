<?php

namespace PlasticStudio\SilverstripePopups\DataObjects;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\Page;
use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Versioned\Versioned;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\LinkField\Form\MultiLinkField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class Popup extends DataObject
{
    private static string $table_name = 'Popup';

    private static $extensions = [
        Versioned::class,
    ];

    private static $db = [
        'Title' => 'Varchar',
        'MinimizedTitle' => 'Varchar',
        'Content' => 'HTMLText',
        'RawEmbed' => 'Text',
        
        'ExtraPopupClasses' => 'Varchar(255)',
        'ExtraMinimizeClasses' => 'Varchar(255)',
        'ExtraCloseClasses' => 'Varchar(255)',
        
        'Mode' => "Enum('modal, strip, edge', 'modal')",
        'ShowAfter' => 'Int',

        'ActiveStart' => 'DBDatetime',
        'ActiveEnd' => 'DBDatetime',
        
        'EnableMinimize' => 'Boolean',
        'CollapseOnMobile' => 'Boolean',

        'AllPages' => 'Boolean',
        'LinkBy' => "Enum('page, pageType', 'page')",
        'PageTypes' => 'Text', // Comma-separated list of page types

        'PopupSortOrder' => 'Int',
    ];

    private static $defaults = [
        'AllPages' => true,
        'CollapseOnMobile' => false,
        'ShowAfter' => 30,
    ];
    
    private static $has_one = [
        'Image' => Image::class,
    ];

    private static $has_many = [
        'Links' => Link::class . '.Owner',
    ];

    private static $many_many = [
        'Pages' => SiteTree::class,
    ];

    private static $owns = [
        'Image',
        'Links',
    ];

    private static array $cascade_deletes = [
        'Links',
    ];

    private static array $cascade_duplicates = [
        'Links',
    ];

    private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Title' => 'Title',
        'Mode' => 'Display Mode',
        'PagesList' => 'Associated Pages',
        'ActiveStart.Nice' => 'Active Start',
        'ActiveEnd.Nice' => 'Active End',
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
            HTMLEditorField::create('Content', 'Content'),
            UploadField::create('Image', 'Image')
                ->setFolderName('PopupImages')->setAllowedFileCategories('image')
                ->setDescription('Optional image to display in the popup.'),
            TextareaField::create('RawEmbed', 'Raw Embed Code')
                ->setDescription('Optional raw embed code (for example: video iframe or newsletter signup). Note that some providers may restrict embedding on different domains.'),
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
            // Use class name as both key and value for proper storage/retrieval
            $filteredPageTypes[$pageType] = $shortName;
        }

        $pageTypesField = ListboxField::create(
            'PageTypes',
            'Show on these page types',
            $filteredPageTypes
        );

        $pageTypesField->displayIf('AllPages')->isNotChecked()->andIf('LinkBy')->isEqualTo('pageType')->end();
        
        $fields->addFieldsToTab('Root.DisplaySettings', [
            NumericField::create('ShowAfter', 'Show after (seconds)')
                ->setDescription('Delay before showing this popup, in seconds. 0 shows immediately.'),

            DropdownField::create(
                'Mode',
                'Display Mode',
                [
                    'modal' => 'Modal (center of screen)',
                    'strip' => 'Strip',
                    'edge' => 'Edge',
                ]
            ),

            CheckboxField::create(
                'EnableMinimize',
                'Enable Minimize'
            )
                ->setDescription('Allow the user to minimize the popup to a small bar. The popup can be restored by clicking on the bar.')
                ->displayIf('Mode')->isEqualTo('strip')->orIf('Mode')->isEqualTo('edge')->end(),

            TextField::create(
                'MinimizedTitle',
                'Minimized title'
            )
                ->setDescription('The title to show when the popup is minimized. Only applies if "Enable Minimize" is checked and the display mode is "Strip" or "Edge".')
                ->displayIf('EnableMinimize')->isChecked()
                ->andIf()
                    ->group()
                        ->orIf('Mode')->isEqualTo('strip')
                        ->orIf('Mode')->isEqualTo('edge')
                    ->end(),

            CheckboxField::create(
                'CollapseOnMobile',
                'Collapse on mobile'
            )
                ->setDescription('Initially show the popup as minimised on mobile. Useful for more than a sentence or two of content.')
                ->displayIf('Mode')->isEqualTo('strip')->orIf('Mode')->isEqualTo('edge')->end(),

            CheckboxField::create(
                'AllPages',
                'All Pages'
            )->setDescription('Show this popup on all pages.'),

            DropdownField::create(
                'LinkBy',
                'Link by',
                [
                    'page' => 'Page',
                    'pageType' => 'Page Type',
                ]
            )->displayIf('AllPages')->isNotChecked()->end(),

            $pagesField,
            $pageTypesField

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
        
        // Check by LinkBy setting
        if ($this->LinkBy === 'pageType') {
            $selectedTypes = $this->PageTypes ? explode(',', $this->PageTypes) : [];
            if (in_array(get_class($page), $selectedTypes)) {
                return true;
            }
        } elseif ($this->LinkBy === 'page') {
            if ($this->Pages()->exists() && $this->Pages()->find('ID', $page->ID)) {
                return true;
            }
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

    /**
     * Get the cookie expiry time in milliseconds from config
     *
     * @return int Cookie expiry time in milliseconds
     */
    public function getCookieExpiryTime()
    {
        return (int) Config::inst()->get(self::class, 'cookie_expiry_time') ?: 2628000000; // Default to 1 month
    }
}
