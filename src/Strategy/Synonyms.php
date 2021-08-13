<?php

namespace Drupal\cgk_elastic_api\Strategy;

use Drupal\cgk_elastic_api\SyncStrategy;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\search_api\Entity\Index;
use nodespark\DESConnector\ClientInterface;

/**
 * Strategy to sync synonyms.
 *
 * @package Drupal\cgk_elastic_api\Strategy
 */
class Synonyms extends SyncStrategy {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * SynonymSync constructor.
   *
   * @param \Drupal\search_api\Entity\Index $index
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  public function __construct(Index $index, ConfigFactoryInterface $configFactory) {
    parent::__construct($index);
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ClientInterface $client, array $settingsParams = [], array $mappingParams = []) {
    $synonyms = $this->configFactory->get('cgk_elastic_api.synonym_settings')
      ->get('synonyms');

    if (is_null($synonyms)) {
      return TRUE;
    }

    $synonyms = explode("\r\n", $synonyms);
    $synonyms = array_map(function ($synonym) {
      return trim($synonym, ',');
    }, $synonyms);

    $settingsParams = ['index' => $this->indexName];
    $settingsParams['body'] = [
      "index" => [
        "analysis" => [
          "filter" => [
            "synonym" => [
              "type" => "synonym_graph",
              "synonyms" => $synonyms,
              "ignore_case" => TRUE,
            ],
          ],
          "analyzer" => [
            "default" => [
              "tokenizer" => "whitespace",
              "filter" => ["lowercase", "synonym"],
            ],
          ],
        ],
      ],
    ];

    parent::execute($client, $settingsParams);
  }

}
