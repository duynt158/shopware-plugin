<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class EEComBlogVisibilityDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eecom_blog_visibility';

    public const VISIBILITY_LINK = 10;

    public const VISIBILITY_SEARCH = 20;

    public const VISIBILITY_ALL = 30;

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return EEComBlogVisibilityEntity::class;
    }

    public function getCollectionClass(): string
    {
        return EEComBlogVisibilityCollection::class;
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
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new FkField('eecom_blog_id', 'eecomBlogId', EEComBlogDefinition::class))->addFlags(new Required()),
            (new ReferenceVersionField(EEComBlogDefinition::class))->addFlags(new Required()),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),
            (new IntField('visibility', 'visibility'))->addFlags(new Required()),
            new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false),
            new ManyToOneAssociationField('eEComBlog', 'eecom_blog_id', EEComBlogDefinition::class, 'id', false),
        ]);
    }
}
