<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlogCategory;

use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogCategoryMapping\EEComBlogCategoryMappingDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\Aggregate\EEComBlogCategoryTranslation\EEComBlogCategoryTranslationDefinition;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ReverseInherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;

class EEComBlogCategoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eecom_blog_category';


    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return EEComBlogCategoryCollection::class;
    }

    public function getEntityClass(): string
    {
        return EEComBlogCategoryEntity::class;
    }

    public function getDefaults(): array
    {
        return [
            'active' => true
        ];
    }


    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new IntField('position', 'position'))->addFlags(new ApiAware()),
            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            (new TranslatedField('metaTitle'))->addFlags(new ApiAware()),
            (new TranslatedField('metaDescription'))->addFlags(new ApiAware()),
            (new TranslatedField('keywords'))->addFlags(new ApiAware()),
            (new ManyToManyAssociationField('eEComBlogs', EEComBlogDefinition::class, EEComBlogCategoryMappingDefinition::class, 'category_id', 'eecom_blog_id'))->addFlags(new ApiAware(),new CascadeDelete(), new ReverseInherited('categories')),
            // Reverse Associations not available in store-api
            (new OneToManyAssociationField('seoUrls', SeoUrlDefinition::class, 'foreign_key'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(EEComBlogCategoryTranslationDefinition::class, 'eecom_blog_category_id'))->addFlags(new ApiAware(), new Required()),
        ]);
    }
}
