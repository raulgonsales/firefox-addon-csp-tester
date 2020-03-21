<?php

namespace App\Models\Collections;

use App\Models\Collections\AddonSiteItems\SiteInfo;

class AddonSiteInfoCollection extends \ArrayObject
{
    public static function createFromArray(array $addonSiteInfo): self
    {
        $collection = new self();

        foreach ($addonSiteInfo as $siteId => $addonSiteInfoItem) {
            if (!is_array($addonSiteInfoItem)) {
                $addonSiteInfoItem = null;
            }

            $siteInfo = SiteInfo::createFromArray($addonSiteInfoItem, $siteId);

            $collection->append($siteInfo);
        }

        return $collection;
    }
}
