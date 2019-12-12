<?php


namespace PicPay\Common\Kafka;

use Closure;

class RdKafka
{

    /**
     * @param $topic
     * @param $message
     * @param array $properties
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function publish($topic, $message, array $properties = [])
    {

        /* @var Publisher $publisher */
        $publisher = app()->make(Publisher::class);
        $publisher
            ->mergeProperties($properties);

        if (is_string($message)) {
            $message = new Message($message, ['content_type' => 'text/plain', 'delivery_mode' => 2]);
        }

        $publisher->publish($topic, $message);
    }

    /**
     * @param $queue
     * @param Closure $callback
     * @param array $properties
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function consume($queue, Closure $callback, $properties = [])
    {

        /* @var Consumer $consumer */
        $consumer = app()->make(Consumer::class);
        $consumer
            ->mergeProperties($properties);

        $consumer->consume($queue, $callback);
//        Request::shutdown($consumer->getChannel(), $consumer->getConnection());
    }

    /**
     * @param string $body
     * @param array $properties
     * @param array $headers
     * @return Message
     */
    public function message(string $body = '', array $properties = [], array $headers = [])
    {
        return new Message($body, $properties, $headers);
    }
}