<?php


namespace Oembed\tests\embeds;

use UnitTester;

class TwitterTest extends EmbedTestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testXDotComEmbedWithUrl()
    {
        // Test video URL
        $url = 'https://x.com/pathfinderSport/status/1234567890?mx=2';

        $service = $this->createServiceMock([
            'enableCache' => false,
        ]);

        $adapter = $this->createEmbedAdapter('<blockquote class="twitter-tweet"><p>Some tweet</p><a href="https://twitter.com/pathfinderSport/status/1234567890">link</a></blockquote>');

        $service->expects($this->once())
            ->method('createEmbed')
            ->with(
                $this->equalTo($url),
                $this->isType('array'),
                $this->isType('array')
            )
            ->willReturn($adapter);

        $render = (string)$service->render($url);

        // Assert that the render contains the blockquote twitter embed
        $this->assertStringContainsString('<blockquote class="twitter-tweet"', $render);
        $this->assertStringContainsString('https://twitter.com/pathfinderSport/status/1234567890', $render);
    }

}
