<?php

namespace Drupal\cgk_elastic_api;

use nodespark\DESConnector\ClientInterface;

/**
 * Class SynonymSync.
 */
interface SyncStrategyInterface {

  /**
   * Sync the data with the index.
   *
   * @param \nodespark\DESConnector\ClientInterface $client
   *   The Elasticsearch client.
   *
   * @param array $settingsParams
   * @param array $mappingParams
   *
   * @return bool
   *   TRUE if the sync is successful.
   */
  public function execute(ClientInterface $client, array $settingsParams = [], array $mappingParams = []);

}
