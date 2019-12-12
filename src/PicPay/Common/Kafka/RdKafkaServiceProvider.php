<?php

namespace PicPay\Common\Kafka;

use Illuminate\Support\ServiceProvider;
use PicPay\Common\Kafka\Facades\RdKafka;

/**
 * Class RdKafkaServiceProvider
 * @package PicPay\Common\Queue
 */
class RdKafkaServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('RdKafka', RdKafka::class);

        if (!class_exists('RdKafka')) {
            class_alias(RdKafka::class, 'RdKafka');
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