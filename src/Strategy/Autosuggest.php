<?php

namespace Drupal\cgk_elastic_api\Strategy;

use Drupal\cgk_elastic_api\SyncStrategy;
use nodespark\DESConnector\ClientInterface;

/**
 * Strategy to configure the autosuggest.
 *
 * @package Drupal\cgk_elastic_api\Strategy
 */
class Autosuggest extends SyncStrategy {

  /**
   * {@inheritdoc}
   */
  public function execute(ClientInterface $client, array $settingsParams = [], array $mappingParams = []) {
    $response = $this->getFieldMapping($client, 'title');
    $fieldMapping = $response['mappings']['title']['mapping']['title'];

    $fieldMapping['fields']['keyword'] = [
      "type" => "keyword",
      "ignore_above" => 256,
    ];
    $fieldMapping['copy_to'] = "search_suggest";
    $fieldMapping['boost'] = 21;

    $mappingParams = [
      'index' => $this->indexName,
      'body' => [
        "properties" => [
          "search_suggest" => [
            "type" => "completion",
          ],
          "title" => $fieldMapping,
        ],
      ],
    ];

    parent::execute($client, $settingsParams, $mappingParams);
  }

}
