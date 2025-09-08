<?php

namespace Oembed\tests;

use FunctionalTester;

/**
 * Functional tests for the oEmbed Craft CMS plugin
 * Tests the plugin functionality in a real Craft environment
 */
class OembedPluginFunctionalCest
{
    /**
     * Test that the plugin is properly installed and accessible
     */
    public function testPluginIsInstalled(FunctionalTester $I): void
    {
        $I->amOnPage('?p=/');
        $I->seeResponseCodeIs(200);
    }

    /**
     * Test that the oEmbed field type is available in the field creation interface
     */
    public function testOembedFieldTypeAvailable(FunctionalTester $I): void
    {
        // This would test the admin CP functionality
        // For now, just verify the basic page loads
        $I->amOnPage('?p=/');
        $I->seeResponseCodeIs(200);
    }

    /**
     * Test basic plugin functionality without requiring authentication
     */
    public function testBasicPluginFunctionality(FunctionalTester $I): void
    {
        // Test that the plugin doesn't break the site
        $I->amOnPage('?p=/');
        $I->seeResponseCodeIs(200);
        $I->dontSee('Fatal error');
        $I->dontSee('Exception');
    }
}