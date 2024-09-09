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
    private $extractorData;

    /**
     * @param mixed $data The data to be mapped to the EmbedAdapter object
     * @param \Embed\Extractor $extractorData The extractor data to be used as a fallback
     * 
     * @var string $title The title of the media
     * @var string $description The description of the media
     * @var string $url The url of the media
     * @var string $type The type of the media
     * @var array $tags The tags of the media
     * @var string $images The images of the media
     * @var string $image The image of the media
     * @var int $imageWidth The width of the image
     * @var int $imageHeight The height of the image
     * @var string $code The code of the media
     * @var int $width The width of the media
     * @var int $height The height of the media
     * @var float $aspectRatio The aspect ratio of the media
     * @var string $authorName The author name of the media
     * @var string $authorUrl The author url of the media
     * @var string $providerName The provider name of the media
     * @var string $providerUrl The provider url of the media
     * @var array $providerIcons The provider icons of the media
     * @var string $providerIcon The provider icon of the media
     * @var string $publishedDate The published date of the media
     * @var string $license The license of the media
     * @var array $linkedData The linked data of the media
     * @var array $feeds The feeds of the media
     * 
     * @return void
     */
    public function __construct($data, \Embed\Extractor $extractorData = null)
    {
        $this->data = $data;
        $this->extractorData = $extractorData;

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

            // Try to get the value from the extractorData first and fallback to the oembed data array
            if($this->$key === null && $this->extractorData) {
                try {
                    $this->$key = $this->extractorData->$key;

                    // If the value is an stringable object, convert it to a string
                    if($this->$key !== null && is_object($this->$key) && method_exists($this->$key, '__toString')) {
                        $this->$key = (string) $this->$key;
                    }
                } catch (\Exception $e) {
                    // Do nothing if the property doesn't exist
                }
            }
        }

        // Get linkedData from the extractorData if it's not set
        if($key === 'linkedData' && $this->extractorData && $this->linkedData === null) {
            $this->linkedData = $this->extractorData->getLinkedData()->all();
        }

        // Fallback for .code incase it's empty
        $this->code = $this->code ?: $this->getCode();

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

        // Set width, height and aspect ratio from the extractorData code object, if they are not set
        try {
            $codeObject = $this->extractorData->code;

            if($codeObject && $this->width === null) {
                $this->width = $codeObject->width;
            }

            if($codeObject && $this->height === null) {
                $this->height = $codeObject->height;
            }

            if($codeObject && $this->ratio === null) {
                $this->aspectRatio = $codeObject->ratio;
            }
        } catch (\Exception $e) {
            // Do nothing if the property doesn't exist
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
