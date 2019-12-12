<?php


namespace PicPay\Common\Queue;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\Serializer;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;
use Illuminate\Support\Arr;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use PicPay\Common\Queue\Jobs\RdKafkaJob;
use PicPay\Common\Queue\Serializers\EventDefaultSerializer;

/**
 * Class RdKafkaQueue
 * @package PicPay\Common\Queue
 */
class RdKafkaQueue extends Queue implements QueueContract
{

    /**
     * @var RdKafkaConnectionFactory
     */
    protected $connectionFactory;

    /**
     * The name of the default config.
     *
     * @var array
     */
    protected $config;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * RdKafkaQueue constructor.
     * @param RdKafkaConnectionFactory $connectionFactory
     * @param array $config
     * @param Serializer|null $serializer
     */
    public function __construct(
        RdKafkaConnectionFactory $connectionFactory,
        array $config = [],
        Serializer $serializer = null)
    {
        $this->connectionFactory = $connectionFactory;
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * Get the size of the queue.
     *
     * @param string|null $queue
     * @return int
     */
    public function size($queue = null): int
    {
//        $queue = $this->getQueue($queue);
//        return 1;
    }

    /**
     * @param object|string $job
     * @param string $data
     * @param null $queue
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function push($job, $data = '', $queue = null): void
    {
        $this->pushRaw($this->createPayload($job, $queue, $data), $queue);
    }

    /**
     * @param string $payload
     * @param null $queue
     * @param array $options
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function pushRaw($payload, $queue = null, array $options = []): void
    {
        $context = $this->getContext();

        $context->createProducer()
            ->send($context->createQueue($this->getQueue($queue)), $context->createMessage($payload));
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateInterval|\DateTimeInterface|int $delay
     * @param object|string $job
     * @param string $data
     * @param null $queue
     * @return mixed|void
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $this->pushRaw($this->createPayload($job, $this->getQueue($queue), $data), $queue);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        $consumer = $this->getConsumer($queue);

        $message = $consumer->receive();

        if(!empty($message)) {
            return new RdKafkaJob(
                $this->container,
                $consumer,
                $queue,
                $message
            );
        }

        return null;
    }

    /**
     * @param string $topicName
     * @return Consumer
     */
    private function getConsumer(string $topicName): Consumer
    {
        $context = $this->connectionFactory
            ->createContext();

        $consumer = $context->createConsumer(
            $context->createQueue($topicName)
        );

        $consumer->setSerializer($this->serializer ?? new EventDefaultSerializer());

        return $consumer;
    }

    /**
     * @return Context
     */
    private function getContext(): Context
    {
        return $this->connectionFactory
            ->createContext();
    }

    /**
     * @throws \Exception
     */
    public function close(): void
    {
        $this->getContext()
            ->close();
    }

    /**
     * @param $queue
     * @return string
     */
    private function getQueue($queue): string
    {
        return $queue ?? Arr::get($this->config, 'queue');
    }
}