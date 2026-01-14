<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\services;

use craft;
use craft\base\Component;
use craft\helpers\Template;
use DOMDocument;
use Embed\Embed;
use Exception;
use wrav\oembed\adapters\EmbedAdapter;
use wrav\oembed\adapters\FallbackAdapter;
use wrav\oembed\events\BrokenUrlEvent;
use wrav\oembed\Oembed;

/**
 * OembedService Service
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     1.0.0
 */
class OembedService extends Component
{
    private const YOUTUBE_PATTERN = '/(?:http|https)*?:*\/\/(?:www\.|)(?:youtube\.com|m\.youtube\.com|youtu\.be|youtube-nocookie\.com)/i';
    private const VIMEO_PATTERN = '/vimeo\.com/i';
    
    private const DEFAULT_CACHE_KEYS = [
        'title', 'description', 'url', 'type', 'tags', 'images', 'image',
        'imageWidth', 'imageHeight', 'code', 'width', 'height', 'aspectRatio',
        'authorName', 'authorUrl', 'providerName', 'providerUrl', 'providerIcons',
        'providerIcon', 'publishedDate', 'license', 'linkedData', 'feeds',
    ];
    
    private const SUCCESSFUL_CACHE_DURATION = 3600; // 1 hour
    private const FAILED_CACHE_DURATION = 900; // 15 minutes
    
    // Dependencies - can be injected for testing
    private $cacheService;
    private $pluginService;
    private $settings;
    private $eventDispatcher;

    /**
     * Constructor allows dependency injection for testing
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        
        // Initialize dependencies - can be overridden for testing
        $this->cacheService = $this->cacheService ?? Craft::$app->cache;
        $this->pluginService = $this->pluginService ?? Craft::$app->plugins;
        $this->settings = $this->settings ?? Oembed::getInstance()->getSettings();
        $this->eventDispatcher = $this->eventDispatcher ?? Oembed::getInstance();
    }

    /**
     * Set cache service (for testing)
     */
    public function setCacheService($cacheService): void
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Set plugin service (for testing)
     */
    public function setPluginService($pluginService): void
    {
        $this->pluginService = $pluginService;
    }

