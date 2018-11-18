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

use wrav\oembed\Oembed;
use craft\base\Model;

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
     * Some model attribute
     *
     * @var string
     */
    public $url = '';

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
    public function rules()
    {
        return [
            ['url', 'text'],
            ['url', 'default', 'value' => ''],
        ];
    }

    public function getUrl()
    {

        $value = $this->url;

        if (is_string($value)) {
            $decValue = json_decode($value, true);
            if($decValue) {
                $value = $decValue;
            }
        }

        if(is_array($value)) {
            if (isset($value['url'])) {
                if(is_array($value['url'])) {
                    if(is_array($value['url']['url'])) {
                        if(is_array($value['url']['url']['url'])) {
                            return $value['url']['url']['url']['url'];
                        }
                        return $value['url']['url']['url'];
                    }
                    return $value['url']['url'];
                }
                return $value['url'];
            }
            return null;
        }

        return $value ? $value : null;
    }

    /**
     * @param array $options
     * @return string
     */
    public function render(array $options = [])
    {
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
        return Oembed::getInstance()->oembedService->embed($this->getUrl(), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function media(array $options = [])
    {
        return $this->embed($options);
    }
}
