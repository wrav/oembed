<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\models;

use Embed\Adapters\Youtube;
use wrav\oembed\Oembed;
use craft\base\Model;
use craft\helpers\Json;

/**
 * OembedModel Model
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     1.0.0
 */
class OembedModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var mixed
     */
    private $oembed = null;

    /**
     * OembedModel constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    // Public Methods
    // =========================================================================

    public function __toString()
    {
        return "".$this->getUrl();
    }

    public function __get($name)
    {
        if (property_exists($this , $name)) {
            return $this->$name ?? null;
        }

        if ($this->oembed === null) {
            $oembed = Oembed::getInstance()->oembedService->embed($this->url);

            if (!$oembed) {
                $this->embed = [];
            }

            $this->oembed = $oembed;
        }

        return $this->oembed->$name ?? null;
    }


    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            ['url', 'text'],
            ['url', 'default', 'value' => ''],
        ];
    }

    public function parseArray($arr)
    {
        if (is_array($arr) && !empty($arr['url'])) {
            if(is_array($arr['url'])) {
                return $this->parseArray($arr['url']);
            }
            return $arr['url'];
        }
        return null;
    }

    public function getUrl()
    {
        $value = $this->url;

        if (is_string($value) && $decValue = Json::decodeIfJson($value, true)) {
            if (isset($decValue['url'])) {
                return new OembedModel($decValue['url'] ?? null);
            }
        }

        if (is_string($value)) {
            $decValue = json_decode($value, true);
            if($decValue) {
                $value = $decValue;
            }
        }

        if(is_array($value)) {
            $value = $this->parseArray($value);
        }

        $this->url = $value ? $value : null;
        return $this->url;
    }

    /**
     * @param array $options
     * @return string
     */
    public function render(array $options = [])
    {
        if (empty($this->getUrl())) {
            return null;
        }

        return Oembed::getInstance()->oembedService->render($this->getUrl(), $options);
    }

    /**
     * @todo Currently for CraftCMS version 2.5 template support, will be removed in future release
     *
     * @param array $options
     * @return string
     */
    public function embed(array $options = [])
    {
        if (empty($this->getUrl())) {
            return null;
        }

        return Oembed::getInstance()->oembedService->embed($this->getUrl(), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function media(array $options = [])
    {
        if (empty($this->getUrl())) {
            return null;
        }

        return $this->embed($options);
    }

    /**
     * @return boolean
     */
    public function valid(array $options = [])
    {
        if (empty($this->getUrl())) {
            return false;
        }

        $media = $this->embed($options);
        return (!empty($media) && isset($media->code));
    }
}
