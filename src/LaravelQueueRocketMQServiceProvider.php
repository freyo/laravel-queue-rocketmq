<?php

namespace Freyo\LaravelQueueRocketMQ;

use Freyo\LaravelQueueRocketMQ\Queue\Connectors\RocketMQConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class LaravelQueueRocketMQServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app instanceof LumenApplication) {
            $this->app->configure('queue');
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/rocketmq.php', 'queue.connections.rocketmq'
        );
    }

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        /** @var QueueManager $queue */
        $queue = $this->app['queue'];

        $queue->addConnector('rocketmq', function () {
            return new RocketMQConnector();
        });
    }
}
