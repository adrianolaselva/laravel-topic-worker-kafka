<?php

namespace PicPay\Common\Kafka;

use Illuminate\Support\ServiceProvider;
use PicPay\Common\Kafka\Facades\RdKafkaFacade;

/**
 * Class RdKafkaServiceProvider
 * @package PicPay\Common\Queue
 */
class RdKafkaServiceProvider extends ServiceProvider
{

//    /**
//     * Register the service provider.
//     *
//     * @return void
//     */
//    public function register(): void
//    {
//        $this->mergeConfigFrom(
//            __DIR__.'/../../../../config/rdkafka.php',
//            'queue.connections.rdkafka'
//        );
//        if ($this->app->runningInConsole()) {
//            $this->commands([
//                Console\ListenTopicCommand::class
//            ]);
//        }
//
//        $this->app->singleton(RdKafkaConnector::class, function ($app) {
//            return new RdKafkaConnector(
//                $app['events']
//            );
//        });
//    }
//
//    /**
//     * Register the application's event listeners.
//     *
//     * @return void
//     */
//    public function boot(): void
//    {
//        /** @var QueueManager $queue */
//        $queue = $this->app['queue'];
//
//        $queue->addConnector('rdkafka', function () {
//            return new RdKafkaConnector(
//                $this->app['events']
//            );
//        });
//    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('RdKafka', RdKafkaFacade::class);

        if (!class_exists('RdKafka')) {
            class_alias(RdKafkaFacade::class, 'RdKafka');
        }

        $this->publishes([
            __DIR__.'/../../../../config/rdkafka.php' => config_path('rdkafka.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(\PicPay\Common\Kafka\Publisher::class, function ($app) {
            return new \PicPay\Common\Kafka\Publisher(config());
        });
        $this->app->singleton(\PicPay\Common\Kafka\Consumer::class, function ($app) {
            return new \PicPay\Common\Kafka\Consumer(config());
        });
    }

    public function provides()
    {
        return [
            'RdKafka',
            \PicPay\Common\Kafka\Publisher::class,
            \PicPay\Common\Kafka\Consumer::class
        ];
    }

}