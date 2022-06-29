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
     * Magic method to execute methods to return paramaters
     * For example, $source->sourceUrl executes $source->getSourceUrl().
     *
     * @param string $name The property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get'.$name;

        if(property_exists($this, $name)){
            return $this->$name;
        }

        if(property_exists($this, $method)){
            return $this->$method;
        }

        if (method_exists($this, $method)) {
            return $this->$name = $this->$method();
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
