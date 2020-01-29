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
use craft\helpers\Template;
use craft\base\Component;
use DOMDocument;
use Embed\Adapters\Adapter;
use Embed\Embed;
use wrav\oembed\Oembed;
use yii\log\Logger;

/**
 * OembedService Service
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     1.0.0
 */
class OembedService extends Component
{
    /**
     * @param $url
     * @param array $options
     * @return Media|string
     */
    public function embed($url, array $options = [])
    {
        if (Oembed::getInstance()->getSettings()->enableCache && Craft::$app->cache->exists($url)) {
            return \Craft::$app->cache->get($url);
        }

        try {
            array_multisort($options);

            /** @var Adapter $media */
            $media = Embed::create($url, $options);

            if (!empty($media) && !isset($media->code)) {
                $media->code = "<iframe src='$url' width='100%' frameborder='0' scrolling='no'></iframe>";
            }
        } finally {
            if (!empty($media)) {
                if (Oembed::getInstance()->getSettings()->enableCache) {
                    Craft::$app->cache->set($url, $media, 'P1H');
                }
            } else {
                $media = new class {
                    // Returns NULL for calls to props
                    public function __call(string $name , array $arguments )
                    {
                        return null;
                    }
                };
            }


            // Wrapping to be safe :)
            try {
                $html = $media->code;
                $dom = new DOMDocument;
                $dom->loadHTML($html);

                $iframe = $dom->getElementsByTagName('iframe')->item(0);
                $src = $iframe->getAttribute('src');

                if(!empty($options['params'])) {
                    foreach((array)$options['params'] as $key => $value) {
                        $src = preg_replace('/\?(.*)$/i', '?'.$key.'='. $value .'&${1}', $src);
                    }
                }

                // Autoplay
                if (!empty($options['autoplay']) && strpos($html, 'autoplay=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?autoplay='. (!!$options['autoplay'] ? '1' : '0') .'&${1}', $src);
                }

                // Looping
                if (!empty($options['loop']) && strpos($html, 'loop=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?loop='. (!!$options['loop'] ? '1' : '0') .'&${1}', $src);
                }

                // Autopause
                if (!empty($options['autopause']) && strpos($html, 'autopause=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?autopause='. (!!$options['autopause'] ? '1' : '0') .'&${1}', $src);
                }

                // Rel
                if (!empty($options['rel']) && strpos($html, 'rel=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?rel='. (!!$options['rel'] ? '1' : '0') .'&${1}', $src);
                }

                $iframe->setAttribute('src', $src);
                $media->code = $dom->saveXML($iframe);
            } catch (\Exception $exception) {
                Craft::info($exception->getMessage(), 'oembed');
            }
            finally {
                return $media;
            }
        }
    }

    /**
     * @param $url
     * @param array $options
     * @return string
     */
    public function render($url, array $options = [])
    {
        /** @var Media $media */
        $media = $this->embed($url, $options);

        if (!empty($media)) {
            return Template::raw(isset($media->code) ? $media->code : '');
        } else {
            return null;
        }
    }
}
