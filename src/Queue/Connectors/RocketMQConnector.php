<?php

namespace Freyo\LaravelQueueRocketMQ\Queue\Connectors;

use Freyo\LaravelQueueRocketMQ\Queue\RocketMQQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use MQ\MQClient;

class RocketMQConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @throws \ReflectionException
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $client = new MQClient(
            $config['endpoint'], $config['access_id'], $config['access_key']
        );

        return new RocketMQQueue($client, $config);
    }
}
