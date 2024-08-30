<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogCategoryMapping;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class EEComBlogCategoryMappingDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'eecom_blog_category_mapping';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }


    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('eecom_blog_id', 'eecomBlogId', EEComBlogDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new ReferenceVersionField(EEComBlogDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('category_id', 'categoryId', EEComBlogCategoryDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('blog', 'eecom_blog_id', EEComBlogDefinition::class, 'id', false),
            new ManyToOneAssociationField('category', 'category_id', EEComBlogCategoryDefinition::class, 'id', false),
        ]);
    }
}
