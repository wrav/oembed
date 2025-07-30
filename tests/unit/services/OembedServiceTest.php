<?php

namespace wrav\oembed\tests\unit\services;

use Codeception\Test\Unit;
use wrav\oembed\services\OembedService;
use wrav\oembed\adapters\EmbedAdapter;
use wrav\oembed\adapters\FallbackAdapter;

/**
 * Example unit tests for the refactored OembedService
 * Demonstrates how dependency injection makes the service testable
 */
class OembedServiceTest extends Unit
{
    private $service;
    private $mockCache;
    private $mockPlugin;
    private $mockSettings;
    private $mockEventDispatcher;

    protected function _before()
    {
        $this->service = new OembedService();
        
        // Create mock dependencies
        $this->mockCache = $this->createMock(\craft\cache\FileCache::class);
        $this->mockPlugin = $this->createMock(\craft\services\Plugins::class);
        $this->mockSettings = $this->createMock(\wrav\oembed\models\Settings::class);
        $this->mockEventDispatcher = $this->createMock(\wrav\oembed\Oembed::class);
        
        // Inject dependencies
        $this->service->setCacheService($this->mockCache);
        $this->service->setPluginService($this->mockPlugin);
        $this->service->setSettings($this->mockSettings);
        $this->service->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testGenerateCacheKey()
    {
        $mockPlugin = $this->createMock(\craft\base\Plugin::class);
        $mockPlugin->method('getVersion')->willReturn('3.1.5');
        
        $this->mockPlugin->method('getPlugin')->with('oembed')->willReturn($mockPlugin);
        
        $url = 'https://www.youtube.com/watch?v=123';
        $options = ['autoplay' => 1];
        $cacheProps = ['title'];
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('generateCacheKey');
        $method->setAccessible(true);
        
        $cacheKey = $method->invokeArgs($this->service, [$url, $options, $cacheProps]);
        
        $this->assertStringContainsString($url, $cacheKey);
        $this->assertStringContainsString('3.1.5', $cacheKey);
    }

    public function testPrepareOptionsWithFacebookKey()
    {
        $this->mockSettings->facebookKey = 'test-fb-key';
        
        $options = ['autoplay' => 1];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('prepareOptions');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($this->service, [$options]);
        
        $this->assertEquals('test-fb-key', $result['facebook']['key']);
        $this->assertEquals(1, $result['autoplay']);
    }

    public function testPrepareOptionsWithoutFacebookKey()
    {
        $this->mockSettings->facebookKey = null;
        
        $options = ['autoplay' => 1];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('prepareOptions');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($this->service, [$options]);
        
        $this->assertArrayNotHasKey('facebook', $result);
        $this->assertEquals(1, $result['autoplay']);
    }

    public function testManageGdprDisabled()
    {
        $this->mockSettings->enableGdpr = false;
        
        $url = 'https://www.youtube.com/watch?v=123';
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('manageGDPR');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($this->service, [$url]);
        
        $this->assertEquals($url, $result);
    }

    public function testManageGdprYouTubeUrl()
    {
        $this->mockSettings->enableGdpr = true;
        
        $url = 'https://www.youtube.com/watch?v=123';
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('manageGDPR');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($this->service, [$url]);
        
        $this->assertStringContainsString('youtube-nocookie.com', $result);
    }

    public function testManageGdprVimeoUrl()
    {
        $this->mockSettings->enableGdpr = true;
        
        $url = 'https://player.vimeo.com/video/123';
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('manageGDPR');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($this->service, [$url]);
        
        $this->assertStringContainsString('dnt=1', $result);
    }

    public function testProcessIframeSrcWithOptions()
    {
        $this->mockSettings->enableGdpr = false;
        
        $src = 'https://www.youtube.com/embed/123';
        $options = ['autoplay' => 1, 'loop' => 1];
        $url = 'https://www.youtube.com/watch?v=123';
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('processIframeSrc');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($this->service, [$src, $options, $url]);
        
        $this->assertStringContainsString('autoplay=1', $result);
        $this->assertStringContainsString('loop=1', $result);
    }

    public function testRenderReturnsFallbackForFailedEmbed()
    {
        $this->mockSettings->enableCache = false;
        
        // Mock plugin to return version
        $mockPlugin = $this->createMock(\craft\base\Plugin::class);
        $mockPlugin->method('getVersion')->willReturn('3.1.5');
        $this->mockPlugin->method('getPlugin')->with('oembed')->willReturn($mockPlugin);
        
        // Mock createEmbed to return null (simulating embed failure)
        $service = $this->getMockBuilder(OembedService::class)
            ->onlyMethods(['createEmbed'])
            ->getMock();
        
        $service->setCacheService($this->mockCache);
        $service->setPluginService($this->mockPlugin);
        $service->setSettings($this->mockSettings);
        $service->setEventDispatcher($this->mockEventDispatcher);
        
        $service->method('createEmbed')->willReturn(null);
        
        $result = $service->render('invalid-url');
        
        // Should return fallback iframe, not null
        $this->assertNotNull($result);
        $this->assertStringContainsString('iframe', (string)$result);
        $this->assertStringContainsString('invalid-url', (string)$result);
    }

    public function testRenderHandlesEmptyUrl()
    {
        $this->mockSettings->enableCache = false;
        
        // Mock plugin to return version
        $mockPlugin = $this->createMock(\craft\base\Plugin::class);
        $mockPlugin->method('getVersion')->willReturn('3.1.5');
        $this->mockPlugin->method('getPlugin')->with('oembed')->willReturn($mockPlugin);
        
        $result = $this->service->render('');
        
        // For empty URL, the service should return something (likely fallback)
        // Let's just verify it doesn't crash and returns a Template result
        $this->assertInstanceOf(\Twig\Markup::class, $result);
    }
}