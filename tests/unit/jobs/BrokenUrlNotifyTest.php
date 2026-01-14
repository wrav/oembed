<?php

namespace wrav\oembed\tests\unit\jobs;

use Codeception\Test\Unit;
use wrav\oembed\jobs\BrokenUrlNotify;
use wrav\oembed\Oembed;
use craft\test\TestCase;

/**
 * Unit test for BrokenUrlNotify job
 */
class BrokenUrlNotifyTest extends TestCase
{
    public function testJobHandlesEmptyUrl()
    {
        $job = new BrokenUrlNotify();
        $job->url = '';

        // Mock the queue to avoid actual execution
        $mockQueue = $this->createMock(\craft\queue\Queue::class);

        // Should not throw exception with empty URL
        $job->execute($mockQueue);
        
        // If we get here, the job handled empty URL gracefully
        $this->assertTrue(true);
    }

    public function testJobHandlesWhitespaceUrl()
    {
        $job = new BrokenUrlNotify();
        $job->url = '   ';

        // Mock the queue to avoid actual execution
        $mockQueue = $this->createMock(\craft\queue\Queue::class);

        // Should not throw exception with whitespace URL
        $job->execute($mockQueue);
        
        // If we get here, the job handled whitespace URL gracefully
        $this->assertTrue(true);
    }

    public function testJobHandlesNullUrl()
    {
        $job = new BrokenUrlNotify();
        $job->url = null;

        // Mock the queue to avoid actual execution
        $mockQueue = $this->createMock(\craft\queue\Queue::class);

        // Should not throw exception with null URL
        $job->execute($mockQueue);
        
        // If we get here, the job handled null URL gracefully
        $this->assertTrue(true);
    }
}