<?php

namespace wrav\oembed\tests\unit\fields;

use Codeception\Test\Unit;
use wrav\oembed\fields\OembedField;
use wrav\oembed\models\OembedModel;
use craft\test\TestCase;

/**
 * Unit test for OembedField
 *
 * Tests URL normalization feature (fixes #177)
 */
class OembedFieldTest extends TestCase
{
    private OembedField $field;

    protected function setUp(): void
    {
        parent::setUp();
        $this->field = new OembedField();
    }

    /**
     * Test that URLs without scheme get https:// prepended
     * @dataProvider urlNormalizationProvider
     */
    public function testNormalizeValueAddsScheme(string $input, string $expectedUrl)
    {
        $result = $this->field->normalizeValue($input, null);

        $this->assertInstanceOf(OembedModel::class, $result);
        $this->assertEquals($expectedUrl, $result->url);
    }

    public static function urlNormalizationProvider(): array
    {
        return [
            'youtube without scheme' => [
                'youtube.com/watch?v=dQw4w9WgXcQ',
                'https://youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            'youtube with www without scheme' => [
                'www.youtube.com/watch?v=dQw4w9WgXcQ',
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            'youtube with https scheme' => [
                'https://youtube.com/watch?v=dQw4w9WgXcQ',
                'https://youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            'youtube with http scheme' => [
                'http://youtube.com/watch?v=dQw4w9WgXcQ',
                'http://youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            'vimeo without scheme' => [
                'vimeo.com/123456789',
                'https://vimeo.com/123456789'
            ],
            'protocol-relative URL' => [
                '//youtube.com/watch?v=dQw4w9WgXcQ',
                'https://youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            'url with whitespace' => [
                '  youtube.com/watch?v=dQw4w9WgXcQ  ',
                'https://youtube.com/watch?v=dQw4w9WgXcQ'
            ],
        ];
    }

    public function testNormalizeValueWithNull()
    {
        $result = $this->field->normalizeValue(null, null);

        $this->assertInstanceOf(OembedModel::class, $result);
        $this->assertEquals('', $result->url);
    }

    public function testNormalizeValueWithOembedModel()
    {
        $model = new OembedModel('youtube.com/watch?v=dQw4w9WgXcQ');
        $result = $this->field->normalizeValue($model, null);

        $this->assertInstanceOf(OembedModel::class, $result);
        $this->assertEquals('https://youtube.com/watch?v=dQw4w9WgXcQ', $result->url);
    }

    public function testNormalizeValueWithJsonString()
    {
        $json = json_encode(['url' => 'youtube.com/watch?v=dQw4w9WgXcQ']);
        $result = $this->field->normalizeValue($json, null);

        $this->assertInstanceOf(OembedModel::class, $result);
        $this->assertEquals('https://youtube.com/watch?v=dQw4w9WgXcQ', $result->url);
    }
}
