<?php


namespace Tests\PicPay;

use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PicPay\Common\Queue\RdKafkaQueue;
use PicPay\Common\Queue\RdKafkaServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            RdKafkaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('queue.default', 'rdkafka');
        $app['config']->set('queue.connections.rdkafka', [
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
    }
    protected function connection(): RdKafkaQueue
    {
        return Queue::connection();
    }
}