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

use craft\helpers\Template;
use craft\base\Component;
use Essence\Essence;
use Essence\Media;

/**
 * OembedService Service
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     2.0.0
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
        array_multisort($options);

        /** @var Essence $essence */
        $essence = new Essence();

        /** @var Media $media */
        $media = $essence->extract($url,$options);

        if ($media) {
            return $media;
        }

        return null;
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

        return Template::raw(isset($media->html) ? $media->html : '');
    }
}
