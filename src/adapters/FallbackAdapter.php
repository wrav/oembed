<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\adapters;

use Embed\Adapters\Adapter;
use Embed\Utils;

class FallbackAdapter extends Adapter
{
    protected function init()
    {
        $this->providers = [];
    }

    public function getCode()
    {
        if (!$this->url) {
            return Utils::iframe($this->url);
        }

        return null;
    }
}
