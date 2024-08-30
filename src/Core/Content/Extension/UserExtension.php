<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\Extension;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogActivityDefinition;
use EECom\EEComBlog\VersionControlSystem\Data\EEComBlogDraftDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\System\User\UserDefinition;

class UserExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {

        $collection->add(
            (new OneToOneAssociationField(
                'blogAuthor',
                'id',
                'author_id',
                EEComBlogDefinition::class
            ))->addFlags(new CascadeDelete())
        );

        $collection->add(
            (new OneToManyAssociationField(
                'blogDrafts',
                EEComBlogDraftDefinition::class,
                'owner_user_id',
                'id'
            ))->addFlags());


        $collection->add(
            (new OneToManyAssociationField(
                'blogActivities',
                EEComBlogActivityDefinition::class,
                'user_id',
                'id'
            ))->addFlags());

    }

    public function getDefinitionClass(): string
    {
        return UserDefinition::class;
    }
}
