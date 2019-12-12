<?php


namespace PicPay\Common\Kafka;

use Closure;

class RdKafka
{

    public function publish($routing, $message, array $properties = [])
    {
        $properties['routing'] = $routing;

        /* @var Publisher $publisher */
        $publisher = app()->make(Publisher::class);
        $publisher
            ->mergeProperties($properties)
            ->setup();

        if (is_string($message)) {
            $message = new Message($message, ['content_type' => 'text/plain', 'delivery_mode' => 2]);
        }

        $publisher->publish($routing, $message);
//        Request::shutdown($publisher->getChannel(), $publisher->getConnection());
    }

    public function consume($queue, Closure $callback, $properties = [])
    {
        $properties['queue'] = $queue;

        /* @var Consumer $consumer */
        $consumer = app()->make(Consumer::class);
        $consumer
            ->mergeProperties($properties)
            ->setup();

        $consumer->consume($queue, $callback);
        Request::shutdown($consumer->getChannel(), $consumer->getConnection());
    }

    public function message(string $body = '', array $properties = [], array $headers = [])
    {
        return new Message($body, $properties, $headers);
    }
}