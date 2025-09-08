<?php


namespace Oembed\tests\embeds;

use craft\test\TestCase;
use UnitTester;
use wrav\oembed\Oembed;
use wrav\oembed\services\OembedService;

class YoutubeTest extends TestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testYoutubeEmbedWithUrl()
    {
        // Test video URL
        $url = 'https://www.youtube.com/watch?v=9bZkp7q19f0';

        $render = (new OembedService())->render($url, [
            'width' => 560,
            'height' => 315,
            'autoplay' => 1,
        ]);

        // Assert that the render contains the iframe parts
        $this->assertStringContainsString('<iframe', $render);
        $this->assertStringContainsString('src="https://www.youtube.com/embed/9bZkp7q19f0', $render);
        $this->assertStringContainsString('width="560"', $render);
        $this->assertStringContainsString('height="315"', $render);
        $this->assertStringContainsString('autoplay=1', $render);
    }

}
