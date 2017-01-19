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
        MessageLevel::ERROR => MailSender::class,
        MessageLevel::WARNING => MailSender::class,
        MessageLevel::FATAL => MailSender::class,
        MessageLevel::INFO => MailSender::class,
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
