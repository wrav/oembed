<?php


namespace Oembed\tests\embeds;

use UnitTester;

class YoutubeTest extends EmbedTestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testYoutubeEmbedWithUrl()
    {
        // Test video URL
        $url = 'https://www.youtube.com/watch?v=9bZkp7q19f0';
        $options = [
            'width' => 560,
            'height' => 315,
            'autoplay' => 1,
            'loop' => 1,
        ];

        $service = $this->createServiceMock([
            'enableCache' => false,
            'enableGdpr' => true,
        ]);

        $adapter = $this->createEmbedAdapter('<iframe src="https://www.youtube.com/embed/9bZkp7q19f0" width="480" height="270"></iframe>');

        $service->expects($this->once())
            ->method('createEmbed')
            ->with(
                $this->equalTo($url),
                $this->callback(function ($passedOptions) use ($options) {
                    return $passedOptions['width'] === $options['width']
                        && $passedOptions['height'] === $options['height']
                        && $passedOptions['autoplay'] === $options['autoplay']
                        && $passedOptions['loop'] === $options['loop'];
                }),
                $this->isType('array')
            )
            ->willReturn($adapter);

        $render = (string)$service->render($url, $options);

        // Assert that the render contains the iframe parts with GDPR-safe domain and playlist parameter
        $this->assertStringContainsString('<iframe', $render);
        $this->assertStringContainsString('src="https://www.youtube-nocookie.com/embed/9bZkp7q19f0', $render);
        $this->assertStringContainsString('width="560"', $render);
        $this->assertStringContainsString('height="315"', $render);
        $this->assertStringContainsString('autoplay=1', $render);
        $this->assertStringContainsString('loop=1', $render);
        $this->assertStringContainsString('playlist=9bZkp7q19f0', $render);
    }

}
