<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\SalesChannel\Listing;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class EEComBlogListingResult extends EntitySearchResult
{
    protected ?string $sorting = null;

    protected array $currentFilters = [];


    protected ?string $streamId = null;

    public function addCurrentFilter(string $key, $value): void
    {
        $this->currentFilters[$key] = $value;
    }


    public function getSorting(): ?string
    {
        return $this->sorting;
    }

    public function setSorting(?string $sorting): void
    {
        $this->sorting = $sorting;
    }

    public function getCurrentFilters(): array
    {
        return $this->currentFilters;
    }

    public function getCurrentFilter(string $key)
    {
        return $this->currentFilters[$key] ?? null;
    }

    public function getApiAlias(): string
    {
        return 'eecom_blog_listing';
    }

    public function setStreamId(?string $streamId): void
    {
        $this->streamId = $streamId;
    }

    public function getStreamId(): ?string
    {
        return $this->streamId;
    }
}
