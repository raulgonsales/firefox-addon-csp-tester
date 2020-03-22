<?php

namespace App\Models\Collections;

use App\Models\Collections\AddonSiteItems\ScriptInfo;

class ScriptsInfoCollection extends \ArrayObject
{
    public static function createFromArray(array $scriptsInfo): self
    {
        $collection = new self();

        foreach ($scriptsInfo as $scriptsInfoItemPath => $scriptsInfoItem) {
            if (!is_array($scriptsInfoItem)) {
                $scriptsInfoItem = null;
            }

            $scriptInfo = ScriptInfo::createFromArray($scriptsInfoItem, $scriptsInfoItemPath);

            $collection->append($scriptInfo);
        }

        return $collection;
    }
}
