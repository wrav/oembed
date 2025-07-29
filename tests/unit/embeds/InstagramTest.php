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
        $this->assertStringContainsString('src="https://www.instagram.com/reel/DDP7yUypbZs/', $render);
    }

}
