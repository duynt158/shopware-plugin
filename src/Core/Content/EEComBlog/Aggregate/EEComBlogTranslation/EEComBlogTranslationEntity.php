<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Shopware\Core\System\Language\LanguageEntity;

class EEComBlogTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    /**
     * @var string|null
     */
    protected ?string $metaDescription;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string|null
     */
    protected ?string $keywords;

    /**
     * @var string|null
     */
    protected ?string $description;

    /**
     * @var string|null
     */
    protected ?string $metaTitle;

    /**
     * @var array|null
     */
    protected ?array $customSearchKeywords;

    /**
     * @var array|null
     */
    protected $customFields;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $eecomBlogId;

    /**
     * @var string
     */
    protected $languageId;

    /**
     * @var EEComBlogEntity|null
     */
    protected $eecomBlog;

    /**
     * @var LanguageEntity|null
     */
    protected $language;

    /**
     * @var array|null
     */
    protected $slotConfig;


    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getCustomSearchKeywords(): ?array
    {
        return $this->customSearchKeywords;
    }

    public function setCustomSearchKeywords(?array $customSearchKeywords): void
    {
        $this->customSearchKeywords = $customSearchKeywords;
    }

    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    public function setCustomFields(?array $customFields): void
    {
        $this->customFields = $customFields;
    }

    public function getCreatedAt(): ?\DateTimeInterface
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

    public function getEecomBlogId(): string
    {
        return $this->eecomBlogId;
    }

    public function setEecomBlogId(string $eecomBlogId): void
    {
        $this->eecomBlogId = $eecomBlogId;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getEecomBlog(): ?EEComBlogEntity
    {
        return $this->eecomBlog;
    }

    public function setEecomBlog(?EEComBlogEntity $eecomBlog): void
    {
        $this->eecomBlog = $eecomBlog;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }

    /**
     * @return array|null
     */
    public function getSlotConfig(): ?array
    {
        return $this->slotConfig;
    }

    /**
     * @param array|null $slotConfig
     */
    public function setSlotConfig(?array $slotConfig): void
    {
        $this->slotConfig = $slotConfig;
    }
}
