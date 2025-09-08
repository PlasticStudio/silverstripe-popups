<?php

namespace PlasticStudio\SilverstripePopups\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\Requirements;
use PlasticStudio\SilverstripePopups\DataObjects\Popup;

class PageExtension extends Extension
{
    /**
     * Load CSS and JS assets when popups exist on the page
     */
    public function onAfterInit()
    {
        // Only load assets if there are popups to display
        if ($this->owner->getPopups()->exists()) {
            Requirements::css('plasticstudio/silverstripe-popups:client/dist/styles/bundle.css');
            Requirements::javascript('plasticstudio/silverstripe-popups:client/dist/js/bundle.js');
        }
    }

    /**
     * Get popups that should be shown on the current page
     * Filtered by page eligibility and sorted by PopupSortOrder
     *
     * @return ArrayList
     */
    public function getPopups()
    {
        $popups = Popup::get()->sort('PopupSortOrder ASC');
        $eligiblePopups = ArrayList::create();
        
        foreach ($popups as $popup) {
            if ($popup->shouldShowOnPage($this->owner)) {
                $eligiblePopups->push($popup);
            }
        }
        
        return $eligiblePopups;
    }
}
