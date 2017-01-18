<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/16
 * Time: 15:34
 */

namespace Palarm\Collector;

use Palarm\Record\Collection;

interface CollectorInterface
{
    /**
     * 将数据提取成所需要的数据结构
     */
    public function collect();

    /**
     * 获取收集器所提取数据集合
     *
     * @return Collection
     */
    public function getCollection();

    /**
     * 是否直接通过策略层
     *
     * @return boolean
     */
    public function isStraight();
}
