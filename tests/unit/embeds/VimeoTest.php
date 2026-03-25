<?php


namespace Oembed\tests\embeds;

use UnitTester;

class VimeoTest extends EmbedTestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testVimeoEmbedWithUrl()
    {
        // Test video URL
        $url = 'https://www.vimeo.com/76979871';

        $options = [
            'width' => 560,
            'height' => 315,
            'autoplay' => 1,
        ];

        $service = $this->createServiceMock([
            'enableCache' => false,
            'enableGdpr' => true,
        ]);

        $adapter = $this->createEmbedAdapter('<iframe src="https://player.vimeo.com/video/76979871" width="400" height="300"></iframe>');

        $service->expects($this->once())
            ->method('createEmbed')
            ->with(
                $this->equalTo($url),
                $this->callback(function ($passedOptions) use ($options) {
                    return $passedOptions['autoplay'] === $options['autoplay'];
                }),
                $this->isType('array')
            )
            ->willReturn($adapter);

        $render = (string)$service->render($url, $options);

        // Assert that the render contains the iframe with DNT applied and dimensions overridden
        $this->assertStringContainsString('<iframe', $render);
        $this->assertStringContainsString('src="https://player.vimeo.com/video/76979871?', $render);
        $this->assertStringContainsString('dnt=1', $render);
        $this->assertStringContainsString('autoplay=1', $render);
        $this->assertStringContainsString('width="560"', $render);
        $this->assertStringContainsString('height="315"', $render);
    }

}
