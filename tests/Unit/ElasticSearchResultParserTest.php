<?php

namespace Drupal\Test\cgk_elastic_api\Unit;

use Drupal\cgk_elastic_api\Search\ElasticSearchResultParser;
use Drupal\cgk_elastic_api\Search\FacetedSearchAction;
use Drupal\cgk_elastic_api\Search\SearchResult;
use PHPUnit\Framework\TestCase;

/**
 * Test case for \Drupal\cgk_elastic_api\Search\ElasticSearchResultParser.
 *
 * @coversDefaultClass \Drupal\cgk_elastic_api\Search\ElasticSearchResultParser
 */
class ElasticSearchResultParserTest extends TestCase {

  /**
   * Tests parsing of the search results into a SearchResult object.
   */
  public function testParse() {
    $parser = new ElasticSearchResultParser();

    // Sample taken from some demo data.
    $response = [
      'took' => 1,
      'timed_out' => FALSE,
      '_shards' =>
        [
          'total' => 5,
          'successful' => 5,
          'failed' => 0,
        ],
      'hits' =>
        [
          'total' => 12,
          'max_score' => 2.7183442,
          'hits' =>
            [
              0 =>
                [
                  '_index' => 'elasticsearch_index_drupal_cgk',
                  '_type' => 'cgk',
                  '_id' => 'entity:node/113:nl',
                  '_score' => 2.7183442,
                ],
              1 =>
                [
                  '_index' => 'elasticsearch_index_drupal_cgk',
                  '_type' => 'cgk',
                  '_id' => 'entity:node/111:nl',
                  '_score' => 2.6657796,
                ],
            ],
        ],
      'aggregations' =>
        [
          'steunverlenende_overheid' =>
            [
              'doc_count' => 2,
              'filtered' =>
                [
                  'doc_count_error_upper_bound' => 0,
                  'sum_other_doc_count' => 0,
                  'buckets' =>
                    [
                      0 =>
                        [
                          'key' => 41,
                          'doc_count' => 1,
                        ],
                      1 =>
                        [
                          'key' => 44,
                          'doc_count' => 1,
                        ],
                    ],
                ],
            ],
          'omvang_bedrijf' =>
            [
              'doc_count' => 5,
              'filtered' =>
                [
                  'doc_count_error_upper_bound' => 0,
                  'sum_other_doc_count' => 0,
                  'buckets' =>
                    [
                      0 =>
                        [
                          'key' => 52,
                          'doc_count' => 3,
                        ],
                      1 =>
                        [
                          'key' => 53,
                          'doc_count' => 2,
                        ],
                    ],
                ],
            ],
          'leeftijd_onderneming' =>
            [
              'doc_count' => 2,
              'filtered' =>
                [
                  'doc_count_error_upper_bound' => 0,
                  'sum_other_doc_count' => 0,
                  'buckets' =>
                    [
                      0 =>
                        [
                          'key' => 50,
                          'doc_count' => 1,
                        ],
                      1 =>
                        [
                          'key' => 51,
                          'doc_count' => 1,
                        ],
                    ],
                ],
            ],
          'juridische_vorm' =>
            [
              'doc_count' => 2,
              'filtered' =>
                [
                  'doc_count_error_upper_bound' => 0,
                  'sum_other_doc_count' => 0,
                  'buckets' =>
                    [
                      0 =>
                        [
                          'key' => 55,
                          'doc_count' => 1,
                        ],
                      1 =>
                        [
                          'key' => 56,
                          'doc_count' => 1,
                        ],
                    ],
                ],
            ],
          'type_tegemoetkoming' =>
            [
              'doc_count' => 2,
              'filtered' =>
                [
                  'doc_count_error_upper_bound' => 0,
                  'sum_other_doc_count' => 0,
                  'buckets' =>
                    [
                      0 =>
                        [
                          'key' => 37,
                          'doc_count' => 2,
                        ],
                    ],
                ],
            ],
          'sector' =>
            [
              'doc_count' => 2,
              'filtered' =>
                [
                  'doc_count_error_upper_bound' => 0,
                  'sum_other_doc_count' => 0,
                  'buckets' =>
                    [
                      0 =>
                        [
                          'key' => 32,
                          'doc_count' => 2,
                        ],
                    ],
                ],
            ],
        ],
    ];

    $searchAction = new FacetedSearchAction(
      2,
      NULL,
      ['sector', 'steunverlenende_overheid', 'omvang_bedrijf']
    );

    $expected_result = new SearchResult(
      12,
      [
        'entity:node/113:nl',
        'entity:node/111:nl',
      ],
      [
        'sector' => [
          32 => 2,
        ],
        'steunverlenende_overheid' => [
          41 => 1,
          44 => 1,
        ],
        'omvang_bedrijf' => [
          52 => 3,
          53 => 2,
        ],
      ]
    );

    $this->assertEquals($expected_result, $parser->parse($searchAction, $response));
  }

}
