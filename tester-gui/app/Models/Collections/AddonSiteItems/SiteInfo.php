<?php

namespace App\Models\Collections\AddonSiteItems;

use App\Models\Collections\ScriptsInfoCollection;
use App\Models\Collections\CollectionFactory;

class SiteInfo
{
    /** @var integer */
    private $siteId;

    /** @var integer|null */
    private $contentScriptsCount;

    /** @var integer|null */
    private $contentScriptsCountWithSigns;

    /** @var ScriptsInfoCollection|null */
    private $scriptsInfoCollection;

    /**
     * @param int $siteId
     * @param int|null $contentScriptsCount
     * @param int|null $contentScriptsCountWithSigns
     * @param ScriptsInfoCollection|null $scriptsInfoCollection
     */
    public function __construct(
        int $siteId,
        ?int $contentScriptsCount,
        ?int $contentScriptsCountWithSigns,
        ?ScriptsInfoCollection $scriptsInfoCollection
    )
    {
        $this->siteId = $siteId;
        $this->contentScriptsCount = $contentScriptsCount;
        $this->contentScriptsCountWithSigns = $contentScriptsCountWithSigns;
        $this->scriptsInfoCollection = $scriptsInfoCollection;
    }

    /**
     * @param array|null $data
     * @param int $siteId
     * @return SiteInfo
     */
    public static function createFromArray(?array $data, int $siteId): self
    {
        return new self(
            $siteId,
            $data['content_scripts_count'] ?? null,
            $data['content_scripts_count_with_signs'] ?? null,
            isset($data['scripts_info'])
                ? CollectionFactory::createScriptsInfoCollection($data['scripts_info'])
                : null
        );
    }
}
