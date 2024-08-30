<?php declare(strict_types=1);

namespace EECom\EEComBlog\Elasticsearch\EEComBlog;

use Doctrine\DBAL\Connection;
use EECom\EEComBlog\Core\Content\EEComBlog\EEComBlogDefinition;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\QueryBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Elasticsearch\Framework\AbstractElasticsearchDefinition;
use Shopware\Elasticsearch\Framework\Indexing\EntityMapper;
use Shopware\Elasticsearch\Product\CustomFieldUpdater;

class ElasticsearchEEComBlogDefinition extends AbstractElasticsearchDefinition
{
    private const EECOM_BLOG_NAME_FIELDS = ['eecom_blog_translation.translation.name', 'eecom_blog_translation.translation.fallback_1.name', 'eecom_blog_translation.translation.fallback_2.name'];
    private const EECOM_BLOG_DESCRIPTION_FIELDS = ['eecom_blog_translation.translation.description', 'eecom_blog_translation.translation.fallback_1.description', 'eecom_blog_translation.translation.fallback_2.description'];
    private const EECOM_BLOG_CUSTOM_FIELDS = ['eecom_blog_translation.translation.custom_fields', 'eecom_blog_translation.translation.fallback_1.custom_fields', 'eecom_blog_translation.translation.fallback_2.custom_fields'];

    protected EEComBlogDefinition $definition;

    private Connection $connection;


    private ?array $customFieldsTypes = null;

    public function __construct(
        EEComBlogDefinition $definition,
        EntityMapper        $mapper,
        Connection          $connection
    )
    {
        parent::__construct($mapper);
        $this->definition = $definition;
        $this->connection = $connection;
    }

    public function getEntityDefinition(): EntityDefinition
    {

        return $this->definition;
    }

    public function getMapping(Context $context): array
    {


        return [
            '_source' => ['includes' => ['id']],
            'properties' => [
                'id' => EntityMapper::KEYWORD_FIELD,
                'active' => EntityMapper::BOOLEAN_FIELD,
                'description' => EntityMapper::KEYWORD_FIELD,
                'name' => EntityMapper::KEYWORD_FIELD,
                'publishedAt' => [
                    'type' => 'date',
                ],
                'tags' => [
                    'type' => 'nested',
                    'properties' => [
                        'id' => EntityMapper::KEYWORD_FIELD,
                    ],
                ],
                'visibilities' => [
                    'type' => 'nested',
                    'properties' => [
                        'id' => EntityMapper::KEYWORD_FIELD,
                        'visibility' => EntityMapper::INT_FIELD,
                    ],
                ],
                'customFields' => $this->getCustomFieldsMapping(),
            ]

        ];
    }

    public function extendDocuments(array $documents, Context $context): array
    {
        return $documents;
    }

    public function buildTermQuery(Context $context, Criteria $criteria): BoolQuery
    {
        $query = parent::buildTermQuery($context, $criteria);

        $query->add(
            new MatchQuery('description', (string)$criteria->getTerm()),
            BoolQuery::SHOULD
        );

        return $query;
    }

    /**
     * @throws \Exception
     */
    public function fetch(array $ids, Context $context): array
    {

        $data = $this->fetchBlogs($ids, $context);


        $documents = [];

        foreach ($data as $id => $item) {
            $visibilities = array_filter(explode('|', $item['visibilities'] ?? ''));

            $visibilities = array_map(function (string $text) {
                [$visibility, $salesChannelId] = explode(',', $text);

                return [
                    'visibility' => $visibility,
                    'salesChannelId' => $salesChannelId,
                ];
            }, $visibilities);

            $document = [
                'id' => $id,
                'name' => $this->stripText($item['name'] ?? ''),
                'active' => (bool)$item['active'],
                'customFields' => $this->formatCustomFields($item['customFields'] ? json_decode($item['customFields'], true) : []),
                'visibilities' => $visibilities,
                'description' => $this->stripText((string)$item['description']),
                'publishedAt' => isset($item['publishedAt']) ? (new \DateTime($item['publishedAt']))->format('c') : null,
                'tags' => array_map(fn(string $tagId) => ['id' => $tagId], json_decode($item['tagIds'] ?? '[]', true)),
                'tagIds' => json_decode($item['tagIds'] ?? '[]', true),
                'fullText' => $this->stripText(implode(' ', [$item['name'], $item['description']])),
                'fullTextBoosted' => $this->stripText(implode(' ', [$item['name'], $item['description']])),
            ];


            $documents[$id] = $document;
        }

        return $documents;
    }

    private function buildCoalesce(array $fields, Context $context): string
    {
        $fields = array_splice($fields, 0, \count($context->getLanguageIdChain()));

        $coalesce = 'COALESCE(';

        foreach (['eecom_blog_translation_main'] as $join) {
            foreach ($fields as $field) {
                $coalesce .= sprintf('%s.`%s`', $join, $field) . ',';
            }
        }

        return substr($coalesce, 0, -1) . ')';
    }

