<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;

class EEComBlogVisibilityEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected string $eecomBlogId;

    /**
     * @var string
     */
    protected string $salesChannelId;

    /**
     * @var int
     */
    protected int $visibility;

    /**
     * @var SalesChannelEntity|null
     */
    protected ?SalesChannelEntity $salesChannel;

    /**
     * @var EEComBlogEntity|null
     */
    protected ?EEComBlogEntity $eEComBlog;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getEecomBlogId(): string
    {
        return $this->eecomBlogId;
    }

    public function setEecomBlogId(string $eecomBlogId): void
    {
        $this->eecomBlogId = $eecomBlogId;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getVisibility(): int
    {
        return $this->visibility;
    }

    public function setVisibility(int $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    public function getEEComBlog(): ?EEComBlogEntity
    {
        return $this->eEComBlog;
    }

    public function setEEComBlog(?EEComBlogEntity $eEComBlog): void
    {
        $this->eEComBlog = $eEComBlog;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
