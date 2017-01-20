<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/16
 * Time: 15:26
 */

namespace Palarm;

use Palarm\Collector\CollectorInterface;
use Palarm\Sender\SenderInterface;
use Palarm\Strategy\AbstractStrategy;
use Palarm\Sender\Send;

class Alarm
{
    const VERSION = '1.0.1';
    const VERSION_ID = 10001;

    private $collector;
    private $strategy;

    /*
     * ==========================================
     * Alarm类封装了收集器, 策略, 消息通知的相关逻辑
     * 收集器, 策略, 通知 三者相互独立, 可自生封装传它
     * 们之间的工作逻辑;
     *
     * Alarm类提供了添加消息通知类接口, 由此可扩展消息
     * 通知;
     * ==========================================
     */


    public function __construct(CollectorInterface $collector, AbstractStrategy $strategy)
    {
        $this->collector = $collector;
        $this->strategy = $strategy;

        $this->sender = new Send();
    }

    /**
     * 收集器执行收集后有两种逻辑
     *
     * 1.可直接通过策略层
     * 2.经过策略分析, 发送消息
     *
     * 为何可直接通过策略层?
     * 在数据提取过程中, 可能会出现提取过程中就需要发送消息, 比如:错误数据量巨大等等
     * 所以在策略层也设置了直接通过消息的方法
     */
    public function handle()
    {
        $this->collector->collect();

        if ($this->collector->isStraight()) {
            echo 'debug' . 'straight' . "\n";
            $this->straight();
        } else {
            echo 'debug' . 'analyze start ' . "\n";
            $this->analyze();
        }
    }


    /**
     * 执行分析策略
     * 提取的数据集合是空不必经过策略层
     */
    protected function analyze()
    {
        $collection = $this->collector->getCollection();

        if (empty($collection)) {
            echo 'debug' . 'collection is empty' . "\n";
            return;
        }

        $this->strategy->analyze($collection);

        if ($this->strategy->needAlarm()) {
            $this->sender->send();
        }
    }

    /**
     * 直接通过策略层, 并发送消息
     */
    protected function straight()
    {
        $this->strategy->straight();

        $this->sender->send();
    }

    /**
     * 添加自定义通知类接口
     *
     * @param $level
     * @param SenderInterface $sender
     */
    public function addSender($level, SenderInterface $sender)
    {
        $this->sender->addSender($level, $sender);
    }
}
