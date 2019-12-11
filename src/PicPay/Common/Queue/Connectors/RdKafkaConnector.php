<?php

namespace PicPay\Common\Queue\Connectors;


use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Queue\Queue;
use PicPay\Common\Queue\RdKafkaQueue;
use Illuminate\Support\Arr;

/**
 * Class RdKafkaConnector
 * @package PicPay\Common\Queue\Connectors
 */
class RdKafkaConnector implements ConnectorInterface
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;


    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config): Queue
    {
        $queue = new RdKafkaQueue($this->getConnectionFactory($config), $config);

        $this->dispatcher->listen(WorkerStopping::class, static function () use ($queue): void {
            $queue->close();
        });

        return $queue;
    }

    /**
     * @param array $config
     * @return RdKafkaConnectionFactory
     */
    private function getConnectionFactory(array $config): RdKafkaConnectionFactory
    {
        return new RdKafkaConnectionFactory(Arr::get($config, 'parameters'));
    }
}