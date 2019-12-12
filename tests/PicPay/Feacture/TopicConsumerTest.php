<?php

namespace Tests\PicPay\Feacture;

use Tests\PicPay\TestCase;

class TopicConsumerTest extends TestCase
{
    public function testConnection(): void
    {
        $message = $this->connection()->pop('default');

        $event = json_decode($message->getRawBody(), true);
        $data = json_decode($event['data'], true);

        $this->assertNotEmpty($message->getRawBody());
        $this->assertEquals('job:test', $event['displayName']);
        $this->assertEquals('job:test', $event['job']);
        $this->assertNull($event['maxTries']);
        $this->assertNull($event['delay']);
        $this->assertNull($event['timeout']);
        $this->assertNotNull($event['data']);
        $this->assertEquals('teste', $data['message']);
    }
}