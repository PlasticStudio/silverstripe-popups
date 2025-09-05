<?php

namespace PlasticStudio\SilverstripePopups\DataObjects;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Versioned\Versioned;
use SilverStripe\LinkField\Form\MultiLinkField;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class Popup extends DataObject
{
    private static string $table_name = 'Popup';

    private static $extensions = [
        Versioned::class,
    ];

    private static $db = [
        'Enabled' => 'Boolean',

        'Title' => 'Varchar',
        'Content' => 'Varchar',

        'CollapseOnMobile' => 'Boolean',

        'AllPages' => 'Boolean',

        'ActiveStart' => 'Datetime',
        'ActiveEnd' => 'Datetime',

        'GAReference' => 'Varchar',
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
        'Links' => Link::class,
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
        'GAReference' => 'GA Reference',
    ];

    private static $default_sort = 'PopupSortOrder';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'PopupSortOrder',
        ]);

        
        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create(
                'Enabled',
                'Popup Enabled',
                [
                    true => 'Enabled',
                    false => 'Disabled',
                ]
            )->setDescription('Manually enable or disable this popup.'),
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
        $pageTypeNames = [];
        foreach ($pageTypes as $pageType) {
            $shortName = ClassInfo::shortName($pageType);
            $pageTypeNames[$pageType] = $shortName;
        }
        unset($pageTypeNames[SiteTree::class]);
        $pageTypesField = ListboxField::create(
            'PageTypes',
            'Show on these page types',
            // $pageTypeNames
            $pageTypes
        );
        $pageTypesField->displayIf('AllPages')->isNotChecked()->andIf('LinkBy')->isEqualTo('pageType')->end();
        
        $fields->addFieldsToTab('Root.Settings', [
            UploadField::create('PopupBackdropImage', 'Backdrop Image')->setDescription('This will be applied to the popup\'s backdrop')
                ->setFolderName('PopupBackdropImages')->setAllowedFileCategories('image')
                ->displayIf('EnableBackdrop')->isChecked()->andIf('BackdropMode')->isEqualTo('image')->end(), // TODO: fix the conditional

            UploadField::create('PopupBackdropVideo', 'Backdrop Video')->setDescription('This will be applied to the popup\'s backdrop')
                ->setFolderName('PopupBackdropVideos')->setAllowedFileCategories('video')
                ->displayIf('EnableBackdrop')->isChecked()->andIf('BackdropMode')->isEqualTo('video')->end(), // TODO: fix the conditional

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
