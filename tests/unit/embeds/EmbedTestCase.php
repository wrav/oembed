<?php

namespace Oembed\tests\embeds;

use craft\test\TestCase;
use wrav\oembed\adapters\EmbedAdapter;
use wrav\oembed\models\Settings;
use wrav\oembed\services\OembedService;

abstract class EmbedTestCase extends TestCase
{
    protected function createServiceMock(array $settingsOverrides = []): OembedService
    {
        $service = $this->getMockBuilder(OembedService::class)
            ->onlyMethods(['createEmbed'])
            ->getMock();

        $cache = $this->createMock(\craft\cache\FileCache::class);
        $service->setCacheService($cache);

        $plugin = $this->createMock(\craft\base\Plugin::class);
        $plugin->method('getVersion')->willReturn('test-version');

        $pluginService = $this->createMock(\craft\services\Plugins::class);
        $pluginService->method('getPlugin')->with('oembed')->willReturn($plugin);
        $service->setPluginService($pluginService);

        $settings = new Settings();
        foreach ($settingsOverrides as $key => $value) {
            $settings->$key = $value;
        }
        $service->setSettings($settings);

        $service->setEventDispatcher($this->createMock(\wrav\oembed\Oembed::class));

        return $service;
    }

    protected function createEmbedAdapter(string $html, array $extra = []): EmbedAdapter
    {
        return new EmbedAdapter(array_merge(['html' => $html], $extra));
    }
}
