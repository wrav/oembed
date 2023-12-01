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

        // Map the data to the EmbedAdapter object properties for easy access
        $defaultKeys = [
            'title',
            'description',
            'url',
            'type',
            'tags',
            'images',
            'image',
            'imageWidth',
            'imageHeight',
            'code',
            'width',
            'height',
            'aspectRatio',
            'authorName',
            'authorUrl',
            'providerName',
            'providerUrl',
            'providerIcons',
            'providerIcon',
            'publishedDate',
            'license',
            'linkedData',
            'feeds',
        ];

        foreach ($defaultKeys as $key) {
            // Convert the key from camelCase to snake_case
            $keyName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));

            $this->$key = $this->data[$keyName] ?? null;
        }

        // Custom mapping

        // Fallback for .image
        if(!$this->image) {
            if ($this->data['thumbnail_url'] ?? null) {
                $this->image = $this->data['thumbnail_url'] ?? null;
            }
        }

        // Fallback for .images
        if(!$this->images) {
            if($this->image) {
                $this->images = array_filter([$this->image]);
            }
        }

        // Fallback for .providerIcons
        if(!$this->providerIcons) {
            if($this->providerIcon) {
                $this->providerIcons = array_filter([$this->providerIcon]);
            }
        }

        // Fallback for .imageWidth
        if (!$this->imageWidth) {
            if ($this->data['thumbnail_width'] ?? null) {
                $this->imageWidth = $this->data['thumbnail_width'] ?? null;
            }
        }

        // Fallback for .imageHeight
        if (!$this->imageHeight) {
            if ($this->data['thumbnail_height'] ?? null) {
                $this->imageHeight = $this->data['thumbnail_height'] ?? null;
            }
        }
    }

    public function __call(string $name, array $arguments)
    {
        return $this->$name ?? $this->data[$name] ?? null;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->$name = $value;
    }

    public function __isset(string $name): bool
    {
        return $this->$name ?? $this->data[$name] ?? false;
    }

    public function __toString(): string
    {
        return $this->getCode();
    }

    public function getCode(): string
    {
        return $this->data['html'] ?? '';
    }
}
