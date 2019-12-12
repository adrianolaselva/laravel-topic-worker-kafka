<?php

namespace PicPay\Common\Kafka;


class Publisher extends Request
{
    /**
     * @param $topic
     * @param $message
     * @param array $properties
     * @param array $headers
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function publish($topic, $message, $properties = [], $headers = [])
    {
        $context = $this->getContext();

        $context->createProducer()
            ->send(
                $context->createQueue($this->getQueue($topic)),
                $context->createMessage($message, $properties, $headers));
    }
}
