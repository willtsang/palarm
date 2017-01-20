<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/19
 * Time: 15:10
 */

namespace Palarm\Sender;

class NullSender implements SenderInterface
{

    public function send(Message $message)
    {
        return ;
    }
}
