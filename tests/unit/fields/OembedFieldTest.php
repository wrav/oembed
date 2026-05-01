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

    /**
     * Test that HTML embed codes are rejected and return an empty OembedModel (fixes #181)
     * @dataProvider htmlEmbedCodeProvider
     */
    public function testNormalizeValueRejectsHtmlEmbedCode(string $input)
    {
        $result = $this->field->normalizeValue($input, null);

        $this->assertInstanceOf(OembedModel::class, $result);
        $this->assertEmpty($result->url, 'HTML embed code should produce an empty URL, not be stored or rendered');
    }

    public static function htmlEmbedCodeProvider(): array
    {
        return [
            'vimeo full embed code' => [
                '<div style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/1127343747?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" referrerpolicy="strict-origin-when-cross-origin" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="Test Video"></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>',
            ],
            'youtube iframe embed' => [
                '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
            ],
            'bare iframe tag' => [
                '<iframe src="https://example.com/video"></iframe>',
            ],
            'already-prefixed bad data from db' => [
                'https://<div style="padding:56.25% 0 0 0"><iframe src="https://player.vimeo.com/video/123"></iframe></div>',
            ],
        ];
    }

    /**
     * Test that an OembedModel containing an HTML embed code URL is also rejected
     */
    public function testNormalizeValueRejectsOembedModelWithHtmlUrl()
    {
        $model = new OembedModel('https://<div><iframe src="https://player.vimeo.com/video/123"></iframe></div>');
        $result = $this->field->normalizeValue($model, null);

        $this->assertInstanceOf(OembedModel::class, $result);
        $this->assertEmpty($result->url);
    }
}
