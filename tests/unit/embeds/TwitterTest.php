<?php


namespace Oembed\tests\embeds;

use craft\test\TestCase;
use UnitTester;
use wrav\oembed\Oembed;
use wrav\oembed\services\OembedService;

class TwitterTest extends TestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testXDotComEmbedWithUrl()
    {
        // Test video URL
        $url = 'https://x.com/pathfinderSport/status/1234567890?mx=2';

        $render = (new OembedService())->render($url);

        // Assert that the render contains the iframe parts
        $this->assertStringContainsString('<blockquote class="twitter-tweet"', $render);
        $this->assertStringContainsString('https://twitter.com/pathfinderSport/status/1234567890', $render);
    }

}
