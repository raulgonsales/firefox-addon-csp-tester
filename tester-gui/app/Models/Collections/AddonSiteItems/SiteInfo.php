<?php

namespace App\Models\Collections\Items;

use App\Models\Collections\ScriptsInfoCollection;

class SiteInfo
{
    /** @var integer */
    private $contentScriptsCount;

    /** @var integer */
    private $contentScriptsCountWithSigns;

    /** @var ScriptsInfoCollection */
    private $scriptsInfo;

    /**
     * @param int $contentScriptsCount
     * @param int $contentScriptsCountWithSigns
     * @param ScriptsInfoCollection $scriptsInfo
     */
    public function __construct(
        int $contentScriptsCount,
        int $contentScriptsCountWithSigns,
        ScriptsInfoCollection $scriptsInfo
    )
    {
        $this->contentScriptsCount = $contentScriptsCount;
        $this->contentScriptsCountWithSigns = $contentScriptsCountWithSigns;
        $this->scriptsInfo = $scriptsInfo;
    }

    /**
     * @param array $data
     * @return SiteInfo
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['content_scripts_count'],
            $data['content_scripts_count_with_signs'],
            ScriptsInfoCollection::createFromArray($data['scripts_info'])
        );
    }
}
