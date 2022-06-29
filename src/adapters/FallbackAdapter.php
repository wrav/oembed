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
    /**
     * Magic method to return properties if the exists
     * and fail gracefully (return null) if they don't.
     *
     * @param string $name The property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if(property_exists($this, $name)){
            return $this->$name;
        }

        return null;
    }

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
