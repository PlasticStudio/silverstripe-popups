<?php

namespace PlasticStudio\SilverstripePopups\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use PlasticStudio\SilverstripePopups\DataObjects\Popup;

class PageExtension extends Extension
{
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
