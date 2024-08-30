<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\Extension;

use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTag\EEComBlogTagDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\System\Tag\TagDefinition;

class TagExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {

        $collection->add(
            (new ManyToManyAssociationField('blogs', EEComBlogDefinition::class, EEComBlogTagDefinition::class, 'tag_id', 'eecom_blog_id'))->addFlags(new CascadeDelete())
        );



    }

    public function getDefinitionClass(): string
    {
        return TagDefinition::class;
    }
}