    /**
     * Set settings (for testing)
     */
    public function setSettings($settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Set event dispatcher (for testing)
     */
    public function setEventDispatcher($eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $url
     * @param array $options
     * @param array $cacheProps
     * @return string|null
     */
    public function render($url, array $options = [], array $cacheProps = [])
    {
        $media = $this->embed($url, $options, $cacheProps);

        if (empty($media)) {
            return null;
        }

        $code = $media->code ?? $media->html ?? '';
        return Template::raw($code);
    }

    /**
     * @param string $input 
     * @param array $options
     * @return string 
     */
    public function parseTags(string $input, array $options = [], array $cacheProps = [])
    {
        if (empty($input)) {
            return '';
        }
        
        $output = preg_replace_callback('/\<oembed\s(?:.*?)url="(.*?)"(?:.*?)>(?:.*?)<\/oembed>/i', function($matches) use ($options, $cacheProps) {
            $url = $matches[1];
            return $this->render($url, $options, $cacheProps);
        }, $input);

        return $output;
    }

    /**
     * Generate cache key for the embed request
     */
    protected function generateCacheKey(string $url, array $options, array $cacheProps): string
    {
        // Additional safety check (should not be needed after embed() method fix)
        $url = $url ?: '';
        
        try {
            $hash = md5(json_encode($options)) . md5(json_encode($cacheProps));
        } catch (\Exception $exception) {
            $hash = '';
        }
        
        $plugin = $this->pluginService->getPlugin('oembed');
        return $url . '_' . $plugin->getVersion() . '_' . $hash;
    }

    /**
     * Get embed data from cache if available
     */
    private function getCachedEmbed(string $cacheKey)
    {
        if ($this->settings->enableCache && $this->cacheService->exists($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }
        return null;
    }

    /**
     * Set up options with Facebook API key if available
     */
    protected function prepareOptions(array $options): array
    {
        if ($this->settings->facebookKey) {
            $options['facebook']['key'] = $this->settings->facebookKey;
        }
        return $options;
    }

    /**
     * Create embed using the Embed library
     */
    protected function createEmbed(string $url, array $options, array $factories): ?EmbedAdapter
    {
        try {
            array_multisort($options);
            
            // Create a custom crawler with cookie settings
            $curlClient = new \Embed\Http\CurlClient();
            $cookieSettings = $this->getCookieSettings();
            $curlClient->setSettings($cookieSettings);
            
            $crawler = new \Embed\Http\Crawler($curlClient);
            $embed = new Embed($crawler);
        
            // Add custom factories
            if (count($factories) > 0) {
                foreach ($factories as $factory) {
                    $embed->getExtractorFactory()->addAdapter($factory['domain'], $factory['extractor']::class);
                }
            }

            $infos = $embed->get($url ?: "");
            $infos->setSettings($options);
            $data = $infos->getOEmbed()->all();

            return new EmbedAdapter($data, $infos);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->triggerBrokenUrlEvent($url);
            return null;
        }
    }

    /**
     * Create fallback adapter when embed fails
     */
    private function createFallbackAdapter(string $url): FallbackAdapter
    {
        return new FallbackAdapter([
            'html' => $url ? '<iframe src="' . $url . '"></iframe>' : null
        ]);
    }

    /**
     * Log error message
     */
    private function logError(string $message): void
    {
        Craft::info($message, 'oembed');
    }

    /**
     * Trigger broken URL event if notifications are enabled
     */
    private function triggerBrokenUrlEvent(string $url): void
    {
        if (!$this->settings->enableNotifications) {
            return;
        }

        // Validate URL before triggering event
        if (!$url || trim($url) === '') {
            Craft::warning('triggerBrokenUrlEvent: Cannot trigger event for empty URL', 'oembed');
            return;
        }

        Craft::info('triggerBrokenUrlEvent: Triggering broken URL event for: ' . $url, 'oembed');

        $this->eventDispatcher->trigger(Oembed::EVENT_BROKEN_URL_DETECTED, new BrokenUrlEvent([
            'url' => trim($url),
        ]));
    }

    /**
     * Get cookie settings for the embed library
     */
    private function getCookieSettings(): array
    {
        $settings = [];
        
        // Set custom cookies path if configured
        if (!empty($this->settings->cookiesPath)) {
            $cookiesDir = rtrim($this->settings->cookiesPath, '/');
            if (!is_dir($cookiesDir)) {
                @mkdir($cookiesDir, 0755, true);
            }
            $settings['cookies_path'] = $cookiesDir . '/embed-cookies.txt';
        } else {
            // Use plugin-specific temp directory
            $tempDir = Craft::$app->getPath()->getTempPath() . '/oembed-cookies';
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            $settings['cookies_path'] = $tempDir . '/embed-cookies-' . uniqid() . '.txt';
        }
        
        return $settings;
    }

    /**
     * Clean up old cookie files
     */
    public function cleanupCookieFiles(): int
    {
        if (!$this->settings->enableCookieCleanup) {
            return 0;
        }

        $deletedCount = 0;
        $maxAge = $this->settings->cookieMaxAge;
        $cutoffTime = time() - $maxAge;

        // Define directories to clean
        $dirsToClean = [
            Craft::$app->getPath()->getTempPath() . '/oembed-cookies',
            sys_get_temp_dir()
        ];

        // Add custom cookies path if set
        if (!empty($this->settings->cookiesPath)) {
            $dirsToClean[] = rtrim($this->settings->cookiesPath, '/');
        }

        foreach ($dirsToClean as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            try {
                $files = glob($dir . '/embed-cookie*.txt');
                if ($files) {
                    foreach ($files as $file) {
                        if (is_file($file) && filemtime($file) < $cutoffTime) {
                            if (@unlink($file)) {
                                $deletedCount++;
                                Craft::info("Cleaned up cookie file: {$file}", 'oembed');
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Craft::warning("Failed to cleanup cookies in {$dir}: " . $e->getMessage(), 'oembed');
            }
        }

        if ($deletedCount > 0) {
            Craft::info("Cookie cleanup completed: {$deletedCount} files removed", 'oembed');
        }

        return $deletedCount;
    }

    /**
     * Process DOM and apply options to iframe
     */
    private function processDomAndApplyOptions($media, array $data, string $url, array $options)
    {
        try {
            $dom = new DOMDocument;
            $html = $this->prepareHtml($media, $data, $url);
            
            // Skip DOM processing if HTML is empty
            if (empty($html)) {
                return $media;
            }
            
            $dom->loadHTML($html);

            $iframe = $dom->getElementsByTagName('iframe')->item(0);
            if (!$iframe) {
                return $media;
            }

            $src = $iframe->getAttribute('src');
            $src = $this->processIframeSrc($src, $options, $url);
            $iframe->setAttribute('src', $src);

            $this->applyGdprAttributes($iframe, $src);
            $this->applyIframeAttributes($iframe, $options, $media);

            $mainElement = $this->getMainElement($dom);
            $code = $dom->saveXML($mainElement, LIBXML_NOEMPTYTAG);

            $media->code = $code;
            $media->url = $media->url ?: $url;

            return $media;
        } catch (\Exception $exception) {
            $this->logError($exception->getMessage());
            return $media;
        }
    }

    /**
     * Prepare HTML for DOM processing
     */
    private function prepareHtml($media, array $data, string $url): string
    {
        $html = $media->getCode() ?: null;

        if (empty($html) || empty((string)$url)) {
            return empty((string)$url) ? '' : '<iframe src="' . $url . '"></iframe>';
        }

        $html = $data['html'];
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return preg_replace('/&(?!#?[a-z0-9]+;)/i', '&amp;', $html);
    }

    /**
     * Process iframe src with options and GDPR
     */
    protected function processIframeSrc(string $src, array $options, string $url): string
    {
        $src = $this->manageGDPR($src);
        $src = $this->addQueryParameterBase($src);
        $src = $this->applyUrlParameters($src, $options);
        $src = $this->handleYouTubeLooping($src, $url);
        
        return $src;
    }

    /**
     * Add base query parameter if none exists
     */
    private function addQueryParameterBase(string $src): string
    {
        if (!preg_match('/\?(.*)$/i', $src)) {
            $src .= "?";
        }
        return $src;
    }

    /**
     * Apply URL parameters from options
     */
    private function applyUrlParameters(string $src, array $options): string
    {
        // Apply params
        if (!empty($options['params'])) {
            foreach ((array)$options['params'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $src = preg_replace('/\?(.*)$/i', '?' . $key . '=' . $value . '&${1}', $src);
            }
        }

        // Apply individual parameters
        $parameters = ['autoplay', 'loop', 'autopause', 'rel'];
        foreach ($parameters as $param) {
            if (!empty($options[$param]) && strpos($src, $param . '=') === false && $src) {
                $value = !!$options[$param] ? '1' : '0';
                $src = preg_replace('/\?(.*)$/i', '?' . $param . '=' . $value . '&${1}', $src);
            }
        }

        return $src;
    }

    /**
     * Handle YouTube looping special case
     */
    private function handleYouTubeLooping(string $src, string $url): string
    {
        preg_match(self::YOUTUBE_PATTERN, $url, $ytMatch, PREG_OFFSET_CAPTURE);

        if (count($ytMatch) && strpos($src, 'loop=1') !== false) {
            preg_match('/\/embed\/([^?]+)/', $src, $ytCode);
            if (count($ytCode)) {
                $src = preg_replace('/\?(.*)$/i', '?playlist=' . $ytCode[1] . '&${1}', $src);
            }
        }

        return $src;
    }

    /**
     * Apply GDPR attributes to iframe
     */
    private function applyGdprAttributes($iframe, string $src): void
    {
        if ($this->settings->enableGdprCookieBot) {
            $iframe->setAttribute('data-cookieblock-src', $src);
            $iframe->setAttribute('data-cookieconsent', 'marketing');
        }
    }

    /**
     * Apply iframe attributes and dimensions
     */
    private function applyIframeAttributes($iframe, array $options, $media): void
    {
        // Width/Height overrides
        if (!empty($options['width']) && is_int($options['width'])) {
            $iframe->setAttribute('width', $options['width']);
        }
        if (!empty($options['height']) && is_int($options['height'])) {
            $iframe->setAttribute('height', $options['height']);
        }

        // Apply custom attributes
        if (!empty($options['attributes'])) {
            foreach ((array)$options['attributes'] as $key => $value) {
                $iframe->setAttribute($key, $value);
                if (in_array($key, ['width', 'height'])) {
                    $media->$key = $value;
                }
            }
        }
    }

    /**
     * Get main DOM element for output
     */
    private function getMainElement(DOMDocument $dom)
    {
        $bodyItem = $dom->getElementsByTagName('body')->item(0);
        if ($bodyItem === null) {
            return $dom->getElementsByTagName('iframe')->item(0);
        }

        if ($bodyItem->childNodes->count() === 1) {
            return $bodyItem->childNodes->item(0);
        }

        // Multiple children, wrap in div
        $mainElement = $dom->createElement('div');
        $bodyChildren = [];
        foreach ($bodyItem->childNodes as $child) {
            $bodyChildren[] = $child;
        }
        foreach ($bodyChildren as $child) {
            $mainElement->appendChild($child);
        }
        $bodyItem->appendChild($mainElement);

        return $mainElement;
    }

    /**
     * Cache the embed result
     */
    private function cacheEmbed($media, string $cacheKey, array $cacheProps): void
    {
        if (!$this->settings->enableCache) {
            return;
        }

        $duration = $media instanceof FallbackAdapter ? 
            self::FAILED_CACHE_DURATION : 
            self::SUCCESSFUL_CACHE_DURATION;

        $cacheKeys = array_unique(array_merge(self::DEFAULT_CACHE_KEYS, $cacheProps));

        // Ensure all keys are set to avoid errors
        foreach ($cacheKeys as $key) {
            try {
                $media->{$key} = $media->{$key};
            } catch (\Exception $e) {
                $media->{$key} = null;
            }
        }

        $this->cacheService->set($cacheKey, json_decode(json_encode($media)), $duration);
    }

    /**
     * Main embed method - now much cleaner and testable
     * 
     * @param string $url
     * @param array $options
     * @param array $cacheProps
     * @param array $factories
     * @return mixed
     */
    public function embed($url, array $options = [], array $cacheProps = [], $factories = [])
    {
        // Normalize null/empty URLs immediately to prevent type errors
        $url = $url ?: '';

        // Check cache first
        $cacheKey = $this->generateCacheKey($url, $options, $cacheProps);
        $cachedResult = $this->getCachedEmbed($cacheKey);
        if ($cachedResult !== null) {
            return $cachedResult;
        }

        // Prepare options with API keys
        $options = $this->prepareOptions($options);

        // Try to create embed, fallback if it fails
        $media = $this->createEmbed($url, $options, $factories);
        if ($media === null) {
            $media = $this->createFallbackAdapter($url);
        }

        // Process DOM and apply options
        $data = $media instanceof EmbedAdapter ? ($media->data ?? []) : [];
        if (empty($data)) {
            $data = ['html' => $url ? '<iframe src="' . $url . '"></iframe>' : null];
        }

        $media = $this->processDomAndApplyOptions($media, $data, $url, $options);

        // Cache the result
        $this->cacheEmbed($media, $cacheKey, $cacheProps);

        return $media ?? [];
    }

    /**
     * Apply GDPR-compliant URL modifications
     */
    protected function manageGDPR(string $url): string
    {
        if (!$this->settings->enableGdpr) {
            return $url;
        }

        // Handle YouTube URLs - convert to no-cookie domain
        if (preg_match(self::YOUTUBE_PATTERN, $url)) {
            return preg_replace(self::YOUTUBE_PATTERN, 'https://www.youtube-nocookie.com', $url);
        }

        // Handle Vimeo URLs - add DNT parameter
        if (preg_match(self::VIMEO_PATTERN, $url)) {
            return $this->addVimeoDntParameter($url);
        }

        return $url;
    }

    /**
     * Add DNT (Do Not Track) parameter to Vimeo URLs
     */
    private function addVimeoDntParameter(string $url): string
    {
        // If DNT parameter already exists, ensure it's set to 1
        if (strpos($url, 'dnt=') !== false) {
            return preg_replace('/(dnt=(1|0))/i', 'dnt=1', $url);
        }

        // Add DNT parameter
        if (strpos($url, '?') !== false) {
            return preg_replace('/(\?(.*))$/i', '?dnt=1&${2}', $url);
        } else {
            return $url . '?dnt=1';
        }
    }
}
