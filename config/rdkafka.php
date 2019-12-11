<?php

return [
    'driver' => 'rdkafka',
    'topic' => env('KAFKA_EVENT_TRACKING_TOPIC', 'default'),
    [
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