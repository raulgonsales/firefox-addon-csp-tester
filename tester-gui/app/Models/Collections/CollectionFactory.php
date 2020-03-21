<?php

namespace App\Models\Collections;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use App\Models\Collections\Exceptions\CollectionCreatingException;
use Exception;

class CollectionFactory
{
    public static function createAddonSiteInfoCollection(array $sitesInfo): AddonSiteInfoCollection
    {
        try {
            return AddonSiteInfoCollection::createFromArray($sitesInfo);
        } catch(Exception $e) {
            throw new Exception("Error creating AddonSiteInfoCollection collection \n" . $e->getMessage());
        }
    }

    public static function createScriptsInfoCollection(array $scriptsInfo): ScriptsInfoCollection
    {
        return ScriptsInfoCollection::createFromArray($scriptsInfo);
    }

    public static function createSignsCollection(array $scriptsInfo): SignsCollection
    {
        try {
            return SignsCollection::createFromArray($scriptsInfo);
        } catch (InvalidEnumMemberException $e) {
            throw new CollectionCreatingException("Error while creating collection! \n" . $e->getMessage());
        }
    }
}
