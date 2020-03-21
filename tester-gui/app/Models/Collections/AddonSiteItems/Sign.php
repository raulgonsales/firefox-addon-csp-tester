<?php

namespace App\Models\Collections\AddonSiteItems;

use App\Models\Collections\Enum\SignTypeEnum;

class Sign
{
    /** @var integer */
    private $line;

    /** @var SignTypeEnum */
    private $signType;

    public function __construct(int $line, SignTypeEnum $signType)
    {
        $this->line = $line;
        $this->signType = $signType;
    }

    /**
     * @param array $data
     * @return static
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['line'],
            new SignTypeEnum($data['sign'])
        );
    }
}
