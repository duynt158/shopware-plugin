<?php declare(strict_types=1);

namespace EECom\EEComBlog;

use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use EECom\EEComBlog\Core\Content\EEComBlogCategory\EEComBlogCategoryDefinition;
use EECom\EEComBlog\Util\Update;
use EECom\EEComBlog\Util\Lifecycle;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Context;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class EEComBlog extends Plugin
{

    const TEMPLATE_EECOM_BLOG_CATEGORY_UUID = 'a93289db780c41e3835976b2347183fb';
    const TEMPLATE_EECOM_BLOG_UUID = 'a93289db780c41e3835976b2347183db';

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $this->saveUrlTemplate($installContext->getContext());

        /** @var SystemConfigService $systemConfig */
        $systemConfig = $this->container->get(SystemConfigService::class);

        /** @var EntityRepositoryInterface $cmsPageRepository */
        $cmsPageRepository = $this->container->get('cms_page.repository');
        (new Lifecycle(
            $systemConfig,
            $cmsPageRepository,
          ))->install($installContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->saveUrlTemplate($activateContext->getContext());
        parent::activate($activateContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);
        /** @var SystemConfigService $systemConfig */
        $systemConfig = $this->container->get(SystemConfigService::class);

        /** @var EntityRepositoryInterface $cmsPageRepository */
        $cmsPageRepository = $this->container->get('cms_page.repository');

        (new Update(
            $systemConfig,
            $cmsPageRepository
        ))->update($updateContext);




    }
    private function saveUrlTemplate(Context $context): void
    {
        $repository = $this->container->get('seo_url_template.repository');

        $data =
        [
            [
            'id' => self::TEMPLATE_EECOM_BLOG_CATEGORY_UUID,
            'routeName' => 'frontend.eecom.blog.category.detail.page',
            'entityName' => EEComBlogCategoryDefinition::ENTITY_NAME,
            'template' => 'eecom-blog-category/{{ blogCategory.name }}',
            'isValid' => true,

            ],
            [
                'id' => self::TEMPLATE_EECOM_BLOG_UUID,
                'routeName' => 'frontend.eecom.blog.detail.page',
                'entityName' => EEComBlogDefinition::ENTITY_NAME,
                'template' => 'eecom-blog/{{ blog.name }}',
                'isValid' => true,
            ]
        ];



        $repository->upsert($data, $context);
    }
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        if ($uninstallContext->keepUserData()) {
            return;
        }
        $connection = $this->container->get(Connection::class);
        $this->deleteSeoUrlTemplate($uninstallContext->getContext());

        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_translation`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_category_translation`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_category_mapping`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_category`');

        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_search_keyword`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_tag`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_visibility`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_draft`');
        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog_activity`');

        $connection->executeUpdate('DROP TABLE IF EXISTS `eecom_blog`');




    }
    private function deleteSeoUrlTemplate(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsAnyFilter('entityName', ['eecom_blog','eecom_blog_category'])
        );

        /** @var EntityRepositoryInterface $seoUrlTemplateRepository */
        $seoUrlTemplateRepository = $this->container->get('seo_url_template.repository');

        $seoUrlTemplateRepository->search($criteria, $context);

        $seoUrlTemplateIds = $seoUrlTemplateRepository->searchIds($criteria, $context)->getIds();

        if (!empty($seoUrlTemplateIds)) {
            $ids = array_map(static function ($id) {
                return ['id' => $id];
            }, $seoUrlTemplateIds);
            $seoUrlTemplateRepository->delete($ids, $context);
        }
    }

}
