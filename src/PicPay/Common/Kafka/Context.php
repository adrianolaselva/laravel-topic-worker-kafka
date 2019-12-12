<?php

namespace PicPay\Common\Kafka;

use Enqueue\RdKafka\RdKafkaConsumer;
use Enqueue\RdKafka\RdKafkaContext;
use Illuminate\Config\Repository;
use Interop\Queue\Consumer;


abstract class Context
{

    const REPOSITORY_KEY = 'rdkafka';

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * Context constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->extractProperties($config);
    }

    /**
     * @param Repository $config
     */
    protected function extractProperties(Repository $config)
    {
        if ($config->has(self::REPOSITORY_KEY)) {
            $data             = $config->get(self::REPOSITORY_KEY);
            $this->properties = $data['properties'][$data['use']];
        }
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function mergeProperties(array $properties)
    {
        $this->properties = array_merge($this->properties, $properties);
        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getProperty($key)
    {
        return array_key_exists($key, $this->properties) ? $this->properties[$key] : null;
    }

    /**
     * @param string $topicName
     * @return RdKafkaConsumer
     */
    abstract public function getConsumer(string $topicName): RdKafkaConsumer;

    /**
     * @return RdKafkaContext
     */
    abstract public function getContext(): RdKafkaContext;

    /**
     * @return string
     */
    abstract public function getQueue(string $queue): string;
}
