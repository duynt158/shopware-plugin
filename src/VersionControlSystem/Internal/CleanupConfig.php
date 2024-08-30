<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Internal;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class CleanupConfig
{
    private const CONFIG_KEY_MAX_ENTRIES_PER_PAGE = 'EEComBlogPlugin.config.maxEntriesPerPage';
    private const CONFIG_KEY_MAX_ENTRIES_WITH_DETAILS_PER_PAGE = 'EEComBlogPlugin.config.maxEntriesWithDetailsPerPage';

    public int $maxLogEntriesPerPage;

    public int $maxLogEntriesWithDetailsPerPage;

    public function hasMaxLogEntriesPerPage(): bool
    {
        return $this->maxLogEntriesPerPage > 0;
    }

    public function hasMaxLogEntriesWithDetailsPerPage(): bool
    {
        return $this->maxLogEntriesWithDetailsPerPage > 0;
    }

    public static function fromSystemConfig(SystemConfigService $service): self
    {
        return new self(
            (int)$service->get(self::CONFIG_KEY_MAX_ENTRIES_PER_PAGE),
            (int)$service->get(self::CONFIG_KEY_MAX_ENTRIES_WITH_DETAILS_PER_PAGE)
        );
    }

    private function __construct(int $maxLogEntriesPerPage, int $maxLogEntriesWithDetailsPerPage)
    {
        $this->maxLogEntriesPerPage = $maxLogEntriesPerPage;
        $this->maxLogEntriesWithDetailsPerPage = $maxLogEntriesWithDetailsPerPage;
    }
}
