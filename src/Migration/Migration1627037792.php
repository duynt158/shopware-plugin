<?php declare(strict_types=1);

namespace EECom\EEComBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1627037792 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1627037792;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog` (
                `id` BINARY(16) NOT NULL,
                `version_id` BINARY(16) NOT NULL,
                `active` TINYINT(1) NULL DEFAULT 0,
                `author_id` BINARY(16) NULL,
                `published_at` DATETIME(3) NULL,
                `teaser_id` BINARY(16) NULL,
                `tag_ids` JSON NULL,
                `cms_page_id` BINARY(16) NULL,
                `cms_page_version_id` BINARY(16) NOT NULL,
                `category_ids` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`,`version_id`),
                CONSTRAINT `json.eecom_blog.tag_ids` CHECK (JSON_VALID(`tag_ids`)),
                CONSTRAINT `json.eecom_blog.category_ids` CHECK (JSON_VALID(`category_ids`)),
                KEY `fk.eecom_blog.cms_page_id` (`cms_page_id`,`cms_page_version_id`),
                CONSTRAINT `fk.eecom_blog.cms_page_id` FOREIGN KEY (`cms_page_id`,`cms_page_version_id`) REFERENCES `cms_page` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        ');
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_translation` (
               `meta_description` VARCHAR(255) NULL,
                `name` VARCHAR(255) NOT NULL,
                `keywords` LONGTEXT NULL,
                `description` LONGTEXT NULL,
                `meta_title` VARCHAR(255) NULL,
                `custom_search_keywords` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                `eecom_blog_id` BINARY(16) NOT NULL,
                `eecom_blog_version_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`eecom_blog_id`,`language_id`,`eecom_blog_version_id`),
                CONSTRAINT `json.eecom_blog_translation.custom_search_keywords` CHECK (JSON_VALID(`custom_search_keywords`)),
                CONSTRAINT `json.eecom_blog_translation.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
                KEY `fk.eecom_blog_translation.eecom_blog_id` (`eecom_blog_id`,`eecom_blog_version_id`),
                KEY `fk.eecom_blog_translation.language_id` (`language_id`),
                CONSTRAINT `fk.eecom_blog_translation.eecom_blog_id` FOREIGN KEY (`eecom_blog_id`,`eecom_blog_version_id`) REFERENCES `eecom_blog` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        ');

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_visibility` (
                `id` BINARY(16) NOT NULL,
                `eecom_blog_id` BINARY(16) NOT NULL,
                `eecom_blog_version_id` BINARY(16) NOT NULL,
                `sales_channel_id` BINARY(16) NOT NULL,
                `visibility` INT(11) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                KEY `fk.eecom_blog_visibility.sales_channel_id` (`sales_channel_id`),
                KEY `fk.eecom_blog_visibility.eecom_blog_id` (`eecom_blog_id`,`eecom_blog_version_id`),
                CONSTRAINT `fk.eecom_blog_visibility.sales_channel_id` FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_visibility.eecom_blog_id` FOREIGN KEY (`eecom_blog_id`,`eecom_blog_version_id`) REFERENCES `eecom_blog` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        ');

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_tag` (
                `eecom_blog_id` BINARY(16) NOT NULL,
                `eecom_blog_version_id` BINARY(16) NOT NULL,
                `tag_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`eecom_blog_id`,`eecom_blog_version_id`,`tag_id`),
                KEY `fk.eecom_blog_tag.eecom_blog_id` (`eecom_blog_id`,`eecom_blog_version_id`),
                KEY `fk.eecom_blog_tag.tag_id` (`tag_id`),
                CONSTRAINT `fk.eecom_blog_tag.eecom_blog_id` FOREIGN KEY (`eecom_blog_id`,`eecom_blog_version_id`) REFERENCES `eecom_blog` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_tag.tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        ');
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_search_keyword` (
                `id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `eecom_blog_id` BINARY(16) NOT NULL,
                `eecom_blog_version_id` BINARY(16) NOT NULL,
                `keyword` VARCHAR(255) NOT NULL,
                `ranking` DOUBLE NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                KEY `fk.eecom_blog_search_keyword.eecom_blog_id` (`eecom_blog_id`,`eecom_blog_version_id`),
                KEY `fk.eecom_blog_search_keyword.language_id` (`language_id`),
                CONSTRAINT `fk.eecom_blog_search_keyword.eecom_blog_id` FOREIGN KEY (`eecom_blog_id`,`eecom_blog_version_id`) REFERENCES `eecom_blog` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_search_keyword.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        ');
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_category` (
                `id` BINARY(16) NOT NULL,
                `active` TINYINT(1) NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_category_translation` (
                `name` VARCHAR(255) NOT NULL,
                `meta_title` LONGTEXT NULL,
                `meta_description` LONGTEXT NULL,
                `keywords` LONGTEXT NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                `eecom_blog_category_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`eecom_blog_category_id`,`language_id`),
                CONSTRAINT `json.eecom_blog_category_translation.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
                KEY `fk.eecom_blog_category_translation.eecom_blog_category_id` (`eecom_blog_category_id`),
                KEY `fk.eecom_blog_category_translation.language_id` (`language_id`),
                CONSTRAINT `fk.eecom_blog_category_translation.eecom_blog_category_id` FOREIGN KEY (`eecom_blog_category_id`) REFERENCES `eecom_blog_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_category_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_category_mapping` (
               `eecom_blog_id` BINARY(16) NOT NULL,
                `eecom_blog_version_id` BINARY(16) NOT NULL,
                `category_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`eecom_blog_id`,`category_id`,`eecom_blog_version_id`),
                KEY `fk.eecom_blog_category_mapping.eecom_blog_id` (`eecom_blog_id`,`eecom_blog_version_id`),
                KEY `fk.eecom_blog_category_mapping.category_id` (`category_id`),
                CONSTRAINT `fk.eecom_blog_category_mapping.eecom_blog_id` FOREIGN KEY (`eecom_blog_id`,`eecom_blog_version_id`) REFERENCES `eecom_blog` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_category_mapping.category_id` FOREIGN KEY (`category_id`) REFERENCES `eecom_blog_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');



    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
