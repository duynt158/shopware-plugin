<?php declare(strict_types=1);

namespace EECom\EEComBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1655313413 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1655313413;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `eecom_blog_category`
            ADD COLUMN `position` INT(11) NOT NULL DEFAULT 1 AFTER `active`
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
