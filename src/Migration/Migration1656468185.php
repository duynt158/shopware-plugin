<?php declare(strict_types=1);

namespace EECom\EEComBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Defaults;

class Migration1656468185 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1656468185;
    }

    public function update(Connection $connection): void
    {
        $configurationId = Uuid::randomBytes();
        $defaultFolderId = Uuid::randomBytes();
        $this->addMediaDefaultFolder($connection, $defaultFolderId);
        $cmsMediaId = $this->getCmsMediaFolder($connection);
        $parentId = $this->getParentFolderId($connection,$cmsMediaId);

        $this->addMediaFolderConfiguration($connection, $configurationId);
        $this->addMediaFolder($connection, $parentId, $configurationId, $defaultFolderId);

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
    private function addMediaDefaultFolder(Connection $connection, string $defaultFolderId): void
    {
        $sql = <<<SQL
INSERT IGNORE INTO `media_default_folder` (`id`, `association_fields`, `entity`, `created_at`)
VALUES (:id, '[]', 'eecom_blog', :createdAt);
SQL;

        $connection->executeStatement($sql, [
            ':id' => $defaultFolderId,
            ':createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }
    private function getCmsMediaFolder(Connection $connection): string
    {
        $query = <<<SQL
SELECT `id` FROM `media_default_folder` WHERE `entity` = "cms_page";
SQL;

        $id = $connection->fetchColumn($query);
        if (!\is_string($id)) {
            throw new \RuntimeException('Couldn\'t fetch parent id.');
        }

        return $id;
    }
    private function getBlogFolderId(Connection $connection, string $parentId): string
    {

        return $connection->fetchColumn('SELECT id from media_folder where parent_id = :parentId and name = "Blog"' , ['parentId' => $parentId]);
    }
    private function getParentFolderId(Connection $connection, string $cmsMediaId): string
    {

        return $connection->fetchColumn('SELECT id from media_folder where default_folder_id = :folderId', ['folderId' => $cmsMediaId]);
    }

    private function addMediaFolderConfiguration(Connection $connection, string $configurationId): void
    {
        $sql = <<<SQL
INSERT IGNORE INTO `media_folder_configuration` (`id`, `thumbnail_quality`, `create_thumbnails`, `private`, created_at)
VALUES (:id, 80, 1, 0, :createdAt);
SQL;

        $connection->executeStatement($sql, [
            ':id' => $configurationId,
            ':createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    private function addMediaFolder(Connection $connection, string $parentId, string $configurationId,string $defaultFolderId): void
    {
        $query = <<<SQL
INSERT INTO `media_folder` (`id`, `name`, `parent_id`,`default_folder_id`,  `media_folder_configuration_id`, `use_parent_configuration`, `child_count`, `created_at`)
VALUES (:folderId, 'Blog', :parentId,:defaultFolderId,:configurationId, 1, 0, :createdAt);
SQL;

        $connection->executeStatement($query, [
            ':folderId' => Uuid::randomBytes(),
            ':parentId' => $parentId,
            ':defaultFolderId' => $defaultFolderId,
            ':configurationId' => $configurationId,
            ':createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }


}
