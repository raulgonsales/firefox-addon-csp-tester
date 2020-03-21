<?php

namespace App\Models\Collections;

use App\Models\Collections\AddonSiteItems\Sign;

class SignsCollection extends \ArrayObject
{
    /**
     * @param array $signs
     * @return static
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public static function createFromArray(array $signs): self
    {
        $collection = new self();

        foreach ($signs as $sign) {
            $collection->append(Sign::createFromArray($sign));
        }

        return $collection;
    }
}
