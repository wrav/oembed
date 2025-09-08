<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\console\controllers;

use craft\console\Controller;
use craft\helpers\Console;
use wrav\oembed\Oembed;
use yii\console\ExitCode;

/**
 * Cookie cleanup commands for oEmbed plugin
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     3.1.6
 */
class CookieController extends Controller
{
    /**
     * Clean up old cookie files created by the embed library
     *
     * @return int
     */
    public function actionCleanup(): int
    {
        $this->stdout("Starting cookie file cleanup...\n", Console::FG_CYAN);

        try {
            $deletedCount = Oembed::getInstance()->oembedService->cleanupCookieFiles();
            
            if ($deletedCount > 0) {
                $this->stdout("✓ Cleanup completed: {$deletedCount} cookie files removed\n", Console::FG_GREEN);
            } else {
                $this->stdout("✓ No old cookie files found to remove\n", Console::FG_GREEN);
            }

            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("✗ Cleanup failed: " . $e->getMessage() . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Show cookie cleanup information and statistics
     *
     * @return int
     */
    public function actionInfo(): int
    {
        $settings = Oembed::getInstance()->getSettings();
        
        $this->stdout("oEmbed Cookie Cleanup Information\n", Console::FG_CYAN);
        $this->stdout("================================\n\n", Console::FG_CYAN);
        
        $this->stdout("Cleanup Enabled: ", Console::FG_YELLOW);
        $this->stdout($settings->enableCookieCleanup ? "Yes\n" : "No\n");
        
        $this->stdout("Max Cookie Age: ", Console::FG_YELLOW);
        $this->stdout(gmdate('H:i:s', $settings->cookieMaxAge) . " ({$settings->cookieMaxAge} seconds)\n");
        
        $this->stdout("Custom Cookies Path: ", Console::FG_YELLOW);
        $this->stdout($settings->cookiesPath ?: "Default (temp directory)\n");
        
        // Count existing cookie files
        $dirsToCheck = [
            \Craft::$app->getPath()->getTempPath() . '/oembed-cookies',
            sys_get_temp_dir()
        ];
        
        if (!empty($settings->cookiesPath)) {
            $dirsToCheck[] = rtrim($settings->cookiesPath, '/');
        }
        
        $totalFiles = 0;
        $oldFiles = 0;
        $cutoffTime = time() - $settings->cookieMaxAge;
        
        foreach ($dirsToCheck as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/embed-cookie*.txt');
                if ($files) {
                    $totalFiles += count($files);
                    foreach ($files as $file) {
                        if (filemtime($file) < $cutoffTime) {
                            $oldFiles++;
                        }
                    }
                }
            }
        }
        
        $this->stdout("\nCurrent Cookie Files: ", Console::FG_YELLOW);
        $this->stdout("{$totalFiles} total, {$oldFiles} eligible for cleanup\n");
        
        if ($oldFiles > 0) {
            $this->stdout("\nRun 'php craft oembed/cookie/cleanup' to remove old files.\n", Console::FG_GREEN);
        }
        
        return ExitCode::OK;
    }
}