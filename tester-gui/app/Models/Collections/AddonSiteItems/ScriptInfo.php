<?php

namespace App\Models\Collections\Items;

class ScriptInfo
{
    /** @var string */
    private $scriptPath;

    /** @var Sign[] $sign */
    private $sign;

    /**
     * @param string $scriptPath
     * @param array $sign
     */
    public function __construct(string $scriptPath, array $sign)
    {

    }

    public static function createFromArray(array $data)
    {
        return new self();
    }
}
