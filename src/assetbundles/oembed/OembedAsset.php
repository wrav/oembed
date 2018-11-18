<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\assetbundles\oembed;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    reganlawton
 * @package   oembed
 * @since     1.0.0
 */
class OembedAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@wrav/oembed/assetbundles/oembed/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/oembed.js',
        ];

        $this->css = [
        ];

        parent::init();
    }
}