    private function getTranslationQuery(Context $context): QueryBuilder
    {

        $table = $this->definition->getEntityName() . '_translation';

        $query = new QueryBuilder($this->connection);

        $select = '`#alias#`.name  as `#alias#.name`, `#alias#`.description  as `#alias#.description`, `#alias#`.custom_fields  as `#alias#.custom_fields`';

        // first language has to be the from part, in this case we have to use the system language to enforce we have a record
        $chain = $context->getLanguageIdChain();

        $first = array_shift($chain);
        $firstAlias = 'eecom_blog_translation.translation';

        $foreignKey = EntityDefinitionQueryHelper::escape($firstAlias) . '.' . $this->definition->getEntityName() . '_id';

        // used as join condition
        $query->addSelect($foreignKey);

        // set first language as from part
        $query->addSelect(str_replace('#alias#', $firstAlias, $select));
        $query->from(EntityDefinitionQueryHelper::escape($table), EntityDefinitionQueryHelper::escape($firstAlias));
        $query->where(EntityDefinitionQueryHelper::escape($firstAlias) . '.language_id = :languageId');
        $query->andWhere(EntityDefinitionQueryHelper::escape($firstAlias) . '.eecom_blog_id IN(:ids)');
        $query->andWhere(EntityDefinitionQueryHelper::escape($firstAlias) . '.eecom_blog_version_id = :liveVersionId');
        $query->setParameter('languageId', Uuid::fromHexToBytes($first));

        foreach ($chain as $i => $language) {
            ++$i;

            $condition = '#firstAlias#.#column# = #alias#.#column# AND #alias#.language_id = :languageId' . $i;

            $alias = 'eecom_blog_translation.translation.fallback_' . $i;

            $variables = [
                '#column#' => EntityDefinitionQueryHelper::escape($this->definition->getEntityName() . '_id'),
                '#alias#' => EntityDefinitionQueryHelper::escape($alias),
                '#firstAlias#' => EntityDefinitionQueryHelper::escape($firstAlias),
            ];

            $query->leftJoin(
                EntityDefinitionQueryHelper::escape($firstAlias),
                EntityDefinitionQueryHelper::escape($table),
                EntityDefinitionQueryHelper::escape($alias),
                str_replace(array_keys($variables), array_values($variables), $condition)
            );

            $query->addSelect(str_replace('#alias#', $alias, $select));
            $query->setParameter('languageId' . $i, Uuid::fromHexToBytes($language));
        }

        return $query;
    }


    private function fetchBlogs(array $ids, Context $context): array
    {
        $sql = <<<'SQL'
SELECT
    LOWER(HEX(p.id)) AS id,
     p.active AS active,
    :nameTranslated: AS name,
    :descriptionTranslated: AS description,
    :customFieldsTranslated: AS customFields,
    p.published_at AS publishedAt,
    p.tag_ids AS tagIds,
    GROUP_CONCAT(CONCAT(eecom_blog_visibility.visibility, ',', LOWER(HEX(eecom_blog_visibility.sales_channel_id))) SEPARATOR '|') AS visibilities
FROM eecom_blog p
    LEFT JOIN eecom_blog_visibility ON(eecom_blog_visibility.eecom_blog_id = p.id AND eecom_blog_visibility.eecom_blog_version_id = :liveVersionId)
    LEFT JOIN (
        :eecomBlogTranslationQuery:
    ) eecom_blog_translation_main ON (eecom_blog_translation_main.eecom_blog_id = p.id)
WHERE p.id IN (:ids) AND p.version_id = :liveVersionId GROUP BY p.id
SQL;
        $translationQuery = $this->getTranslationQuery($context);

        $replacements = [
            ':eecomBlogTranslationQuery:' => $translationQuery->getSQL(),
            ':nameTranslated:' => $this->buildCoalesce(self::EECOM_BLOG_NAME_FIELDS, $context),
            ':descriptionTranslated:' => $this->buildCoalesce(self::EECOM_BLOG_DESCRIPTION_FIELDS, $context),
            ':customFieldsTranslated:' => $this->buildCoalesce(self::EECOM_BLOG_CUSTOM_FIELDS, $context),
        ];

        $data = $this->connection->fetchAll(
            str_replace(array_keys($replacements), array_values($replacements), $sql),
            array_merge([
                'ids' => $ids,
                'liveVersionId' => Uuid::fromHexToBytes($context->getVersionId()),
            ], $translationQuery->getParameters()),
            [
                'ids' => Connection::PARAM_STR_ARRAY,
            ]
        );

        return FetchModeHelper::groupUnique($data);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getCustomFieldsMapping(): array
    {
        $fieldMapping = $this->getCustomFieldTypes();
        $mapping = [
            'type' => 'object',
            'dynamic' => true,
            'properties' => [],
        ];

        foreach ($fieldMapping as $name => $type) {
            $esType = CustomFieldUpdater::getTypeFromCustomFieldType($type);

            if ($esType === null) {
                continue;
            }

            $mapping['properties'][$name] = $esType;
        }

        return $mapping;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function formatCustomFields(array $customFields): array
    {
        $types = $this->getCustomFieldTypes();

        foreach ($customFields as $name => $customField) {
            $type = $types[$name] ?? null;
            if ($type === CustomFieldTypes::BOOL) {
                $customFields[$name] = (bool)$customField;
            } elseif (\is_int($customField)) {
                $customFields[$name] = (float)$customField;
            }
        }

        return $customFields;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getCustomFieldTypes(): array
    {
        if ($this->customFieldsTypes !== null) {
            return $this->customFieldsTypes;
        }

        return $this->customFieldsTypes = $this->connection->fetchAllKeyValue('
SELECT
    custom_field.`name`,
    custom_field.type
FROM custom_field_set_relation
    INNER JOIN custom_field ON(custom_field.set_id = custom_field_set_relation.set_id)
WHERE custom_field_set_relation.entity_name = "eecom_blog"
');
    }
}
