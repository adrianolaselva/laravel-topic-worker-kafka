<?php


namespace PicPay\Common\Kafka;

use Closure;

class RdKafka
{
    /**
     * @param $topic
     * @param $message
     * @param array $properties
     * @param array $headers
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function publish($topic, $message, array $properties = [], array $headers = [])
    {
        /* @var Publisher $publisher */
        $publisher = app()->make(Publisher::class);
        $publisher
//            ->mergeProperties($properties)
            ->connect();

        $publisher->publish($topic, $message, $properties, $headers);
    }

    /**
     * @param $topic
     * @param Closure $callback
     * @param array $properties
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function consume($topic, Closure $callback, $properties = [])
    {
        /* @var Consumer $consumer */
        $consumer = app()->make(Consumer::class);
        $consumer
//            ->mergeProperties($properties)
            ->connect();

        $consumer->consume($topic, $callback);
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