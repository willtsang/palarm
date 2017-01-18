<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/18
 * Time: 16:19
 */

namespace Palarm\Sender;

class Sender
{


    public function send()
    {
        $message = Message::getInstance();

        if (empty($message)) {
            throw new \InvalidArgumentException('Message Instance have not be set');
        }

        $sender = $this->facory($message->getLevel());

        $sender->send($message);
    }
}
