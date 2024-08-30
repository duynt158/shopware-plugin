<?php declare(strict_types=1);

namespace EECom\EEComBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1627901496 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1627901496;
    }


    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `eecom_blog_translation`
            ADD COLUMN `slot_config` JSON NULL AFTER `custom_search_keywords`
        ');

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
