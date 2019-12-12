<?php


namespace Tests\PicPay;

use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PicPay\Common\Kafka\RdKafkaServiceProvider;

class TestCase extends BaseTestCase
{
    const REPOSITORY_KEY = 'rdkafka';

    protected $configRepository;
    protected $defaultConfig;

    protected function getPackageProviders($app): array
    {
        return [
            RdKafkaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $config = include dirname(__FILE__).'/../../config/rdkafka.php';
        $this->defaultConfig = $config['parameters'];
        $config = \Mockery::mock('\Illuminate\Config\Repository');
        $config->shouldReceive('has')->with(self::REPOSITORY_KEY)->andReturn(true);
        $config->shouldReceive('get')->with(self::REPOSITORY_KEY)->andReturn($config);
        $this->configRepository = $config;

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

    protected function setProtectedProperty($class, $mock, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($class);
        $channelProperty = $reflectionClass->getProperty($propertyName);
        $channelProperty->setAccessible(true);
        $channelProperty->setValue($mock, $value);
        $channelProperty->setAccessible(false);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }


}