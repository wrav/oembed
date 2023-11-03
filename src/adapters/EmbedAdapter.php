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

class EmbedAdapter
{
    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __call($name, $arguments)
    {
        return $this->data[$name] ?? null;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __toString()
    {
        return (string)$this->getCode();
    }

    public function getCode()
    {
        return isset($this->data['html']) ? $this->data['html'] : '';
    }
}
