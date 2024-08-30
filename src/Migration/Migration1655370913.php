<?php declare(strict_types=1);

namespace EECom\EEComBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Defaults;

class Migration1655370913 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1655370913;
    }

    public function update(Connection $connection): void
    {

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

}
