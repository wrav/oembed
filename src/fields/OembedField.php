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
use craft\helpers\Gql as GqlHelper;
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
     * @var array
     */
    public $oembed = [];

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
     * @inheritdoc
     * @since 3.3.0
     */
    public function getContentGqlType()
    {
        $typeArray = OembedFieldTypeGenerator::generateTypes($this);

        return [
            'name' => $this->handle,
            'description' => "Oembed field",
            'type' => array_shift($typeArray),
        ];
    }

    /**
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value) &&     $decValue = json_decode($value, true)) {
            if (isset($decValue['url'])) {
                return new OembedModel($decValue['url']);
            }
        }

        $oembed = $value ? new OembedModel($value) : null;

        $this->oembed = $oembed;

        return $oembed;
    }

    /**
     * Modifies an element query.
     *
     * @param ElementInterface $query The element query
     * @param mixed            $value The value that was set on this field’s corresponding [[ElementCriteriaModel]]
     *                                param, if any.
     * @return null|false `false` in the event that the method is sure that no elements are going to be found.
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
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
