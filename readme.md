#Palarm
Palarm是一个用php编写, 简单,可扩展的报警系统; 

报警系统以 数据收集 -- 数据提取 -- 报警策略 -- 发送报警消息 的设计思路完成; Palarm只包含后三步骤, 数据收集因不同系统而异故不在本系统中.


##数据提取

提取逻辑由三个主要方法构成

    <?php
    
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

##报警策略
策略会生成一定的消息实例


    <?php
    
    namespace Palarm\Strategy;
    
    use Palarm\Record\Collection;
    
    abstract class AbstractStrategy
    {
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



##发送报警消息
发送消息实例; 消息实例由策略层产生, 本层只负责选择消息媒介, 并发送消息


##主要特性

* 数据提取,策略,消息发送均可扩展
* `Alram`已完成逻辑封装,使用简单
* 可绕过策略分析, 直接通过策略层
* 针对不同等级错误, 可使用渠道发送消息

