<div>
  <p align="center">
    <image src="https://img.alicdn.com/tfs/TB1DThKRXXXXXa.XpXXXXXXXXXX-200-200.png" width="150" height="150">
  </p>
  <p align="center">AlibabaMQ(Apache RocketMQ) Driver for Laravel Queue</p>
  <p align="center">
    <a href="LICENSE">
      <image src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License">
    </a>
    <a href="https://travis-ci.org/freyo/laravel-queue-rocketmq">
      <image src="https://img.shields.io/travis/freyo/laravel-queue-rocketmq/master.svg?style=flat-square" alt="Build Status">
    </a>
    <a href="https://scrutinizer-ci.com/g/freyo/laravel-queue-rocketmq">
      <image src="https://img.shields.io/scrutinizer/coverage/g/freyo/laravel-queue-rocketmq.svg?style=flat-square" alt="Coverage Status">
    </a>
    <a href="https://scrutinizer-ci.com/g/freyo/laravel-queue-rocketmq">
      <image src="https://img.shields.io/scrutinizer/g/freyo/laravel-queue-rocketmq.svg?style=flat-square" alt="Quality Score">
    </a>
    <a href="https://packagist.org/packages/freyo/laravel-queue-rocketmq">
      <image src="https://img.shields.io/packagist/v/freyo/laravel-queue-rocketmq.svg?style=flat-square" alt="Packagist Version">
    </a>
    <a href="https://packagist.org/packages/freyo/laravel-queue-rocketmq">
      <image src="https://img.shields.io/packagist/dt/freyo/laravel-queue-rocketmq.svg?style=flat-square" alt="Total Downloads">
    </a>
  </p>
  <p align="center">
    <a href="https://app.fossa.io/projects/git%2Bgithub.com%2Ffreyo%2Flaravel-queue-rocketmq?ref=badge_small">
      <img src="https://app.fossa.io/api/projects/git%2Bgithub.com%2Ffreyo%2Flaravel-queue-rocketmq.svg?type=small" alt="FOSSA Status">
    </a>
  </p>
</div>

## Installation

  ```shell
  composer require freyo/laravel-queue-rocketmq
  ```

## Configure

**Laravel 5.5+ uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.**

1. `config/app.php`:

  ```php
  'providers' => [
    // ...
    Freyo\LaravelQueueRocketMQ\LaravelQueueRocketMQServiceProvider::class,
  ]
  ```
  
2. `.env`:

  ```
  QUEUE_DRIVER=rocketmq
  
  ROCKETMQ_ACCESS_KEY=your-access-key
  ROCKETMQ_ACCESS_ID=your-access-id
  
  ROCKETMQ_ENDPOINT=http://***.mqrest.***.aliyuncs.com
  ROCKETMQ_INSTANCE_ID=MQ_INST_***_***
  ROCKETMQ_GROUP_ID=GID_***
  
  ROCKETMQ_QUEUE=topic_name # default topic name
  
  ROCKETMQ_USE_MESSAGE_TAG=false # set to true to use message tag
  ROCKETMQ_WAIT_SECONDS=0
  ```

## Usage

Once you completed the configuration you can use Laravel Queue API. If you used other queue drivers you do not need to change anything else. If you do not know how to use Queue API, please refer to the official Laravel documentation: http://laravel.com/docs/queues

### Example

#### Dispatch Jobs

The default connection name is `rocketmq`

  ```php
  //use queue only
  Job::dispatch()->onConnection('connection-name')->onQueue('queue-name');
  // or dispatch((new Job())->onConnection('connection-name')->onQueue('queue-name'))
  
  //use topic and tag filter
  Job::dispatch()->onConnection('connection-name')->onQueue('tag1,tag2,tag3');
  // or dispatch((new Job())->onConnection('connection-name')->onQueue('tag1,tag2,tag3'))
  
  //use topic and routing filter
  Job::dispatch()->onConnection('connection-name')->onQueue('routing-key');
  // or dispatch((new Job())->onConnection('connection-name')->onQueue('routing-key'))
  ```

#### Multiple Queues

Configure `config/queue.php`

```php
'connections' => [
    //...
    'new-connection-name' => [
        'driver' => 'rocketmq',
        'access_key' => 'your-access-key',
        'access_id'  => 'your-access-id',
        'endpoint' => 'http://***.mqrest.***.aliyuncs.com',
        'instance_id' => 'MQ_INST_***_***',
        'group_id' => 'GID_***',
        'queue' => 'your-default-topic-name',
        'use_message_tag' => false,
        'wait_seconds' => 0,
        'plain' => [
            'enable' => false,
            'job' => 'App\Jobs\RocketMQPlainJobHandler@handle',
        ],
    ];
    //...
];
```

#### Process Jobs

```bash
php artisan queue:work {connection-name} --queue={queue-name}
```

#### Plain Mode

Configure `.env`

```
ROCKETMQ_PLAIN_ENABLE=true
ROCKETMQ_PLAIN_JOB=App\Jobs\RocketMQPlainJob@handle
```

Create a job implements `PlainPayload` interface. The method `getPayload` must return a sting value.

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Freyo\LaravelQueueRocketMQ\Queue\Contracts\PlainPayload;

class RocketMQPlainJob implements ShouldQueue, PlainPayload
{
    use Dispatchable, InteractsWithQueue, Queueable;
    
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    
    /**
     * Get the plain payload of the job.
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
```

Create a plain job handler

```php
<?php

namespace App\Jobs;

use Illuminate\Queue\Jobs\Job;

class RocketMQPlainJobHandler
{
    /**
     * Execute the job.
     * 
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param string $payload
     * 
     * @return void
     */
    public function handle(Job $job, $payload)
    {
        // processing your payload...
        var_dump($payload);
        
        // release back to the queue manually when failed.
        // $job->release();
        
        // delete message when processed.
        if (! $job->isDeletedOrReleased()) {
            $job->delete();
        }        
    }
}
```

## References

- [Product Documentation](https://www.alibabacloud.com/product/mq)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Ffreyo%2Flaravel-queue-rocketmq.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Ffreyo%2Flaravel-queue-rocketmq?ref=badge_large)
