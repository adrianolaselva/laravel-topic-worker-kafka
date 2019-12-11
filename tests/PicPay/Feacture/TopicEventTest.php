<?php


namespace Tests\PicPay\Feacture;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PicPay\Common\Queue\Jobs\RdKafkaJob;
use Tests\PicPay\Mocks\TestJob;
use Tests\PicPay\TestCase as BaseTestCase;

class TopicEventTest extends BaseTestCase
{
//    /**
//     * @throws \Exception
//     */
//    protected function setUp(): void
//    {
//        parent::setUp();
////        if ($this->connection()->isQueueExists()) {
////            $this->connection()->purge();
////        }
//    }
//
//    /**
//     * @throws \Exception
//     */
//    protected function tearDown(): void
//    {
//        if ($this->connection()->isQueueExists()) {
//            $this->connection()->purge();
//        }
//        $this->assertSame(0, Queue::size());
//        parent::tearDown();
//    }

    public function testSizeDoesNotThrowExceptionOnUnknownQueue(): void
    {
        $this->assertNull(Queue::size(Str::random()));
    }
//    public function testPopNothing(): void
//    {
//        $this->assertNull(Queue::pop('not_exists'));
//    }

    public function testPushRaw(): void
    {
        $topicName = Str::random();
        Queue::pushRaw($payload = Str::random(), $topicName);
        $this->assertNull(Queue::size($topicName));
        $this->assertNotNull($job = Queue::pop($topicName));
        $this->assertNull($job->attempts());
        $this->assertInstanceOf(RdKafkaJob::class, $job);
        $this->assertSame($payload, $job->getRawBody());
        $this->assertNull($job->getJobId());
        $job->delete();
    }

    public function testPush(): void
    {
        $topicName = Str::random();
        Queue::push(new TestJob(), [], $topicName);
        $this->assertNull(Queue::size($topicName));
        $this->assertNotNull($job = Queue::pop($topicName));
        $this->assertNull($job->attempts());
        $this->assertInstanceOf(RdKafkaJob::class, $job);
        $this->assertSame(TestJob::class, $job->resolveName());
        $this->assertNull($job->getJobId());
        $payload = $job->payload();
        $this->assertSame(TestJob::class, $payload['displayName']);
        $this->assertSame('Illuminate\Queue\CallQueuedHandler@call', $payload['job']);
        $this->assertNull($payload['maxTries']);
        $this->assertNull($payload['delay']);
        $this->assertNull($payload['timeout']);
        $this->assertNull($payload['timeoutAt']);
    }

}