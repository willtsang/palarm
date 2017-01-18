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
    /**
     * @var string|int 消息主体
     */
    private $message;

    /**
     * @var array $context 消息上下文 可携带变量等等
     */
    private $context;

    /**
     * @var int 消息等级(MessageLevel中定义值)
     */
    private $level;

    public static $instance;

    private function __construct()
    {
    }

    /**
     * single instance
     *
     * @param $message
     * @param $level
     * @param array $context
     */
    public static function make($message, $level, $context = [])
    {
        $instance = new self();

        $instance->message = $message;
        $instance->level = $level;
        $instance->context = $context;

        self::$instance = $instance;
    }

    /**
     * Get $instance
     *
     * @return self
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Get context
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get message
     *
     * @return string|int
     */
    public function getMessage()
    {
        return $this->message;
    }
}
