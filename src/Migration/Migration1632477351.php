<?php declare(strict_types=1);

namespace EECom\EEComBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1632477351 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1632477351;
    }

    public function update(Connection $connection): void
    {

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
