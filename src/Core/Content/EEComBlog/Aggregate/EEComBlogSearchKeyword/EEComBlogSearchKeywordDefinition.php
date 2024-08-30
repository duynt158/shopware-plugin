<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogSearchKeyword;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;

class EEComBlogSearchKeywordDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eecom_blog_search_keyword';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return EEComBlogSearchKeywordCollection::class;
    }

    public function getEntityClass(): string
    {
        return EEComBlogSearchKeywordEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }


    protected function getParentDefinitionClass(): ?string
    {
        return EEComBlogDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('language_id', 'languageId', LanguageDefinition::class))->addFlags(new Required()),
            (new FkField('eecom_blog_id', 'eecomBlogId', EEComBlogDefinition::class))->addFlags(new Required()),
            (new ReferenceVersionField(EEComBlogDefinition::class))->addFlags(new Required()),
            (new StringField('keyword', 'keyword'))->addFlags(new Required()),
            (new FloatField('ranking', 'ranking'))->addFlags(new Required()),
            new ManyToOneAssociationField('eEComBlog', 'eecom_blog_id', EEComBlogDefinition::class, 'id', false),
            new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, 'id', false),
        ]);
    }
}
