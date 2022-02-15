<?php

namespace Drupal\cgk_elastic_api;

use Drupal\elasticsearch_connector\ElasticSearch\Parameters\Factory\IndexFactory;
use Drupal\search_api\Entity\Index;
use nodespark\DESConnector\ClientInterface;

/**
 * SyncStrategy base class.
 *
 * @package Drupal\cgk_elastic_api
 */
class SyncStrategy implements SyncStrategyInterface {

  /**
   * Index.
   *
   * @var \Drupal\search_api\Entity\Index
   */
  protected $index;

  /**
   * The index name.
   */
  protected $indexName;

  /**
   * SyncStrategy constructor.
   *
   * @param \Drupal\search_api\Entity\Index $index
   */
  public function __construct(Index $index) {
    $this->index = $index;
    $this->indexName = IndexFactory::getIndexName($this->index);
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ClientInterface $client, array $settingsParams = [], array $mappingParams = []) {
    try {
      $client->indices()->close(['index' => $this->indexName]);
      if (!empty($settingsParams)) {
        $client->indices()->putSettings($settingsParams);
      }
      if (!empty($mappingParams)) {
        $client->indices()->putMapping($mappingParams);
      }
      return TRUE;
    }
    catch (\Exception $e) {
      watchdog_exception('cgk_elastic_api', $e);
      return FALSE;
    }

    finally {
      sleep(1);
      $client->indices()->open(['index' => $this->indexName]);
    }
  }

  /**
   * Returns the index field mapping request.
   *
   * @param \nodespark\DESConnector\ClientInterface $client
   * @param $fields
   *   A comma-separated list of fields
   *
   * @return false|mixed
   */
  public function getFieldMapping(ClientInterface $client, $fields) {
    $params = [
      'index' => $this->indexName,
      'fields' => $fields,
    ];

    $response = $client->indices()->getFieldMapping($params);
    if (!empty($response)) {
      // Strip the index.
      return current($response);
    }
    else {
      return FALSE;
    }
  }

}
