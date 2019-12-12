<?php

namespace PicPay\Common\Queue;

use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use PicPay\Common\Queue\Connectors\RdKafkaConnector;

/**
 * Class RdKafkaServiceProvider
 * @package PicPay\Common\Queue
 */
class RdKafkaServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../../config/rdkafka.php',
            'queue.connections.rdkafka'
        );
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\ListenTopicCommand::class
            ]);
        }

        $this->app->singleton(RdKafkaConnector::class, function ($app) {
            return new RdKafkaConnector(
                $app['events']
            );
        });
    }

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot(): void
    {
        /** @var QueueManager $queue */
        $queue = $this->app['queue'];

        $queue->addConnector('rdkafka', function () {
            return new RdKafkaConnector(
                $this->app['events']
            );
        });
    }

}