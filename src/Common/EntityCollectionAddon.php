<?php declare(strict_types=1);

namespace EECom\EEComBlog\Common;

use Shopware\Core\Framework\Struct\ArrayEntity;
use function array_merge;
use function array_values;

trait EntityCollectionAddon
{
    public function asDelete(): array
    {
        return $this->forUpdate([]);
    }

    public function forUpdate(array $with): array
    {
        return array_values($this->map(static function (ArrayEntity $entity) use ($with): array {
            return array_merge(['id' => $entity->getId()], $with);
        }));
    }
}
