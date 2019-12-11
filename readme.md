
# Implementação de listener para apache kafka utilizando o estrutura de jobs do laravel

### Instalação

```sh
composer require adrianolaselva/laravel-topic-worker-kafka
```

### Adicionar ao arquivo `./config/queue.php` aa seguinte configuração

```php
[
    'connections' => [
        'rdkafka' => [
            'driver' => 'rdkafka',
            'topic' => env('KAFKA_EVENT_TRACKING_TOPIC'),
            'group.id' => env('KAFKA_GROUP_ID'),
            'metadata.broker.list' => env('KAFKA_METADATA_BROKER_LIST'),
            'enable.auto.commit' => env('KAFKA_ENABLE_AUTO_COMMIT'),
            'auto.offset.reset' => env('KAFKA_AUTO_OFFSET_RESET'),
        ]
    ]
]
```

Obs: Por sem uma implementação exclusiva para uso com apache kafka não há necessidade de expecíficar a variável de ambiente `QUEUE_CONNECTION` como rdkafka, assim podendo trabalhar de froma híbrida, ou seja, utilizar o kafka onde faz sentido o uso, não perdendo a praticidade da implementação nativa do framework.


### Variáveis de ambiente

```dotenv
KAFKA_GROUP_ID=event-tracking-command
KAFKA_METADATA_BROKER_LIST=127.0.0.1:9092
KAFKA_ENABLE_AUTO_COMMIT=false
KAFKA_AUTO_OFFSET_RESET=earliest
KAFKA_EVENT_TRACKING_TOPIC=event-tracking-topic
```

### Configurações no laravel

Registrar service provider no arquivo `config/app.php`

```php
[
    'providers' => [
        PicPay\Common\Queue\RdKafkaServiceProvider::class
    ]
]
```

### Configurações no lumen

Registrar service provider no arquivo `bootstrap/app.php`

```php
$app->register(PicPay\Common\Queue\RdKafkaServiceProvider::class);
```

