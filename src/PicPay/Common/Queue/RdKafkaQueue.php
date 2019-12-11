<?php


namespace PicPay\PicPay\Common\Queue;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\Serializer;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use PicPay\PicPay\Common\Queue\Jobs\RdKafkaJob;
use PicPay\PicPay\Common\Queue\Serializers\EventDefaultSerializer;

/**
 * Class RdKafkaQueue
 * @package PicPay\PicPay\Common\Queue
 */
class RdKafkaQueue extends Queue implements QueueContract
{

    /**
     * @var RdKafkaConnectionFactory
     */
    protected $connectionFactory;

    /**
     * The name of the default groupId.
     *
     * @var string
     */
    protected $groupId;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * RdKafkaQueue constructor.
     * @param RdKafkaConnectionFactory $connectionFactory
     * @param string $groupId
     * @param Serializer|null $serializer
     */
    public function __construct(
        RdKafkaConnectionFactory $connectionFactory,
        string $groupId = 'default',
        Serializer $serializer = null)
    {
        $this->connectionFactory = $connectionFactory;
        $this->groupId = $groupId;
        $this->serializer = $serializer;
    }

    /**
     * Get the size of the queue.
     *
     * @param string|null $queue
     * @return int
     */
    public function size($queue = null)
    {
        // TODO: Implement size() method.
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
            ->send($context->createQueue($queue), $context->createMessage($payload));
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string|object $job
     * @param mixed $data
     * @param string|null $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $consumer = $this->getConsumer($queue);

        if($message = $consumer->receive()) {
            return new RdKafkaJob(
                $this->container,
                $consumer,
                $queue,
                $message
            );
        }
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
}