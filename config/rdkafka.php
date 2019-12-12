<?php

return [
    'driver' => 'rdkafka',
    'topic' => env('KAFKA_DEFAULT_TOPIC', 'default'),
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
];