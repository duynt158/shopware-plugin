<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\Extension;

use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogVisibility\EEComBlogVisibilityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class SalesChannelExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {

        $collection->add(
            (new OneToManyAssociationField(
                'blogVisibilities',
                EEComBlogVisibilityDefinition::class,
                'sales_channel_id')
            )->addFlags(new CascadeDelete())
        );



    }

    public function getDefinitionClass(): string
    {
        return SalesChannelDefinition::class;
    }
}
