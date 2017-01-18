<?php

/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/16
 * Time: 15:31
 */

namespace Palarm\Collector;

use Palarm\Record\Collection;
use Palarm\Strategy\AbstractStrategy;

class ElasticsearchCollector implements CollectorInterface
{
    /*
     * =============
     * 从elasticsearch中获取数据
     *
     * =============
     */

    /**
     * @var object http请求客户端
     */
    private $client;

    private $strategy;

    private $collection;

    private $straight = false;


    public function __construct($client, AbstractStrategy $strategy)
    {
        $this->client = $client;
        $this->strategy = $strategy;
    }

    /**
     *
     */
    public function collect()
    {
        $count = $this->getCountInFiveMinutes();

        if (0 == $count) {
            $this->straight = true;
        }

        if ($count > 10000) {

            $message = '';
            $data = [];

            $this->strategy->straight($message, $data);
        }

        $this->collection = $this->getDataInFiveMinutes();
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return int
     */
    protected function getCountInFiveMinutes()
    {

    }

    /**
     * @return boolean
     */
    public function isStraight()
    {
        return $this->straight;
    }

    /**
     * @return Collection
     */
    protected function getDataInFiveMinutes()
    {
        $currentTime = time(); // 时间戳


        // first get count



        // second get data

    }
}
