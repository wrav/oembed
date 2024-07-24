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
use craft\elements\MatrixBlock as MatrixBlockElement;
use craft\gql\arguments\elements\MatrixBlock as MatrixBlockArguments;
use craft\gql\resolvers\elements\MatrixBlock as MatrixBlockResolver;
use craft\gql\types\generators\MatrixBlockType as MatrixBlockTypeGenerator;
use craft\gql\types\QueryArgument;
use craft\helpers\ArrayHelper;
use craft\helpers\Gql as GqlHelper;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use GraphQL\Type\Definition\Type;
use wrav\oembed\gql\OembedFieldTypeGenerator;
use wrav\oembed\Oembed;
use yii\db\Schema;
use wrav\oembed\models\OembedModel;

/**
 * OembedField Field
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     1.0.0
 */
class OembedField extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var mixed|null
     */
    protected $value;

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
    public function rules(): array
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
     * @inheritdoc
     * @since 3.3.0
     */
    public function getContentGqlType()
    {
        $typeArray = OembedFieldTypeGenerator::generateTypes($this);

        $handle = $this->handle;

        return [
            'name' => $handle,
            'description' => "Oembed field",
            'type' => array_shift($typeArray),
            // The `args` array specifies the GraphQL arguments that the `embed` function accepts so we can apply options for the oEmbed service
            'args' => [
                'options' => [
                    'name' => 'options',
                    'type' => Type::string(),
                    'description' => 'This should be a JSON-encoded string.',
                ],
                'cacheProps' => [
                    'name' => 'cacheProps',
                    'type' => Type::string(),
                    'description' => 'This should be a JSON-encoded string.',
                ],
            ],
            // Use the `resolve` method to convert the field value into a format that can be used by the oEmbed services embed method
            'resolve' => function($source, $arguments) use ($handle) {
                try {
                    $url = $source[$handle]['url'];
                } catch (\Exception $e) {
                    throw new \Exception('The oEmbed field is not set.');
                }

                if (isset($arguments['options'])) {
                    $arguments['options'] = Json::decode($arguments['options']);
                }
                if (isset($arguments['cacheProps'])) {
                    $arguments['cacheProps'] = Json::decode($arguments['cacheProps']);
                }

                $embed = Oembed::getInstance()->oembedService->embed($url, $arguments['options'] ?? [], $arguments['cacheProps'] ?? []);

                return $embed;
            }
        ];
    }

    /**
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, $element = null)
    {
        // If null, don’t proceed
        if ($value === null) {
            if ($this->required) {
                return null;
            }

            return new OembedModel(null);
        }

        // If an instance of `OembedModel` and URL is set, return it
        if ($value instanceof OembedModel && $value->url) {
            if (UrlHelper::isFullUrl($value->url)) {
                return $this->value = $value->url;
            } else {
                // If we get here, something’s gone wrong
                return new OembedModel(null);
            }
        }

        // If JSON object string, decode it and use that as the value
        $value = Json::decodeIfJson($value); // Returns an array

        // If array with `url` attribute, that’s our url so update the value
        // Run `getValue` to avoid https://github.com/wrav/oembed/issues/74
        while(is_array($value)) {
            $value = ArrayHelper::getValue($value, 'url');
        }

        // If URL stri  ng, return an instance of `OembedModel`
        if (is_string($value) && UrlHelper::isFullUrl($value)) {
            return $this->value = new OembedModel($value);
        }

        // If we get here, something’s gone wrong
        return new OembedModel(null);
    }

    /**
     * Modifies an element query.
     *
     * @param ElementInterface $query The element query
     * @param mixed            $value The value that was set on this field’s corresponding [[ElementCriteriaModel]]
     *                                param, if any.
     * @return null|false `false` in the event that the method is sure that no elements are going to be found.
     */
    public function serializeValue($value, $element = null): mixed
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @return string|null
     */
    public function getSettingsHtml(): ?string
    {
        return null;
    }

    /**
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     * @param mixed                 $value   The field’s value. This will either be the [[normalizeValue() normalized
     *                                       value]], raw POST data (i.e. if there was a validation error), or null
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $settings = Oembed::getInstance()->getSettings();
        $hidden = $settings['previewHidden'];
        $previewIcon = $hidden ? 'expand' : 'collapse';


        $input = '<input name="'.$this->handle.'" class="text nicetext fullwidth oembed-field" value="'.$value.'" />';
        $preview = '<div class="oembed-header">
                      <p class="fullwidth"><strong>Preview</strong> <span class="right" data-icon-after="'.$previewIcon.'"></span></p>
                    </div>';

        if ($value) {
            try {
                if ($embed = new OembedModel($value)) {
                    $embed = $embed->embed();

                    $hiddenClass = $hidden ? 'hidden' : '';

                    if (!empty($embed)) {
                        $preview .= '<div class="oembed-preview '.$hiddenClass.'">'.$embed->code.'</div>';
                    } else {
                        $preview .= '<div class="oembed-preview '.$hiddenClass.'"><p class="error">Please check your URL.</p></div>';
                    }
                }
            } catch (\Exception $exception) {
                $preview .= '<div class="oembed-preview"><p class="error">Please check your URL.</p></div>';
            }
        } else {
            $preview .= '<div class="oembed-preview"></div>';
        }
        return $input.$preview;
    }
}
