<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/18
 * Time: 16:19
 */

namespace Palarm\Sender;

class Send
{
    private $sendMapping = [
        MessageLevel::ERROR => NullSender::class,
        MessageLevel::WARNING => NullSender::class,
        MessageLevel::FATAL => NullSender::class,
        MessageLevel::INFO => NullSender::class,
    ];

    /*
     * 发送层统一入口
     *
     * 1.完成消息类的构建
     * 2.扩展消息类
     *
     */


    /**
     * 发送消息
     */
    public function send()
    {
        $message = Message::getInstance();

        if (empty($message)) {
            return;
        }

        $sender = $this->factory($message->getLevel());

        // send strategy
        // 同一通知:
        // 上一封通知的十分钟内不再发相同消息提示, 但可发送等级更高的提示
        // 第二封与第三封间隔二十成分钟, 等级更高可发
        // 第三封与第四封间隔四十分钟, 等级更高可发
        // 第四封与第五封间隔八十分钟, 等级更高可发
        // 直到间隔超过八小时, 则重新开始

        $sender->send($message);
    }


    /**
     * 消息发送类构造工厂
     * 由等级确定发送类
     *
     * @param int $messageLevel 消息等级
     * @return SenderInterface
     */
    protected function factory($messageLevel)
    {
        $className = $this->sendMapping[$messageLevel];

        if (is_object($className)) {
            return $className;
        }

        return new $className();
    }


    /**
     * 添加/覆盖原来的基础发送类
     *
     * @param int $level
     * @param SenderInterface $sender
     */
    public function addSender($level, SenderInterface $sender)
    {
        if (!isset($this->sendMapping[$level])) {
            throw new \RuntimeException('Not Support Level Not In MessageLevel');
        }

        $this->sendMapping[$level] = $sender;
    }
}
