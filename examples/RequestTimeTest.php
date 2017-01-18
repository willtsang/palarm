<?php

/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/18
 * Time: 17:23
 */

use Palarm\Collector\ElasticsearchCollector;
use Palarm\Strategy\RequestTimeStrategy;


$client  = '';

$collector = new ElasticsearchCollector($client);
$strategy = new RequestTimeStrategy();
$sender = new \Palarm\Sender\Sender();


$collector->collect();

$collector->isString();

$collection = $collector->getCollection();


if ($collector->isStrien()) {

} else {
    $collection = $collector->getCollection();
    if (empty($collection)) {
        return ;
    }

    $message = $strategy->analyze($collection);
    $sender->send();
}


if (!empty($collection)) {
    $message = $strategy->analyze($collection);
    $sender->send();

}










