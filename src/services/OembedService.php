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
    private $youtubePattern = '/(?:http|https)*?:*\/\/(?:www\.|)(?:youtube\.com|m\.youtube\.com|youtu\.be|youtube-nocookie\.com)/i';

    /**
     * @param $url
     * @param array $options
     * @return string
     */
    public function render($url, array $options = [], array $cacheProps = [])
    {
        /** @var Media $media */
        $media = $this->embed($url, $options, $cacheProps);

        if (!empty($media)) {
            // If code is empty, we have a fallback to HTML prop
            if(empty($media->code)) {
                return Template::raw($media->html ?? '');
            }

            return Template::raw($media->code ?? '');
        } else {
            return null;
        }
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
     * @param $url
     * @param array $options
     * @return Media|string
     */
    public function embed($url, array $options = [], array $cacheProps = [], $factories = [])
    {
        $plugin = Craft::$app->plugins->getPlugin('oembed');

        try {
            $hash = md5(json_encode($options)).md5(json_encode($cacheProps));
        } catch (\Exception $exception) {
            $hash = '';
        }
        $cacheKey = $url . '_' . $plugin->getVersion() . '_' . $hash;
        $data = [];

        if (Oembed::getInstance()->getSettings()->enableCache && Craft::$app->cache->exists($cacheKey)) {
            return \Craft::$app->cache->get($cacheKey);
        }

        if (Oembed::getInstance()->getSettings()->facebookKey) {
            $options['facebook']['key'] = Oembed::getInstance()->getSettings()->facebookKey;
        }

        try {
            array_multisort($options);

            $embed = new Embed();
        
            // Add custom factories
            if (count($factories) > 0) {
                foreach ($factories as $factory) {
                    $embed->getExtractorFactory()->addAdapter($factory['domain'], $factory['extractor']::class);
                }
            }

            $infos = $embed
                ->get($url ?: "")
            ;

            $infos->setSettings($options);
            
            $data = $infos->getOEmbed()->all();

            $media = new EmbedAdapter($data, $infos);
        } catch (Exception $e) {
            Craft::info($e->getMessage(), 'oembed');

            // Trigger notification event
            if (Oembed::getInstance()->getSettings()->enableNotifications) {
                Oembed::getInstance()->trigger(Oembed::EVENT_BROKEN_URL_DETECTED, new BrokenUrlEvent([
                    'url' => $url,
                ]));
            }

            // Create fallback
            $media = new FallbackAdapter([
                'html' => $url ? '<iframe src="'.$url.'"></iframe>' : null
            ]);
        } finally {
            // Fallback to iframex
            if (empty($data)) {
                $data = [
                    'html' => $url ? '<iframe src="'.$url.'"></iframe>' : null
                ];
            }

            // Wrapping to be safe :)
            try {
                $dom = new DOMDocument;
                $html = $media->getCode() ?: null;

                if (empty($html) || empty((string)$url) ) {
                    $html = empty((string)$url) ? '' : '<iframe src="'.$url.'"></iframe>';
                    $dom->loadHTML($html);
                } else {
                    $dom->loadHTML($data['html']);
                }

                $iframe = $dom->getElementsByTagName('iframe')->item(0);
                $src = $iframe->getAttribute('src');

                $src = $this->manageGDPR($src);

                // Adds additional attributes for GDPR compliance
                if (Oembed::getInstance()->getSettings()->enableGdprCookieBot) {
                    $iframe->setAttribute('data-cookieblock-src', $src);
                    $iframe->setAttribute('data-cookieconsent', 'marketing');
                }

                // Solved issue with "params" options not applying
                if (!preg_match('/\?(.*)$/i', $src)) {
                    $src .= "?";
                }

                if (!empty($options['params'])) {
                    foreach ((array)$options['params'] as $key => $value) {
                        // If value is an array, skip
                        if (is_array($value)) {
                            continue;
                        }

                        $src = preg_replace('/\?(.*)$/i', '?' . $key . '=' . $value . '&${1}', $src);
                    }
                }

                // Autoplay
                if (!empty($options['autoplay']) && strpos($src, 'autoplay=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?autoplay=' . (!!$options['autoplay'] ? '1' : '0') . '&${1}', $src);
                }

                // Width - Override
                if (!empty($options['width']) && is_int($options['width'])) {
                    $iframe->setAttribute('width', $options['width']);
                }

                // Height - Override
                if (!empty($options['height']) && is_int($options['height'])) {
                    $iframe->setAttribute('height', $options['height']);
                }

                // Looping
                if (!empty($options['loop']) && strpos($src, 'loop=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?loop=' . (!!$options['loop'] ? '1' : '0') . '&${1}', $src);
                }

                // Autopause
                if (!empty($options['autopause']) && strpos($src, 'autopause=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?autopause=' . (!!$options['autopause'] ? '1' : '0') . '&${1}', $src);
                }

                // Rel
                if (!empty($options['rel']) && strpos($src, 'rel=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?rel=' . (!!$options['rel'] ? '1' : '0') . '&${1}', $src);
                }

                // Apply attributes to the iframe
                if (!empty($options['attributes'])) {
                    foreach ((array)$options['attributes'] as $key => $value) {
                        $iframe->setAttribute($key, $value);

                        // If key in array, add to the media object
                        if (in_array($key, ['width', 'height'])) {
                            $media->$key = $value;
                        }
                    }
                }

                // Resolve YT loop issues
                preg_match($this->youtubePattern, $url, $ytMatch, PREG_OFFSET_CAPTURE);

                // If youtube video and loop=1 is found
                if (count($ytMatch) && strpos($src, 'loop=1') !== false) {
                    // Get playlist param from the URL code
                    preg_match('/\/embed\/([^?]+)/', $src, $ytCode);

                    // If playlist param is found
                    if (count($ytCode)) {
                        // Add playlist param to the URL
                        $src = preg_replace('/\?(.*)$/i', '?playlist=' . $ytCode[1] . '&${1}', $src);
                    }
                }

                // Set the SRC
                $iframe->setAttribute('src', $src);

                // Get the main element
                $mainElement = null;
                $bodyItem = $dom->getElementsByTagName('body')->item(0);
                if($bodyItem !== null) {
                    if ($bodyItem->childNodes->count() === 1) {
                        // Body only contains 1 child, use that
                        $mainElement = $bodyItem->childNodes->item(0);
                    } else {
                        // Body contains multiple children, wrap in div
                        $mainElement = $dom->createElement('div');

                        // Collect all body children
                        $bodyChildren = [];
                        foreach ($bodyItem->childNodes as $child) {
                            $bodyChildren[] = $child;
                        }

                        // Move all body children to the div
                        foreach ($bodyChildren as $child) {
                            $mainElement->appendChild($child);
                        }
                        
                        // Add div back to body
                        $bodyItem->appendChild($mainElement);
                    }
                }

                // If we were unable to get the main element fall back to the iframe
                if($mainElement !== null) {
                    $mainElement = $iframe;
                }

                // Set the code
                $code = $dom->saveXML($mainElement, LIBXML_NOEMPTYTAG);

                // Apply the code to the media object
                $media->code = $code;

                // Set the URL if not set
                $media->url = $media->url ?: $url;
            } catch (\Exception $exception) {
                Craft::info($exception->getMessage(), 'oembed');
            } finally {
                if (Oembed::getInstance()->getSettings()->enableCache) {
                    // Cache failed requests only for 15 minutes
                    $duration = $media instanceof FallbackAdapter ? 15 * 60 : 60 * 60;

                    $defaultCacheKeys = [
                        'title',
                        'description',
                        'url',
                        'type',
                        'tags',
                        'images',
                        'image',
                        'imageWidth',
                        'imageHeight',
                        'code',
                        'width',
                        'height',
                        'aspectRatio',
                        'authorName',
                        'authorUrl',
                        'providerName',
                        'providerUrl',
                        'providerIcons',
                        'providerIcon',
                        'publishedDate',
                        'license',
                        'linkedData',
                        'feeds',
                    ];

                    $cacheKeys = array_unique(array_merge($defaultCacheKeys, $cacheProps));

                    // Make sure all keys are set to avoid errors
                    foreach ($cacheKeys as $key) {
                        try {
                            $media->{$key} = $media->{$key};
                        } catch (\Exception $e) {
                            $media->{$key} = null;
                        }
                    }

                    Craft::$app->cache->set($cacheKey, json_decode(json_encode($media)), $duration);
                }

                return $media ?? [];
            }
        }
    }

    private function manageGDPR($url)
    {
        if (Oembed::getInstance()->getSettings()->enableGdpr) {
            $skip = false;
            $youtubePattern = '/(?:http|https)*?:*\/\/(?:www\.|)(?:youtube\.com|m\.youtube\.com|youtu\.be|youtube-nocookie\.com)/i';
            preg_match($youtubePattern, $url, $matches, PREG_OFFSET_CAPTURE);

            if (count($matches)) {
                $url = preg_replace($youtubePattern, 'https://www.youtube-nocookie.com', $url);
                $skip = true;
            }

            if (!$skip) {
                if (strpos($url, 'vimeo.com') !== false) {
                    if (strpos($url, 'dnt=') === false) {
                        preg_match('/\?.*$/', $url, $matches, PREG_OFFSET_CAPTURE);
                        if (count($matches)) {
                            $url = preg_replace('/(\?(.*))$/i', '?dnt=1&${2}', $url);
                        } else {
                            $url = $url . '?dnt=1';
                        }
                    }

                    $url = preg_replace('/(dnt=(1|0))/i', 'dnt=1', $url);
                }
            }
        }

        return $url;
    }
}
