<?php

namespace PicPay\PicPay\Common\Queue;

use Illuminate\Queue\QueueServiceProvider as DefaultQueueServiceProvider;
use PicPay\PicPay\Common\Queue\Connectors\RdKafkaConnector;

/**
 * Class QueueServiceProvider
 * @package PicPay\PicPay\Common\Queue
 */
class QueueServiceProvider extends DefaultQueueServiceProvider
{
    /**
     * Register the connectors on the queue manager.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     */
    public function registerConnectors($manager)
    {
        foreach (['Null', 'Sync', 'Database', 'Redis', 'Beanstalkd', 'Sqs', 'RdKafka'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }

    /**
     * Register the Amazon RdKafka topic connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerRdKafkaConnector($manager)
    {
        $manager->addConnector('rdkafka', function () {
            return new RdKafkaConnector();
        });
    }
}