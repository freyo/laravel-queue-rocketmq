<?php

/**
 * This is an example of queue connection configuration.
 * It will be merged into config/queue.php.
 * You need to set proper values in `.env`.
 */
return [

    'driver' => 'rocketmq',

    'access_key' => env('ROCKETMQ_ACCESS_KEY', 'your-access-key'),
    'access_id' => env('ROCKETMQ_ACCESS_ID', 'your-access-id'),

    'endpoint' => env('ROCKETMQ_ENDPOINT'),
    'instance_id' => env('ROCKETMQ_INSTANCE_ID'),
    'group_id' => env('ROCKETMQ_GROUP_ID'),

    'queue' => env('ROCKETMQ_QUEUE', 'default'),

    'use_message_tag' => env('ROCKETMQ_USE_MESSAGE_TAG', false),
    'wait_seconds' => env('ROCKETMQ_WAIT_SECONDS', 0),

    'plain' => [
        'enable' => env('ROCKETMQ_PLAIN_ENABLE', false),
        'job' => env('ROCKETMQ_PLAIN_JOB', 'App\Jobs\RocketMQPlainJobHandler@handle'),
    ],

];
