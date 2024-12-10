<?php


namespace Oembed\tests\embeds;

use Codeception\Attribute\Env;
use craft\test\TestCase;
use UnitTester;
use wrav\oembed\Oembed;
use wrav\oembed\services\OembedService;

class InstagramTest extends TestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testInstagramEmbedWithUrl()
    {
        // Test post URL
        $url = 'https://www.instagram.com/reel/DDP7yUypbZs/';

        $render = (new OembedService())->render($url, [
            'facebook:token' => getenv('FACEBOOK_API_KEY'),
            'instagram:token' => getenv('FACEBOOK_API_KEY'),
            'instagram' => [
                'key' => getenv('FACEBOOK_API_KEY'),
            ],
            'facebook' => [
                'key' => getenv('FACEBOOK_API_KEY'),
            ],
        ]);

        // Assert that the render contains the iframe parts
        $this->assertStringContainsString('<iframe', $render);
        $this->assertStringContainsString('src="https://www.youtube.com/embed/9bZkp7q19f0', $render);
        $this->assertStringContainsString('width="560"', $render);
        $this->assertStringContainsString('height="315"', $render);
        $this->assertStringContainsString('autoplay=1', $render);
    }

}
