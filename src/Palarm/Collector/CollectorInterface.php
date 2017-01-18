<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/16
 * Time: 15:34
 */

namespace Palarm\Collector;

interface CollectorInterface
{
    /**
     * 每一个提取器都需实现该接口
     *
     * 将数据提取成所需要的数据结构, 以完成后需操作
     */
    public function collect();

    public function getCollection();

    public function isStraight();
}
