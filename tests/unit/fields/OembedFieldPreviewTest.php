<?php

namespace wrav\oembed\tests\unit\fields;

use Codeception\Test\Unit;
use craft\test\TestCase;
use wrav\oembed\fields\OembedField;
use wrav\oembed\models\OembedModel;
use craft\base\PreviewableFieldInterface;

/**
 * Unit tests for OembedField card preview functionality
 */
class OembedFieldPreviewTest extends TestCase
{
    private OembedField $field;

    protected function _before(): void
    {
        parent::_before();
        $this->field = new OembedField();
    }

    public function testFieldImplementsPreviewableFieldInterface(): void
    {
        $this->assertInstanceOf(PreviewableFieldInterface::class, $this->field);
    }

    public function testFieldImplementsThumbableFieldInterfaceInCraft5(): void
    {
        if (!interface_exists(\craft\base\ThumbableFieldInterface::class)) {
            $this->markTestSkipped('ThumbableFieldInterface only exists in Craft 5+');
        }

        $this->assertInstanceOf(\craft\base\ThumbableFieldInterface::class, $this->field);
    }

    public function testGetPreviewHtmlReturnsEmptyStringForNullValue(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);

        $result = $this->field->getPreviewHtml(null, $element);

        $this->assertEquals('', $result);
    }

    public function testGetPreviewHtmlReturnsEmptyStringForInvalidModel(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);
        $model = new OembedModel(null);

        $result = $this->field->getPreviewHtml($model, $element);

        $this->assertEquals('', $result);
    }

    public function testGetPreviewHtmlReturnsEmptyStringForEmptyUrl(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);
        $model = new OembedModel('');

        $result = $this->field->getPreviewHtml($model, $element);

        $this->assertEquals('', $result);
    }

    public function testPreviewPlaceholderHtmlReturnsSpanElement(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);

        $result = $this->field->previewPlaceholderHtml(null, $element);

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('oEmbed Preview', $result);
    }

    public function testPreviewPlaceholderHtmlWorksWithNullElement(): void
    {
        $result = $this->field->previewPlaceholderHtml(null, null);

        $this->assertStringContainsString('<span', $result);
    }

    public function testGetThumbHtmlReturnsNullForNullValue(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);

        $result = $this->field->getThumbHtml(null, $element, 100);

        $this->assertNull($result);
    }

    public function testGetThumbHtmlReturnsNullForInvalidModel(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);
        $model = new OembedModel(null);

        $result = $this->field->getThumbHtml($model, $element, 100);

        $this->assertNull($result);
    }

    public function testGetThumbHtmlReturnsNullForEmptyUrl(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);
        $model = new OembedModel('');

        $result = $this->field->getThumbHtml($model, $element, 100);

        $this->assertNull($result);
    }

    public function testGetThumbHtmlRespectsSizeParameter(): void
    {
        $element = $this->createMock(\craft\base\ElementInterface::class);
        $model = new OembedModel('');

        // Even with invalid model, verify the method accepts size parameter
        $result50 = $this->field->getThumbHtml($model, $element, 50);
        $result200 = $this->field->getThumbHtml($model, $element, 200);

        // Both should return null for empty URL
        $this->assertNull($result50);
        $this->assertNull($result200);
    }
}
