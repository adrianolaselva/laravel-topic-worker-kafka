<?php


namespace PicPay\Common\Queue\Jobs;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use Interop\Queue\Consumer;
use Interop\Queue\Message;

/**
 * Class RdKafkaJob
 * @package PicPay\Common\Queue\Jobs
 */
class RdKafkaJob extends Job implements JobContract
{

    /**
     * @var Consumer
     */
    protected $consumer;

    /**
     * @var string
     */
    protected $topic;

    /**
     * @var Message
     */
    protected $message;

    /**
     * RdKafkaJob constructor.
     * @param Container $container
     * @param Consumer $consumer
     * @param string $topic
     * @param Message $message
     */
    public function __construct(Container $container, Consumer $consumer, string $topic, Message $message)
    {
        $this->container = $container;
        $this->consumer = $consumer;
        $this->topic = $topic;
        $this->message = $message;
    }

    /**
     * Delete the job from the queue.
     */
    public function delete()
    {
        parent::delete();
        $this->consumer->acknowledge($this->message);
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->message->getMessageId();
    }

    /**
     * Get the raw body of the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->message->getBody();
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        // TODO: Implement attempts() method.
    }
}