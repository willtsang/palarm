<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/16
 * Time: 15:34
 */

namespace Palarm\Strategy;

use Palarm\Record\Collection;

abstract class AbstractStrategy
{

    /* ===================================================
     * 策略层的上一层是数据提取收集层
     *
     * 策略层对外暴露两个接口/功能
     * 1.进行策略分析, 通过定义的策略, 计算出消息实体后, 转移消息
     *   到发送层
     * 2.直接通过策略层到达消息层, 而一入口仅针对于在数据提取层就
     *   已经预知的报警消息, 如: error异常数据超过10k, 此时不需
     *   进行策略分析, 直接报警;
     *
     * ===================================================
     */

    /**
     * 直接通过策略层
     */
    public function straight()
    {
        throw new \LogicException('You Must Realize This Method');
    }

    /**
     * 执行策略
     *
     * @param Collection $abstractRecord
     */
    abstract public function analyze(Collection $abstractRecord);
}
