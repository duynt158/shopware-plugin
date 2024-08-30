<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Internal;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Field;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;

class VersionFromVersionUpdater
{
    private const INSERT_OPERATION = 'insert';
    private const UPDATE_OPERATION = 'update';
    private const UPSERT_OPERATION = 'upsert';
    private const DELETE_OPERATION = 'delete';

    private EntityWriterInterface $entityWriter;

    private DefinitionInstanceRegistry $registry;

    private EntityRepositoryInterface $versionRepository;

    private EntityRepositoryInterface $versionCommitRepository;

    public function __construct(
        EntityWriterInterface      $entityWriter,
        DefinitionInstanceRegistry $registry,
        EntityRepositoryInterface  $versionRepository,
        EntityRepositoryInterface  $versionCommitRepository
    )
    {
        $this->entityWriter = $entityWriter;
        $this->registry = $registry;
        $this->versionRepository = $versionRepository;
        $this->versionCommitRepository = $versionCommitRepository;
    }

    public function updateFromVersion(string $versionId, WriteContext $writeContext): void
    {
        $versionContext = $writeContext->getContext();
        $commits = $this->fetchCommitsByVersionId($versionId, $versionContext);

        $entitiesToDelete = [];
        $versionIdToUpdate = $versionContext->getVersionId();

        foreach ($commits as $commit) {
            foreach ($commit->getData() as $data) {
                $dataDefinition = $this->registry
                    ->getByEntityName($data->getEntityName());

                $repository = $this->registry
                    ->getRepository($data->getEntityName());

                $entitiesToDelete[] = [
                    'definition' => $dataDefinition,
                    'primary' => $data->getEntityId(),
                ];

                if (!$data->getPayload() && $data->getAction() !== 'delete') {
                    continue;
                }

                switch ($data->getAction()) {
                    case self::INSERT_OPERATION:
                    case self::UPDATE_OPERATION:
                    case self::UPSERT_OPERATION:
                        $payload = $data->getPayload();
                        $this->addVersionToPayload($payload, $dataDefinition, $versionIdToUpdate);

                        $repository->upsert([$payload], $versionContext);

                        break;

                    case self::DELETE_OPERATION:
                        $id = $data->getEntityId();
                        $this->addVersionToPayload($id, $dataDefinition, $versionIdToUpdate);

                        $repository->delete([$id], $versionContext);

                        break;
                }
            }

            $this->versionCommitRepository->delete([['id' => $commit->getId()]], $versionContext);
        }

        $this->versionRepository->delete([['id' => $versionId]], $versionContext);

        $versionContext = $writeContext
            ->createWithVersionId($versionId);

        foreach ($entitiesToDelete as $entity) {
            $definition = $entity['definition'];
            $primary = $entity['primary'];

            $this->addVersionToPayload($primary, $definition, $versionId);
            $this->entityWriter->delete($definition, [$primary], $versionContext);
        }
    }

    private function fetchCommitsByVersionId(string $versionId, Context $context): EntityCollection
    {
        $criteria = $this->createVersionCommitCriteria($versionId);

        return $this->versionCommitRepository
            ->search($criteria, $context)
            ->getEntities();
    }

    private function createVersionCommitCriteria(string $versionId): Criteria
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('versionId', $versionId));
        $criteria->addSorting(new FieldSorting('autoIncrement'));
        $criteria->addAssociation('data');

        $criteria
            ->getAssociation('data')
            ->addSorting(new FieldSorting('autoIncrement'));

        return $criteria;
    }

    private function addVersionToPayload(array &$payload, EntityDefinition $definition, string $versionId): void
    {
        $fields = $definition->getFields()->filter(static function (Field $field) {
            return $field instanceof VersionField || $field instanceof ReferenceVersionField;
        });

        foreach ($fields as $field) {
            $payload[$field->getPropertyName()] = $versionId;
        }
    }
}
