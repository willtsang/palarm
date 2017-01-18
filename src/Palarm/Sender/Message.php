<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/18
 * Time: 16:19
 */

namespace Palarm\Sender;

class Message
{
    private $message;

    private $context;

    private $level;

    public static $instance;

    private function __construct()
    {
    }

    public static function make($message, $level, $context = [])
    {
        $instance = new self();

        $instance->message = $message;
        $instance->level = $level;
        $instance->context = $context;

        self::$instance = $instance;
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
