<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use yii\db\Schema;
use wrav\oembed\models\OembedModel;

/**
 * OembedField Field
 *
 * @author    reganlawton | boscho87
 * @package   Oembed
 * @since     1.0.0
 */
class OembedField extends Field implements PreviewableFieldInterface
{
    // Public Properties
    // =========================================================================

    /**
     * Some attribute
     *
     * @var string
     */
    public $url = '';

    // Static Methods
    // =========================================================================

    /**
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('oembed', 'oEmbed');
    }

    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @return string The column type. [[\yii\db\QueryBuilder::getColumnType()]] will be called
     *                to convert the give column type to the physical one. For example, `string` will be converted
     *                as `varchar(255)` and `string(100)` becomes `varchar(100)`. `not null` will automatically be
     *                appended as well.
     * @see \yii\db\QueryBuilder::getColumnType()
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value) && $decValue = json_decode($value, true)) {
            if (isset($decValue['url'])) {
                return new OembedModel($decValue['url']);
            }
        }
        return $value ? new OembedModel($value) : null;
    }

    /**
     * Modifies an element query.
     *
     * @param ElementInterface $query The element query
     * @param mixed $value The value that was set on this field’s corresponding [[ElementCriteriaModel]]
     *                                param, if any.
     * @return null|false `false` in the event that the method is sure that no elements are going to be found.
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @param mixed $value
     * @param ElementInterface $element
     * @return string
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        // https://stackoverflow.com/questions/2068344/how-do-i-get-a-youtube-video-thumbnail-from-the-youtube-api
        $youtubeUrl = (string)$value;
        $query = parse_url($youtubeUrl, PHP_URL_QUERY) ?? '';
        parse_str($query, $params);
        $id = $params['v'] ?? '';
        if ($id) {
            $url = sprintf('https://img.youtube.com/vi/%s/default.jpg', $id);
            //Todo possible improvement, (make size configurable)
            return sprintf('<img src="%s" alt="" height="60px">', $url);
        }
        return '';
    }

    /**
     * @return string|null
     */
    public function getSettingsHtml()
    {
        return null;
    }

    /**
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     * @param mixed $value The field’s value. This will either be the [[normalizeValue() normalized
     *                                       value]], raw POST data (i.e. if there was a validation error), or null
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $input = '<input name="' . $this->handle . '" class="text nicetext fullwidth oembed-field" value="' . $value . '" />';
        $preview = sprintf('%s%s%s', '<p><strong>', Craft::t('oembed', 'Preview'), '</strong></p>');
        if ($value) {
            try {
                if ($embed = new OembedModel($value)) {
                    $embed = $embed->embed();
                    if (!empty($embed)) {
                        $preview .= '<div class="oembed-preview">' . $embed->code . '</div>';
                    } else {
                        $preview .= sprintf(
                            '%s%s%s',
                            '<div class="oembed-preview"><p class="error">',
                            Craft::t('oembed', 'Please check your URL.'),
                            '</p></div>'
                        );
                    }
                }
            } catch (\Exception $exception) {
                $preview .= sprintf(
                    '%s%s%s',
                    '<div class="oembed-preview"><p class="error">',
                    Craft::t('oembed', 'Please check your URL.'),
                    '</p></div>'
                );
            }
        } else {
            $preview .= '<div class="oembed-preview"></div>';
        }
        return $input . $preview;
    }
}
