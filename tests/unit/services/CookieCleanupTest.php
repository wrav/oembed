<?php

namespace wrav\oembed\tests\unit\services;

use Codeception\Test\Unit;
use wrav\oembed\services\OembedService;
use wrav\oembed\models\Settings;
use craft\test\TestCase;

/**
 * Unit test for cookie cleanup functionality
 */
class CookieCleanupTest extends TestCase
{
    private $tempDir;
    private $service;

    protected function _before()
    {
        parent::_before();
        
        // Create a temporary directory for testing
        $this->tempDir = sys_get_temp_dir() . '/oembed-test-' . uniqid();
        mkdir($this->tempDir, 0755, true);
        
        // Create mock service with test settings
        $settings = new Settings();
        $settings->enableCookieCleanup = true;
        $settings->cookieMaxAge = 3600; // 1 hour
        $settings->cookiesPath = $this->tempDir;
        
        // Create service with mock settings using reflection
        $this->service = new OembedService();
        
        // Set settings using reflection
        $reflection = new \ReflectionClass($this->service);
        $settingsProperty = $reflection->getProperty('settings');
        $settingsProperty->setAccessible(true);
        $settingsProperty->setValue($this->service, $settings);
    }

    protected function _after()
    {
        // Clean up test directory
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->tempDir);
        }
        parent::_after();
    }

    public function testCookieCleanupRemovesOldFiles()
    {
        // Create some test cookie files
        $oldFile = $this->tempDir . '/embed-cookies-old.txt';
        $newFile = $this->tempDir . '/embed-cookies-new.txt';
        $nonCookieFile = $this->tempDir . '/other-file.txt';
        
        // Create files
        file_put_contents($oldFile, 'old cookie data');
        file_put_contents($newFile, 'new cookie data');
        file_put_contents($nonCookieFile, 'other data');
        
        // Set old timestamp for one file (2 hours ago)
        touch($oldFile, time() - 7200);
        
        // Run cleanup
        $deletedCount = $this->service->cleanupCookieFiles();
        
        // Verify results
        $this->assertEquals(1, $deletedCount, 'Should delete 1 old cookie file');
        $this->assertFileDoesNotExist($oldFile, 'Old cookie file should be deleted');
        $this->assertFileExists($newFile, 'New cookie file should remain');
        $this->assertFileExists($nonCookieFile, 'Non-cookie file should remain');
    }

    public function testCookieCleanupRespectsDisabledSetting()
    {
        // Disable cleanup
        $settings = new Settings();
        $settings->enableCookieCleanup = false;
        
        $service = new OembedService();
        
        // Set settings using reflection
        $reflection = new \ReflectionClass($service);
        $settingsProperty = $reflection->getProperty('settings');
        $settingsProperty->setAccessible(true);
        $settingsProperty->setValue($service, $settings);
        
        // Create old file
        $oldFile = $this->tempDir . '/embed-cookies-old.txt';
        file_put_contents($oldFile, 'old cookie data');
        touch($oldFile, time() - 7200);
        
        // Run cleanup
        $deletedCount = $service->cleanupCookieFiles();
        
        // Verify no cleanup occurred
        $this->assertEquals(0, $deletedCount, 'Should not delete any files when disabled');
        $this->assertFileExists($oldFile, 'File should remain when cleanup disabled');
    }

    public function testGetCookieSettingsCreatesCustomPath()
    {
        // Use reflection to access private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getCookieSettings');
        $method->setAccessible(true);
        
        $settings = $method->invoke($this->service);
        
        $this->assertArrayHasKey('cookies_path', $settings);
        $this->assertStringContainsString($this->tempDir, $settings['cookies_path']);
        $this->assertStringContainsString('embed-cookies', $settings['cookies_path']);
    }
}