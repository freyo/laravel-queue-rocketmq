<?php

namespace Freyo\LaravelQueueRocketMQ\Tests;

use Freyo\LaravelQueueRocketMQ\Queue\Connectors\RocketMQConnector;
use Freyo\LaravelQueueRocketMQ\Queue\Jobs\RocketMQJob;
use Freyo\LaravelQueueRocketMQ\Queue\RocketMQQueue;
use Illuminate\Container\Container;
use MQ\Model\Message;
use MQ\Model\TopicMessage;
use MQ\MQConsumer;
use MQ\MQProducer;
use PHPUnit\Framework\TestCase;

class RocketMQQueueTest extends TestCase
{
    public function provider()
    {
        $config = [

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
                'job' => env('ROCKETMQ_PLAIN_JOB', 'App\Jobs\CMQPlainJob@handle'),
            ],

        ];

        $connector = new RocketMQConnector();

        $queue = $connector->connect($config);

        return [
            [$queue, $config],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testIsPlain(RocketMQQueue $queue, $config)
    {
        $this->assertSame($config['plain']['enable'], $queue->isPlain());
    }

    /**
     * @dataProvider provider
     */
    public function testGetPlainJob(RocketMQQueue $queue, $config)
    {
        $this->assertSame($config['plain']['job'], $queue->getPlainJob());
    }

    /**
     * @dataProvider provider
     */
    public function testSize(RocketMQQueue $queue, $config)
    {
        $this->assertGreaterThanOrEqual(1, $queue->size());
    }

    /**
     * @dataProvider provider
     */
    public function testPush(RocketMQQueue $queue, $config)
    {
        $queue = \Mockery::mock(RocketMQQueue::class)
            ->shouldAllowMockingProtectedMethods();

        $queue->expects()
            ->push('App\Jobs\RocketMQJob@handle')
            ->andReturn(new TopicMessage('MockMessageBody'));

        $this->assertInstanceOf(
            TopicMessage::class, $queue->push('App\Jobs\RocketMQJob@handle')
        );
    }

    /**
     * @dataProvider provider
     */
    public function testPushRaw(RocketMQQueue $queue, $config)
    {
        $queue = \Mockery::mock(RocketMQQueue::class)
            ->shouldAllowMockingProtectedMethods();

        $queue->expects()
            ->pushRaw('App\Jobs\RocketMQJob@handle')
            ->andReturn(new TopicMessage('MockMessageBody'));

        $this->assertInstanceOf(
            TopicMessage::class, $queue->pushRaw('App\Jobs\RocketMQJob@handle')
        );
    }

    /**
     * @dataProvider provider
     */
    public function testLater(RocketMQQueue $queue, $config)
    {
        $queue = \Mockery::mock(RocketMQQueue::class)
            ->shouldAllowMockingProtectedMethods();

        $queue->expects()
            ->later(0, 'App\Jobs\RocketMQJob@handle')
            ->andReturn(new TopicMessage('MockMessageBody'));

        $this->assertInstanceOf(
            TopicMessage::class, $queue->later(0, 'App\Jobs\RocketMQJob@handle')
        );
    }

    /**
     * @dataProvider provider
     */
    public function testPop(RocketMQQueue $queue, $config)
    {
        $client = \Mockery::mock(RocketMQQueue::class)
            ->shouldAllowMockingProtectedMethods();

        $client->expects()
            ->pop()
            ->andReturn(
                \Mockery::mock(RocketMQJob::class)
            );

        $this->assertInstanceOf(RocketMQJob::class, $client->pop());
    }

    /**
     * @dataProvider provider
     */
    public function testGetConsumer(RocketMQQueue $queue, $config)
    {
        $this->assertInstanceOf(MQConsumer::class, $queue->getConsumer());
    }

    /**
     * @dataProvider provider
     */
    public function testGetProducer(RocketMQQueue $queue, $config)
    {
        $this->assertInstanceOf(MQProducer::class, $queue->getProducer());
    }
}
