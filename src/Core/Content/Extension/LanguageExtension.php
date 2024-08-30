<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\Extension;

use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogSearchKeyword\EEComBlogSearchKeywordDefinition;
use EECom\EEComBlog\Core\Content\EEComBlog\Aggregate\EEComBlogTranslation\EEComBlogTranslationDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\Aggregate\EEComBlogCategoryTranslation\EEComBlogCategoryTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\System\Language\LanguageDefinition;

class LanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {

        $collection->add(
            (new OneToManyAssociationField(
                'eecomBlogTranslation',
                EEComBlogTranslationDefinition::class,
                'language_id',
                'id'
            ))->addFlags(new CascadeDelete(), new Extension())
        );

        $collection->add(
            (new OneToManyAssociationField(
                'eecomBlogSearchKeywordTranslation',
                EEComBlogSearchKeywordDefinition::class,
                'language_id',
                'id'
            ))->addFlags(new CascadeDelete(), new Extension())
        );

        $collection->add(
            (new OneToManyAssociationField(
                'eecomBlogCategoryTranslation',
                EEComBlogCategoryTranslationDefinition::class,
                'language_id',
                'id'
            ))->addFlags(new CascadeDelete(), new Extension())
        );



    }

    public function getDefinitionClass(): string
    {
        return LanguageDefinition::class;
    }
}
