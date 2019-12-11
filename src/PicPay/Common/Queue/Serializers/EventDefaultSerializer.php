<?php

namespace PicPay\PicPay\Common\Queue\Serializers;

use Enqueue\RdKafka\RdKafkaMessage;
use Enqueue\RdKafka\Serializer;

/**
 * Class EventDefaultSerializer
 * @package PicPay\PicPay\Common\Queue\Serializers
 */
class EventDefaultSerializer implements Serializer
{
    /**
     * @param RdKafkaMessage $message
     * @return string
     */
    public function toString(RdKafkaMessage $message): string
    {
        $json = json_encode([
            'body' => $message->getBody(),
            'properties' => $message->getProperties(),
            'headers' => $message->getHeaders(),
        ]);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf(
                'The malformed json given. Error %s and message %s',
                json_last_error(),
                json_last_error_msg()
            ));
        }

        return $json;
    }

    /**
     * @param string $string
     * @return RdKafkaMessage
     */
    public function toMessage(string $string): RdKafkaMessage
    {
        $data = json_decode($string, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf(
                'The malformed json given. Error %s and message %s',
                json_last_error(),
                json_last_error_msg()
            ));
        }

        return new RdKafkaMessage(
            isset($data['body']) ? $data['body'] : $string,
            isset($data['body']) ? $data['properties'] : [],
            isset($data['body']) ? $data['headers'] : []
        );
    }
}