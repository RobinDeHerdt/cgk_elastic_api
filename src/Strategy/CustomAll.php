<?php

namespace Drupal\cgk_elastic_api\Strategy;

use Drupal\cgk_elastic_api\SyncStrategy;
use nodespark\DESConnector\ClientInterface;

/**
 * Strategy to copy mappings to a custom _all field.
 *
 * @package Drupal\cgk_elastic_api\Strategy
 */
class CustomAll extends SyncStrategy {

  const UNSUPPORTED_FIELD_TYPES = [
    'object',
    'nested',
  ];

  /**
   * {@inheritdoc}
   */
  public function execute(ClientInterface $client, array $settingsParams = [], array $mappingParams = []) {
    $response = $this->getFieldMapping($client, '*');

    $mappingParams = [
      'index' => $this->indexName,
      'type' => $this->index->id(),
      'body' => [
        'properties' => [
          'custom_all' => [
            'type' => 'text',
            'analyzer' => 'ngram_analyzer',
          ],
        ],
      ],
    ];

    $configuredFields = $this->index->getFields();

    foreach ($configuredFields as $configuredField) {
      if (in_array($configuredField->getType(), static::UNSUPPORTED_FIELD_TYPES)) {
        continue;
      }

      $mapping = $response['mappings'][$this->index->id()][$configuredField->getFieldIdentifier()]['mapping'][$configuredField->getFieldIdentifier()];
      if (!isset($mapping['copy_to']) || !in_array('custom_all', $mapping['copy_to'])) {
        $mapping['copy_to'][] = 'custom_all';
      }

      $mappingParams['body']['properties'][$configuredField->getFieldIdentifier()] = $mapping;
    }

    parent::execute($client, $settingsParams, $mappingParams);
  }

}
