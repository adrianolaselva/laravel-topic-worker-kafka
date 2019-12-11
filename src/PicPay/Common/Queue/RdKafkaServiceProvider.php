<?php

namespace PicPay\Common\Queue;

use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use PicPay\Common\Queue\Connectors\RdKafkaConnector;

/**
 * Class QueueServiceProvider
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