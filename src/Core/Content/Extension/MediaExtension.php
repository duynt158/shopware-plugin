<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\Extension;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogActivityDefinition;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;

class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {

        $collection->add(
            (new OneToOneAssociationField(
                'blogTeaser',
                'id',
                'teaser_id',
                EEComBlogDefinition::class,
                false
            ))->addFlags(new CascadeDelete())
        );

        $collection->add(
            (new OneToManyAssociationField(
                'blogDraftPreview',
                EEComBlogDraftDefinition::class,
                'preview_media_id',
                'id'
            ))->addFlags());


    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}
