<?php

namespace PlasticStudio\SilverstripePopups\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class PageControllerExtension extends Extension
{
    public function onAfterInit()
    {
        Requirements::css('plasticstudio/silverstripe-popups:client/dist/styles/bundle.css');
        Requirements::javascript('plasticstudio/silverstripe-popups:client/dist/js/bundle.js');
    }
}

