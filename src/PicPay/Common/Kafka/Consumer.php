<?php

namespace PicPay\Common\Kafka;

use Enqueue\RdKafka\RdKafkaConsumer;
use Enqueue\RdKafka\RdKafkaMessage;
use Closure;

/**
 * Class Consumer
 * @package PicPay\Common\Kafka
 */
class Consumer extends Request
{

    /**
     * @var RdKafkaConsumer
     */
    private $consumer;

    /**
     * @param $queue
     * @param Closure $closure
     * @throws \Exception
     */
    public function consume($queue, Closure $closure)
    {
        $this->consumer = $this->getConsumer($queue);

        do {
            try{
                $message = $this->consumer->receive();

                if(empty($message))
                    continue;

                $closure($message, $this);
            }catch (\Exception $ex){
                throw new \Exception($ex->getMessage(), 0, $ex);
            }
        } while(true);
    }

    /**
     * @param RdKafkaMessage $message
     */
    public function acknowledge(RdKafkaMessage $message)
    {
        $this->consumer->acknowledge($message);
    }

    /**
     * @param RdKafkaMessage $message
     * @param bool $reenqueue
     */
    public function reject(RdKafkaMessage $message, bool $reenqueue = false)
    {
        $this->consumer->reject($message, $reenqueue);
    }

}
