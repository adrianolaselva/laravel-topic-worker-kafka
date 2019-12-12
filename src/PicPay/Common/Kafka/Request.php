<?php

namespace PicPay\Common\Kafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaConsumer;
use Enqueue\RdKafka\RdKafkaContext;
use PicPay\Common\Queue\Serializers\EventDefaultSerializer;

/**
 * Class Request
 * @package PicPay\Common\Kafka
 */
class Request extends Context
{

    /**
     * @var RdKafkaConnectionFactory
     */
    protected $connection;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var
     */
    protected $queue;

    /**
     * @return $this
     * @throws \Exception
     */
    public function connect()
    {
        if (empty($this->getProperty('parameters')))
            throw new \Exception("Parâmetros não configurados");

        if (empty($this->getProperty('topic')))
            throw new \Exception("Parâmetros não configurados");

        $this->queue = $this->getProperty('topic');
        $this->parameters = $this->getProperty('parameters');

        $this->connection = new RdKafkaConnectionFactory($this->parameters);

        return $this;
    }

    /**
     * @return RdKafkaConnectionFactory
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $topicName
     * @return RdKafkaConsumer
     */
    public function getConsumer(string $topicName): RdKafkaConsumer
    {
        $context = $this->connection
            ->createContext();

        $consumer = $context->createConsumer(
            $context->createQueue($topicName)
        );

        $consumer->setSerializer($this->serializer ?? new EventDefaultSerializer());

        return $consumer;
    }

    /**
     * @return RdKafkaContext
     */
    public function getContext(): RdKafkaContext
    {
        return $this->connection->createContext();
    }

    /**
     * Close context
     */
    public function close(): void
    {
        $this->getContext()
            ->close();
    }

    /**
     * @param string $queue
     * @return string
     */
    public function getQueue(string $queue): string
    {
        return empty($queue) ? $this->queue : $queue;
    }
}
