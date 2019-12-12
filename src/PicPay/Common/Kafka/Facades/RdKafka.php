<?php

namespace PicPay\Common\Kafka\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class RdKafka
 * @package PicPay\Common\Kafka\Facades
 */
class RdKafka extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'RdKafka';
    }
}
