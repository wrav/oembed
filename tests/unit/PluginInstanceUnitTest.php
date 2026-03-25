<?php


namespace Oembed\tests;

use Craft;
use craft\test\TestCase;
use UnitTester;
use wrav\oembed\Oembed;

class PluginInstanceUnitTest extends TestCase
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;


    public function testPluginInstance()
    {
        // Get plugin instance
        $plugin = Craft::$app->getPlugins()->getPlugin('oembed');

        // Assert plugin instance
        $this->assertInstanceOf(Oembed::class, $plugin);
    }

}
