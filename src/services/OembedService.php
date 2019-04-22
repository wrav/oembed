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
use Embed\Adapters\Adapter;
use Embed\Embed;
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

                return $media;
            }

            return new class {
                // Returns NULL for calls to props
                public function __call(string $name , array $arguments )
                {
                    return null;
                }
            };
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
