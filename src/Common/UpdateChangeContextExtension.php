<?php declare(strict_types=1);

namespace EECom\EEComBlog\Common;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Framework\Uuid\Uuid;
use function ksort;
use function mb_strlen;

class UpdateChangeContextExtension extends Struct
{
    private const NAME = 'eecom.blog.publisher.common-update-change-detector';

    /**
     * @var string[]
     */
    private array $changes = [];

    /**
     * @var EntityDefinition[]
     */
    private array $definitions = [];

    public static function extract(Context $context): self
    {

        if (!$context->hasExtension(self::NAME)) {
            $context->addExtension(self::NAME, new self());
        }


        return $context->getExtension(self::NAME);
    }

    public function addResult(UpdateCommand $updateCommand, bool $changed): void
    {
        $definition = $updateCommand->getDefinition();

        $this->definitions[$definition->getEntityName()] = $definition;

        $key = $this->generateKey($definition->getEntityName(), $updateCommand->getPrimaryKey());

        if (isset($this->changes[$key]) && $this->changes[$key] === true) {
            return;
        }

        $this->changes[$key] = $changed;
    }

    private function generateKey(string $entityName, $primaryKey): string
    {
        $serializedData = '';
        ksort($primaryKey);

        foreach ($primaryKey as $name => $value) {
            if (mb_strlen($value, '8bit') === 16) {
                $value = Uuid::fromBytesToHex($value);
            }

            $serializedData .= $name . ':' . $value . ';';
        }

        return $entityName . '__' . $serializedData;
    }

    public function hasChanges(EntityWriteResult $writeResult): bool
    {
        $entityName = $writeResult->getEntityName();

        if (!isset($this->definitions[$entityName])) {
            return false;
        }

        $definition = $this->definitions[$entityName];
        $pks = $definition->getPrimaryKeys();

        $primaryKeyData = [];
        foreach ($pks as $field) {
            $primaryKeyData[$field->getStorageName()] = $writeResult->getPayload()[$field->getPropertyName()];
        }

        $key = $this->generateKey($writeResult->getEntityName(), $primaryKeyData);

        return isset($this->changes[$key]) && $this->changes[$key] === true;
    }
}
