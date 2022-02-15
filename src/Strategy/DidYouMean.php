<?php

namespace Drupal\cgk_elastic_api\Strategy;

use Drupal\cgk_elastic_api\SyncStrategy;
use nodespark\DESConnector\ClientInterface;

/**
 * Class DidYouMean.
 */
class DidYouMean extends SyncStrategy {

  /**
   * {@inheritdoc}
   */
  public function execute(ClientInterface $client, array $settingsParams = [], array $mappingParams = []) {
    $response = $this->getFieldMapping($client, 'title');
    $fieldMapping = $response['mappings']['title']['mapping']['title'];

    $fieldMapping['fields']['trigram'] = [
      'type' => 'text',
      'analyzer' => 'trigram',
    ];

    $mappingParams = [
      'index' => $this->indexName,
      'body' => [
        'properties' => [
          'title' => $fieldMapping,
        ],
      ],
    ];

    $settingsParams = [
      'index' => $this->indexName,
      'body' => [
        'settings' => [
          'analysis' => [
            'filter' => [
              'shingle' => [
                'type' => 'shingle',
                'min_shingle_size' => 2,
                'max_shingle_size' => 3,
              ],
            ],
            'analyzer' => [
              'trigram' => [
                'type' => 'custom',
                'tokenizer' => 'standard',
                'filter' => [
                  'lowercase',
                  'shingle',
                ],
              ],
            ],
          ],
        ],
      ],
    ];

    parent::execute($client, $settingsParams, $mappingParams);
  }

}
