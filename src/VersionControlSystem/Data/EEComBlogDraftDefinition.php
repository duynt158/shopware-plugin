<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Data;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogEntity;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\User\UserDefinition;

class EEComBlogDraftDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eecom_blog_draft';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }


    public function getCollectionClass(): string
    {
        return EEComBlogDraftCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new StringField('draft_version_id', 'draftVersion'))->setFlags(new Required(), new WriteProtected(Context::SYSTEM_SCOPE)),
            (new StringField('deep_link_code', 'deepLinkCode'))->setFlags(new Required(), new WriteProtected(Context::SYSTEM_SCOPE)),
            (new StringField('name', 'name'))->setFlags(new Required()),

            (new FkField('eecom_blog_id', 'eecomBlogId', EEComBlogDefinition::class))->setFlags(new Required(), new WriteProtected(Context::SYSTEM_SCOPE)),
            (new ReferenceVersionField(EEComBlogDefinition::class))->addFlags(new Required(), new WriteProtected(Context::SYSTEM_SCOPE)),

            (new FkField('owner_user_id', 'ownerId', UserDefinition::class))->setFlags(new WriteProtected(Context::SYSTEM_SCOPE)),
            (new FkField('preview_media_id', 'previewMediaId', MediaDefinition::class))->setFlags(new WriteProtected(Context::SYSTEM_SCOPE)),

            new ManyToOneAssociationField('eEComBlog', 'eecom_blog_id', EEComBlogDefinition::class, 'id', false),
            new ManyToOneAssociationField('user', 'owner_user_id', UserDefinition::class, 'id', false),
            new ManyToOneAssociationField('previewMedia', 'preview_media_id', MediaDefinition::class, 'id', false),
        ]);
    }
}
