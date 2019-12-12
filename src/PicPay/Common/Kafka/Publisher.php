<?php

namespace PicPay\Common\Kafka;


class Publisher extends Request
{

    /**
     * @param $queue
     * @param $message
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function publish($queue, $message)
    {
        $context = $this->getContext();

        $context->createProducer()
            ->send(
                $context->createQueue($this->getQueue($queue)),
                $context->createMessage($message));
    }
}
