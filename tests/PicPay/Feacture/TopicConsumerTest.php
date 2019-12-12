<?php

namespace Tests\PicPay\Feacture;

use Tests\PicPay\TestCase;

class TopicConsumerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        // partial mock of \Bschmitt\Amqp\Publisher
        // we want all methods except [connect] to be real
        $this->publisherMock = \Mockery::mock('\PicPay\Common\Kafka\Publisher[connect]', [$this->configRepository]);

//        $this->connectionMock = Mockery::mock('\PhpAmqpLib\Connection\AMQPSSLConnection');
//        // channel and connection are both protected and without changing the source this was the only way to mock them
//        $this->setProtectedProperty('\Bschmitt\Amqp\Publisher', $this->publisherMock, 'channel', $this->channelMock);
//        $this->setProtectedProperty('\Bschmitt\Amqp\Publisher', $this->publisherMock, 'connection', $this->connectionMock);

    }
}