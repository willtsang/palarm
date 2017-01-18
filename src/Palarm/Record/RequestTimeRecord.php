<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/18
 * Time: 14:24
 */

namespace Palarm\Record;

class RequestTimeRecord extends AbstractRecord
{
    /**
     * @var int 请求消耗时间(单位:ms)
     */
    private $time;

    /**
     * @var int 请求时间
     */
    private $requestTime;


    public function __construct($time, $requestTime)
    {
        $this->time = $time;
        $this->requestTime = $requestTime;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }
}
