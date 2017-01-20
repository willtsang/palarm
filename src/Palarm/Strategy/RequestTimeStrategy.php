<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/17
 * Time: 19:00
 */

namespace Palarm\Strategy;

use Palarm\Record\Collection;
use Palarm\Record\RequestTimeRecord;
use Palarm\Sender\Message;
use Palarm\Sender\MessageLevel;

class RequestTimeStrategy extends AbstractStrategy
{
    /**
     * @param Collection|RequestTimeRecord[] $collection
     */
    public function analyze(Collection $collection)
    {
        /**
         * @var int $recordStartTime 收集器所收集记录中, 记录的开始时间, 故所得到的请求记录的时间都大于或等于此数
         */
        $recordStartTime = $collection->getPayload();

        $oneMinuteScore = 0;
        $twoMinuteScore = 0;
        $fiveMinuteScore = 0;

        /*
         * 分为三个策略, 1分钟, 2分钟, 5分钟
         * 循环判断该请求属于哪个策略, 可加入多个策略
         * 根据请求响应时间计算该请求的分数
         * 不同策略有不同的分数计算等级
         * 由等级构建消息
         *
         * 在计算分数过程中, 一旦达到策略最大等级, 则停止计算, 直接构建消息
         */
        foreach ($collection as $item) {

            $intervalTime = $item->getRequestTime() - $recordStartTime;

            if ($intervalTime < 60) {
                $oneMinuteScore += $this->getScore($item->getTime());
            }

            if ($oneMinuteScore > 50) {
                break;
            }

            if ($intervalTime < 120) {
                $twoMinuteScore += $this->getScore($item->getTime());
            }

            if ($twoMinuteScore > 80) {
                break;
            }

            $fiveMinuteScore += $this->getScore($item->getTime());

            if ($fiveMinuteScore > 200) {
                break;
            }
        }

        $oneMinuteLevel = $this->oneMinuteLevel($oneMinuteScore);
        $twoMinuteLevel = $this->twoMinuteLevel($twoMinuteScore);
        $fiveMinuteLevel = $this->fiveMinuteLevel($fiveMinuteScore);

        $maxLevel = max($oneMinuteLevel, $twoMinuteLevel, $fiveMinuteLevel);

        echo 'debug' . 'maxLevel is ' . $maxLevel . "\n";

        if ($maxLevel < 2) {
            $this->alarm = false;
            return ;
        }

        $this->createMessage($maxLevel);
    }

    /**
     * @inheritdoc
     */
    public function needAlarm()
    {
        return $this->alarm;
    }

    /**
     * 直接创建报警消息
     */
    public function straight()
    {
        $message = '';
        $level = MessageLevel::ERROR;
        $context = [];

        Message::make($message, $level, $context);
    }

    /**
     * 提取的数据均为>300ms的记录
     *
     * @param int $time 请求毫秒
     * @return float
     */
    protected function getScore($time)
    {
        switch ($time) {
            case $time > 5000 :
                return 10;
            case $time > 4000 :
                return 9;
            case $time > 3000 :
                return 8;
            case $time > 2000 :
                return 7;
            case $time > 1500 :
                return 5;
            case $time > 1000 :
                return 3.5;
            case $time > 500 :
                return 2;
            default :
                return 1;
        }
    }

    protected function oneMinuteLevel($oneMinuteScore)
    {
        if ($oneMinuteScore > 50) {
            $oneMinuteLevel = MessageLevel::FATAL;
        } elseif ($oneMinuteScore > 40) {
            $oneMinuteLevel = MessageLevel::ERROR;
        } elseif ($oneMinuteScore > 30) {
            $oneMinuteLevel = MessageLevel::WARNING;
        } elseif ($oneMinuteScore > 20) {
            $oneMinuteLevel = MessageLevel::INFO;
        } else {
            $oneMinuteLevel = 0;
        }

        return $oneMinuteLevel;
    }

    protected function twoMinuteLevel($twoMinuteScore)
    {
        if ($twoMinuteScore > 80) {
            $twoMinuteScore = MessageLevel::FATAL;
        } elseif ($twoMinuteScore > 70) {
            $twoMinuteScore = MessageLevel::ERROR;
        } elseif ($twoMinuteScore > 50) {
            $twoMinuteScore = MessageLevel::WARNING;
        } elseif ($twoMinuteScore > 30) {
            $twoMinuteScore = MessageLevel::INFO;
        } else {
            $twoMinuteScore = 0;
        }

        return $twoMinuteScore;
    }

    protected function fiveMinuteLevel($fiveMinuteScore)
    {
        if ($fiveMinuteScore > 200) {
            $fiveMinuteScore = MessageLevel::INFO;
        } elseif ($fiveMinuteScore > 160) {
            $fiveMinuteScore = MessageLevel::INFO;
        } elseif ($fiveMinuteScore > 120) {
            $fiveMinuteScore = MessageLevel::INFO;
        } elseif ($fiveMinuteScore > 80) {
            $fiveMinuteScore = MessageLevel::INFO;
        } else {
            $fiveMinuteScore = 0;
        }

        return $fiveMinuteScore;
    }

    /**
     * 创建消息
     *
     * @param $maxLevel
     */
    protected function createMessage($maxLevel)
    {
        $message = '5分钟内的请求有点慢';

        Message::make($message, $maxLevel);
    }
}
