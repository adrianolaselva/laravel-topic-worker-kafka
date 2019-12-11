<?php

namespace PicPay\PicPay\Common\Queue\Connectors;


use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Illuminate\Queue\Connectors\ConnectorInterface;
use PicPay\PicPay\Common\Queue\RdKafkaQueue;

/**
 * Class RdKafkaConnector
 * @package PicPay\PicPay\Common\Queue\Connectors
 */
class RdKafkaConnector implements ConnectorInterface
{

    /**
     * @param array $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new RdKafkaQueue($this->getConnectionFactory($config));
    }

    /**
     * @param array $config
     * @return RdKafkaConnectionFactory
     */
    private function getConnectionFactory(array $config): RdKafkaConnectionFactory
    {
        return new RdKafkaConnectionFactory([
            'global' => [
                'group.id' => $config['group.id'] ?? 'default',
                'metadata.broker.list' => $config['metadata.broker.list'] ?? 'localhost:9092',
                'enable.auto.commit' => $config['enable.auto.commit'] ?? 'false',
            ],
            'topic' => [
                'auto.offset.reset' => $config['auto.offset.reset'] ?? 'earliest',
            ],
        ]);
    }
}