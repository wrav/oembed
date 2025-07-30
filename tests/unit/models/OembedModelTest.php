<?php

namespace wrav\oembed\tests\unit\models;

use Codeception\Test\Unit;
use wrav\oembed\models\OembedModel;
use craft\test\TestCase;

/**
 * Unit test for OembedModel
 */
class OembedModelTest extends TestCase
{
    public function testModelHandlesNullUrl()
    {
        // Create model with null URL (simulating GraphQL scenario)
        $model = new OembedModel(null);
        
        // Accessing properties should not throw exception
        $title = $model->title;
        $description = $model->description;
        $url = $model->url;
        
        // Should handle gracefully without throwing Embed\Embed::get() error
        // URL gets normalized to empty string, other properties should be null or empty
        $this->assertNull($title);
        $this->assertNull($description);
        $this->assertEquals('', $url); // URL gets normalized to empty string
    }

    public function testModelHandlesEmptyStringUrl()
    {
        // Create model with empty string URL
        $model = new OembedModel('');
        
        // Accessing properties should not throw exception
        $title = $model->title;
        $description = $model->description;
        $url = $model->url;
        
        // Should handle gracefully
        $this->assertNull($title);
        $this->assertNull($description);
        $this->assertEquals('', $url);
    }

    public function testModelHandlesWhitespaceUrl()
    {
        // Create model with whitespace URL
        $model = new OembedModel('   ');
        
        // Accessing properties should not throw exception
        $title = $model->title;
        $description = $model->description;
        $url = $model->url;
        
        // Should handle gracefully
        $this->assertNull($title);
        $this->assertNull($description);
        $this->assertEquals('   ', $url);
    }

    public function testModelToStringWithNullUrl()
    {
        // Create model with null URL
        $model = new OembedModel(null);
        
        // toString should not throw exception
        $stringValue = (string) $model;
        
        // Should return empty string, not null
        $this->assertEquals('', $stringValue);
    }

    public function testGraphQLScenarioSimulation()
    {
        // Simulate GraphQL scenario where oEmbed field is null
        $model = new OembedModel(null);
        
        // Test accessing multiple properties as GraphQL would
        $properties = [
            'title', 'description', 'url', 'type', 'image', 
            'imageWidth', 'imageHeight', 'code', 'width', 'height',
            'aspectRatio', 'authorName', 'authorUrl', 'providerName', 'providerUrl'
        ];
        
        foreach ($properties as $property) {
            // This should not throw any exceptions
            $value = $model->$property;
            
            // For null URL, most properties should be null/empty except 'url' which is normalized
            if ($property === 'url') {
                $this->assertEquals('', $value, "Property '$property' should be empty string for null URL");
            } else {
                // Properties may return null or empty string depending on fallback behavior
                $this->assertTrue(
                    $value === null || $value === '', 
                    "Property '$property' should be null or empty for null URL, got: " . var_export($value, true)
                );
            }
        }
    }

    public function testModelNormalizesUrlOnConstruction()
    {
        // Test various null-ish values get normalized
        $testCases = [
            null => '',
            '' => '',
            '   ' => '   ', // Whitespace is preserved but doesn't cause errors
            'https://example.com' => 'https://example.com'
        ];

        foreach ($testCases as $input => $expected) {
            $model = new OembedModel($input);
            $this->assertEquals($expected, $model->url, "URL normalization failed for input: " . var_export($input, true));
        }
    }
}