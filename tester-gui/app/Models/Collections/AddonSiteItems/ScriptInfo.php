<?php

namespace App\Models\Collections\AddonSiteItems;

use App\Models\Collections\SignsCollection;
use App\Models\Collections\CollectionFactory;

class ScriptInfo
{
    /** @var string */
    private $scriptPath;

    /** @var string|null */
    private $runAt;

    /** @var SignsCollection|null $sign */
    private $signsCollection;

    /**
     * @param  string  $scriptPath
     * @param  string|null  $runAt
     * @param  SignsCollection|null  $signsCollection
     */
    public function __construct(string $scriptPath, ?string $runAt, ?SignsCollection $signsCollection)
    {
        $this->scriptPath = $scriptPath;
        $this->runAt = $runAt;
        $this->signsCollection = $signsCollection;
    }

    /**
     * @param array|null $scriptInfo
     * @param string $scriptPath
     * @return ScriptInfo
     * @throws \App\Models\Collections\Exceptions\CollectionCreatingException
     */
    public static function createFromArray(?array $scriptInfo, string $scriptPath)
    {
        return new self(
            $scriptPath,
            $scriptInfo['run_at'],
            $scriptInfo !== null
                ? CollectionFactory::createSignsCollection($scriptInfo['found_script_injection_signs'])
                : null
        );
    }
}
