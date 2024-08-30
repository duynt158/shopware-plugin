<?php declare(strict_types=1);

namespace EECom\EEComBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1627903210 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1627903210;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_activity` (
               `id` BINARY(16) NOT NULL,
                `eecom_blog_id` BINARY(16) NOT NULL,
                `eecom_blog_version_id` BINARY(16) NOT NULL,
                `user_id` BINARY(16) NULL,
                `draft_version_id` VARCHAR(255) NULL,
                `name` VARCHAR(255) NULL,
                `details` JSON NULL,
                `is_merged` TINYINT(1) NULL DEFAULT 0,
                `is_discarded` TINYINT(1) NULL DEFAULT 0,
                `is_released_as_new` TINYINT(1) NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `json.eecom_blog_activity.details` CHECK (JSON_VALID(`details`)),
                KEY `fk.eecom_blog_activity.eecom_blog_id` (`eecom_blog_id`,`eecom_blog_version_id`),
                KEY `fk.eecom_blog_activity.user_id` (`user_id`),
                CONSTRAINT `fk.eecom_blog_activity.eecom_blog_id` FOREIGN KEY (`eecom_blog_id`,`eecom_blog_version_id`) REFERENCES `eecom_blog` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_activity.user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        ');

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `eecom_blog_draft` (
                `id` BINARY(16) NOT NULL,
                `draft_version_id` VARCHAR(255) NOT NULL,
                `deep_link_code` VARCHAR(255) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `eecom_blog_id` BINARY(16) NOT NULL,
                `eecom_blog_version_id` BINARY(16) NOT NULL,
                `owner_user_id` BINARY(16) NULL,
                `preview_media_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                KEY `fk.eecom_blog_draft.eecom_blog_id` (`eecom_blog_id`,`eecom_blog_version_id`),
                KEY `fk.eecom_blog_draft.owner_user_id` (`owner_user_id`),
                KEY `fk.eecom_blog_draft.preview_media_id` (`preview_media_id`),
                CONSTRAINT `fk.eecom_blog_draft.eecom_blog_id` FOREIGN KEY (`eecom_blog_id`,`eecom_blog_version_id`) REFERENCES `eecom_blog` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_draft.owner_user_id` FOREIGN KEY (`owner_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.eecom_blog_draft.preview_media_id` FOREIGN KEY (`preview_media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
