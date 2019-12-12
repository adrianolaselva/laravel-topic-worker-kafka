<?php


namespace Tests\PicPay\Feacture;


use Illuminate\Queue\QueueManager;
use PicPay\Common\Queue\RdKafkaQueue;
use Tests\PicPay\TestCase;

class ConnectorTestOld extends TestCase
{
    public function testLazyConnection(): void
    {
        $this->app['config']->set('queue.connections.rdkafka', [
            'driver' => 'rdkafka',
            'topic' => env('KAFKA_DEFAULT_TOPIC', 'default'),
            'queue' => env('KAFKA_DEFAULT_TOPIC', 'default'),
            'worker' => env('KAFKA_WORKER', 'default'),
            'parameters' => [
                'global' => [
                    'group.id' => env('KAFKA_GROUP_ID', 'default'),
                    'metadata.broker.list' => env('KAFKA_METADATA_BROKER_LIST', 'localhost:9092'),
                    'enable.auto.commit' => env('KAFKA_ENABLE_AUTO_COMMIT') ? 'true' : 'false',
                ],
                'topic' => [
                    'auto.offset.reset' => env('KAFKA_AUTO_OFFSET_RESET', 'earliest'),
                ],
            ]
        ]);
        /** @var QueueManager $queue */
        $queue = $this->app['queue'];
        /** @var RdKafkaQueue $connection */
        $connection = $queue->connection('rdkafka');
        $this->assertInstanceOf(RdKafkaQueue::class, $connection);
        $this->assertEquals('rdkafka', $connection->getConnectionName());
        $this->assertTrue($queue->connected());
    }
}