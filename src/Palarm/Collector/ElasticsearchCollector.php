<?php

/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/16
 * Time: 15:31
 */

namespace Palarm\Collector;

use Palarm\Record\Collection;
use Palarm\Record\RequestTimeRecord;

class ElasticsearchCollector implements CollectorInterface
{
    /*
     * =============
     *
     * 从elasticsearch中获取数据
     *
     * =============
     */

    /**
     * @var object http请求客户端
     */
    private $client;

    private $collection;

    private $straight = false;

    /**
     * ElasticsearchCollector constructor.
     * @param \GuzzleHttp\Client|object $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function collect()
    {
        $nowTime = time();

        $count = $this->getCountInFiveMinutes($nowTime);

        if (0 == $count) {
            return;
        }

        if ($count > 500) {
            $this->straight = true;
        }

        $this->collection = $this->getDataInFiveMinutes($nowTime);
    }

    /**
     * @inheritdoc
     */
    public function isStraight()
    {
        return $this->straight;
    }

    /**
     * @inheritdoc
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * 获取5分钟内, 超过300ms的请求数量
     *
     * @param int $nowTime
     * @return int
     */
    protected function getCountInFiveMinutes($nowTime)
    {
        $uri = '/index-expert/_count';

        $body = $this->getSearchBody($nowTime);

        if ($this->client instanceof \GuzzleHttp\Client) {
            $response = $this->client->post($uri, ['body' => $body]);

            $content = $response->getBody()->getContent();

            $result = json_decode($content, true);

            if (!isset($result['count'])) {
                throw new \RuntimeException('Can not get count of search');
            }

            return $result['count'];
        }
    }


    /**
     * 获取5分钟内, 超过300ms的所有请求数据
     *
     * @param int $nowTime
     * @return Collection
     */
    protected function getDataInFiveMinutes($nowTime)
    {
        $uri = '/index-expert/_search';

        $body = $this->getSearchBody($nowTime);


        if ($this->client instanceof \GuzzleHttp\Client) {
            $response = $this->client->post($uri, ['body' => $body]);

            $content = $response->getBody()->getContent();

            $result = json_decode($content, true);

            if (!isset($result['hits'])) {
                throw new \RuntimeException('Can not get data of search');
            }

            $result = $result['hits']['hist'];

            $collection = new Collection();

            foreach ($result as $value) {

                $record = new RequestTimeRecord(
                    $value['_source']['time'],
                    $value['_source']['requestTime']
                );

                $collection->add($record);
            }

            $this->collection = $collection;
        }
    }


    /**
     * 获取请求elastic的请求语句
     *
     * @param $nowTime
     * @return string
     */
    private function getSearchBody($nowTime)
    {
        $startTime = ($nowTime - 5 * 60) . '000';
        $endTime = $nowTime . '000';

        $body = '
        {
          "query" : {
            "filtered":{
              "filter":{
                "bool":{
                  "must":[
                  {
                    "range":{
                      "time":{
                        "gte":300
                      }
                    }
                   },
                    {
                      "range":{
                        "@timestamp":{
                          "gte":%d,
                          "lt":%d
                        }
                      }
                    }
                  ]
                }
              }
            }
          },
          "sort":[
           {"@timestamp":{"order": "asc"}}
          ],
          "_source":["time", "requestTime"]
         }
        ';

        return sprintf($body, (int)$startTime, (int)$endTime);
    }
}
