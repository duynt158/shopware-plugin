<?php declare(strict_types=1);

namespace EECom\EEComBlog\Common;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;

class PreDeletedEvent extends EntityDeletedEvent
{
    public function __construct(
        string $entityName,
        array $writeResult,
        Context $context,
        array $errors = []
    ) {
        parent::__construct($entityName, $writeResult, $context, $errors);

        $this->name = $entityName . '.pre-deleted';
    }
}
