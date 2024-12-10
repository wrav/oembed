<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\variables;

use wrav\oembed\Oembed;

/**
 * OembedVariable Variable
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     1.0.0
 */
class OembedVariable
{
    /**
     * Call it like this:
     *
     *     {{ craft.oembed.render(url, options) }}
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public function render($url, array $options = [], array $cacheProps = [])
    {
        if (empty($url)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->render($url, $options, $cacheProps);
    }

    /**
     * Call it like this:
     *
     *     {{ craft.oembed.embed(url, options) }}
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public function embed($url, array $options = [], array $cacheProps = [])
    {
        if (empty($url)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->embed($url, $options, $cacheProps);
    }

    /**
     * Call it like this:
     *
     *     {{ craft.oembed.valid(url, options) }}
     *
     * @param $url
     * @param array $options
     * @return bool
     */
    public function valid($url, array $options = [], array $cacheProps = [])
    {
        if (empty($url)) {
            return false;
        }

        $media = $this->embed($url, $options, $cacheProps);
        return (!empty($media) && isset($media->code));
    }

    /**
     * Call it like this:
     *
     *     {{ craft.oembed.parseTags(input, options) }}
     *
     * @param $input
     * @param array $options
     * @return string
     */
    public function parseTags($input, array $options = [], array $cacheProps = [])
    {
        if (empty($input)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->parseTags($input, $options, $cacheProps);
    }
}
