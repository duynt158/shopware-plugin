<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog;

use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogCategoryMapping\EEComBlogCategoryMappingDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogSearchKeyword\EEComBlogSearchKeywordDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTag\EEComBlogTagDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTranslation\EEComBlogTranslationDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility\EEComBlogVisibilityDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogActivityDefinition;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftDefinition;
use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyIdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Tag\TagDefinition;
use Shopware\Core\System\User\UserDefinition;

class EEComBlogDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eecom_blog';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return EEComBlogEntity::class;
    }

    public function getCollectionClass(): string
    {
        return EEComBlogCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'active' => false
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new VersionField())->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new FkField('author_id', 'authorId', UserDefinition::class))->addFlags(new ApiAware()),
            (new DateTimeField('published_at', 'publishedAt'))->addFlags(new ApiAware()),
            (new FkField('teaser_id', 'teaserId', MediaDefinition::class)),
            (new TranslatedField('name'))->addFlags(new ApiAware(), new Inherited(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('description'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaDescription'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('keywords'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaTitle'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('slotConfig'))->addFlags(new Inherited()),
            (new TranslatedField('customSearchKeywords'))->addFlags(new Inherited(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new ManyToManyIdField('tag_ids', 'tagIds', 'tags'))->addFlags(new Inherited()),
            (new FkField('cms_page_id', 'cmsPageId', CmsPageDefinition::class))->addFlags(new ApiAware(), new Inherited()),
            (new ReferenceVersionField(CmsPageDefinition::class))->addFlags(new Inherited(), new Required(), new ApiAware()),
            (new ManyToManyIdField('category_ids', 'categoryIds', 'categories'))->addFlags(new Inherited(), new ApiAware()),

            (new OneToOneAssociationField('user', 'author_id', 'id', UserDefinition::class, false))->addFlags(new ApiAware()),
            (new OneToOneAssociationField('media', 'teaser_id', 'id', MediaDefinition::class, false))->addFlags(new ApiAware()),
            (new ManyToManyAssociationField('tags', TagDefinition::class, EEComBlogTagDefinition::class, 'eecom_blog_id', 'tag_id'))->addFlags(new ApiAware(),new CascadeDelete(), new SearchRanking(SearchRanking::ASSOCIATION_SEARCH_RANKING)),
            (new OneToManyAssociationField('seoUrls', SeoUrlDefinition::class, 'foreign_key'))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('cmsPage', 'cms_page_id', CmsPageDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('searchKeywords', EEComBlogSearchKeywordDefinition::class, 'eecom_blog_id'))->addFlags(new CascadeDelete(false)),
            (new ManyToManyAssociationField('categories', EEComBlogCategoryDefinition::class, EEComBlogCategoryMappingDefinition::class, 'eecom_blog_id', 'category_id'))->addFlags(new ApiAware(), new CascadeDelete()),
            (new OneToManyAssociationField('visibilities', EEComBlogVisibilityDefinition::class, 'eecom_blog_id'))->addFlags(new CascadeDelete(), new Inherited()),
            (new OneToManyAssociationField('drafts', EEComBlogDraftDefinition::class, 'eecom_blog_id'))->addFlags(new CascadeDelete(), new Inherited()),
            (new OneToManyAssociationField('activities', EEComBlogActivityDefinition::class, 'eecom_blog_id'))->addFlags(new CascadeDelete(), new Inherited()),

            (new TranslationsAssociationField(EEComBlogTranslationDefinition::class, 'eecom_blog_id'))->addFlags(new ApiAware(), new Inherited(), new Required()),

        ]);
    }
}
