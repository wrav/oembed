<?php


namespace Oembed\tests\embeds;

use UnitTester;

class InstagramTest extends EmbedTestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testInstagramEmbedWithUrl()
    {
        // Test post URL
        $url = 'https://www.instagram.com/reel/DDP7yUypbZs/';
        $options = [
            'params' => [
                'hidecaption' => true,
            ],
        ];

        $service = $this->createServiceMock([
            'enableCache' => false,
            'facebookKey' => 'abc123|secret',
        ]);

        $adapter = $this->createEmbedAdapter('<iframe src="https://www.instagram.com/reel/DDP7yUypbZs/embed" width="400" height="300"></iframe>');

        $service->expects($this->once())
            ->method('createEmbed')
            ->with(
                $this->equalTo($url),
                $this->callback(function ($passedOptions) use ($options) {
                    return isset($passedOptions['facebook']['key'])
                        && $passedOptions['facebook']['key'] === 'abc123|secret'
                        && isset($passedOptions['params']['hidecaption'])
                        && $passedOptions['params']['hidecaption'] === $options['params']['hidecaption'];
                }),
                $this->isType('array')
            )
            ->willReturn($adapter);

        $render = (string)$service->render($url, $options);

        // Assert that the render contains the iframe parts and preserves parameters
        $this->assertStringContainsString('<iframe', $render);
        $this->assertStringContainsString('instagram.com/reel/DDP7yUypbZs', $render);
        $this->assertStringContainsString('hidecaption=1', $render);
    }

}
