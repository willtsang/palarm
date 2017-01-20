<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/20
 * Time: 13:23
 */

namespace Palarm;

use Palarm\Collector\ElasticsearchCollector;
use Palarm\Strategy\RequestTimeStrategy;
use GuzzleHttp\Client;

class Worker
{
    /*
     * =================================================
     *
     *                       示例
     *
     * 监控服务器访问缓慢, 并报警
     *
     * 每5分钟获取一次超过300ms的请求, 并进行策略分析
     * 根据分析结果报警
     *
     * 以守护进程的方式执行, 配合supervisor完成守护进程, 重启等等
     *
     * Exception退出code为1, 即认为1可重启, supervisor的配置如下:
     *
     * autorestart=unexpected // 不以设定的code中则重启
     * exitcodes=0,2 // 不在0,2中则重启
     *
     * =================================================
     */

    /**
     * @var string process memory limit
     */
    protected $memoryLimit = '48';


    protected function execute()
    {
        $client = new Client();// 还需设置client的请求参数等等
        $collector = new ElasticsearchCollector($client);
        $strategy = new RequestTimeStrategy();

        $alarmManager = new Alarm($collector, $strategy);

        $interval = 300;

        while (true) {
            echo 'debug' . 'start' . "\n";
            $this->fire($alarmManager, $interval);
        }
    }

    /**
     * 将该进程重新启动, 需要配合supervisor
     *
     * supervisor 中existsCode为0,2
     * 0则会重启
     */
    protected function restart()
    {
        exit(1);
    }

    /**
     * 是否内存溢出
     *
     * @param $memoryLimit
     * @return bool
     */
    protected function overflowMemory($memoryLimit)
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * 运行
     *
     * @param Alarm  $alarmManager
     * @param int $interval 时间间隔
     */
    protected function fire($alarmManager, $interval)
    {
        /*
         * 进程的内存与运行时间成正比, 需制定重启策略
         */

        if ($this->overflowMemory($this->memoryLimit)) {

            echo 'debug' . 'overflowMemory' . "\n";
            $this->restart();
        }

        $startTime = time();

        $alarmManager->handle();

        $endTime = time();

        // 逻辑的执行时间不可能为大于5分钟, 即该差值必然>0
        $sleepTime = $interval - ($endTime - $startTime);

        sleep((int)$sleepTime);
    }
}
