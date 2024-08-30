<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogSearchKeyword;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use Shopware\Core\System\Language\LanguageEntity;

class EEComBlogSearchKeywordEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected string $languageId;

    /**
     * @var string
     */
    protected string $eecomBlogId;

    /**
     * @var string
     */
    protected string $keyword;

    /**
     * @var float
     */
    protected float $ranking;

    /**
     * @var EEComBlogEntity|null
     */
    protected ?EEComBlogEntity $eEComBlog;

    /**
     * @var LanguageEntity|null
     */
    protected ?LanguageEntity $language;

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

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getEecomBlogId(): string
    {
        return $this->eecomBlogId;
    }

    public function setEecomBlogId(string $eecomBlogId): void
    {
        $this->eecomBlogId = $eecomBlogId;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getRanking(): float
    {
        return $this->ranking;
    }

    public function setRanking(float $ranking): void
    {
        $this->ranking = $ranking;
    }

    public function getEeComBlog(): ?EEComBlogEntity
    {
        return $this->eEComBlog;
    }

    public function setEeComBlog(?EEComBlogEntity $eEComBlog): void
    {
        $this->eEComBlog = $eEComBlog;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
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
