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
    public function render($url, array $options = [])
    {
        if (empty($url)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->render($url, $options);
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
    public function embed($url, array $options = [])
    {
        if (empty($url)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->embed($url, $options);
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
    public function valid($url, array $options = [])
    {
        if (empty($url)) {
            return false;
        }

        $media = $this->embed($url, $options);
        return (!empty($media) && isset($media->code));
    }
}
