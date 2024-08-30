<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog;

use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility\EEComBlogVisibilityCollection;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogActivityCollection;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\User\UserEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\System\Tag\TagCollection;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use Shopware\Core\Content\Cms\CmsPageEntity;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogSearchKeyword\EEComBlogSearchKeywordCollection;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryCollection;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTranslation\EEComBlogTranslationCollection;

class EEComBlogEntity extends Entity
{
    use EntityIdTrait;


    /**
     * @var bool|null
     */
    protected $active;

    /**
     * @var string|null
     */
    protected $authorId;

    /**
     * @var \DateTimeInterface|null
     */
    protected $publishedAt;

    /**
     * @var string|null
     */
    protected $teaserId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $metaDescription;

    /**
     * @var string|null
     */
    protected $keywords;

    /**
     * @var string|null
     */
    protected $metaTitle;

    /**
     * @var array|null
     */
    protected $customFields;

    /**
     * @var array|null
     */
    protected $customSearchKeywords;

    /**
     * @var array|null
     */
    protected $tagIds;

    /**
     * @var string|null
     */
    protected $cmsPageId;

    /**
     * @var array|null
     */
    protected $categoryIds;

    /**
     * @var UserEntity|null
     */
    protected $user;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var TagCollection|null
     */
    protected $tags;

    /**
     * @var SeoUrlCollection|null
     */
    protected $seoUrls;

    /**
     * @var CmsPageEntity|null
     */
    protected $cmsPage;

    /**
     * @var EEComBlogSearchKeywordCollection|null
     */
    protected $searchKeywords;

    /**
     * @var EEComBlogCategoryCollection|null
     */
    protected $categories;

    /**
     * @var EEComBlogDraftCollection|null
     */
    protected $drafts;

    /**
     * @var EEComBlogActivityCollection|null
     */
    protected $activities;

    /**
     * @var EEComBlogTranslationCollection
     */
    protected $translations;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var array|null
     */
    protected $translated;

    /**
     * @var EEComBlogVisibilityCollection|null
     */
    protected $visibilities;

    /**
     * @var array|null
     */
    protected $slotConfig;


    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    public function setAuthorId(?string $authorId): void
    {
        $this->authorId = $authorId;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getTeaserId(): string
    {
        return $this->teaserId;
    }

    public function setTeaserId(?string $teaserId): void
    {
        $this->teaserId = $teaserId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    public function setCustomFields(?array $customFields): void
    {
        $this->customFields = $customFields;
    }

    public function getCustomSearchKeywords(): ?array
    {
        return $this->customSearchKeywords;
    }

    public function setCustomSearchKeywords(?array $customSearchKeywords): void
    {
        $this->customSearchKeywords = $customSearchKeywords;
    }

    public function getTagIds(): ?array
    {
        return $this->tagIds;
    }

    public function setTagIds(?array $tagIds): void
    {
        $this->tagIds = $tagIds;
    }

    public function getCmsPageId(): ?string
    {
        return $this->cmsPageId;
    }

    public function setCmsPageId(?string $cmsPageId): void
    {
        $this->cmsPageId = $cmsPageId;
    }

    public function getCategoryIds(): ?array
    {
        return $this->categoryIds;
    }

    public function setCategoryIds(?array $categoryIds): void
    {
        $this->categoryIds = $categoryIds;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getTags(): ?TagCollection
    {
        return $this->tags;
    }

    public function setTags(TagCollection $tags): void
    {
        $this->tags = $tags;
    }

    public function getSeoUrls(): ?SeoUrlCollection
    {
        return $this->seoUrls;
    }

    public function setSeoUrls(SeoUrlCollection $seoUrls): void
    {
        $this->seoUrls = $seoUrls;
    }

    public function getCmsPage(): ?CmsPageEntity
    {
        return $this->cmsPage;
    }

    public function setCmsPage(?CmsPageEntity $cmsPage): void
    {
        $this->cmsPage = $cmsPage;
    }

    public function getSearchKeywords(): ?EEComBlogSearchKeywordCollection
    {
        return $this->searchKeywords;
    }

    public function setSearchKeywords(EEComBlogSearchKeywordCollection $searchKeywords): void
    {
        $this->searchKeywords = $searchKeywords;
    }

    public function getCategories(): ?EEComBlogCategoryCollection
    {
        return $this->categories;
    }

    public function setCategories(EEComBlogCategoryCollection $categories): void
    {
        $this->categories = $categories;
    }

    public function getTranslations(): EEComBlogTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(EEComBlogTranslationCollection $translations): void
    {
        $this->translations = $translations;
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

    /**
     * @return EEComBlogVisibilityCollection|null
     */
    public function getVisibilities(): ?EEComBlogVisibilityCollection
    {
        return $this->visibilities;
    }

    /**
     * @param EEComBlogVisibilityCollection $visibilities
     */
    public function setVisibilities(EEComBlogVisibilityCollection $visibilities): void
    {
        $this->visibilities = $visibilities;
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

    /**
     * @return EEComBlogDraftCollection|null
     */
    public function getDrafts(): ?EEComBlogDraftCollection
    {
        return $this->drafts;
    }

    /**
     * @param EEComBlogDraftCollection|null $drafts
     */
    public function setDrafts(EEComBlogDraftCollection $drafts): void
    {
        $this->drafts = $drafts;
    }

    /**
     * @return EEComBlogActivityCollection|null
     */
    public function getActivities(): ?EEComBlogActivityCollection
    {
        return $this->activities;
    }

    /**
     * @param EEComBlogActivityCollection|null $activities
     */
    public function setActivities(EEComBlogActivityCollection $activities): void
    {
        $this->activities = $activities;
    }


}
