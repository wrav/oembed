<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\gql;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeLoader;
use GraphQL\Type\Definition\Type;

class OembedFieldTypeGenerator implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        /** @var OembedField $context */
        $typeName = self::getName($context);

        $properties = [
            'title' => Type::string(),
            'description' => Type::string(),
            'url' => Type::string(),
            'type' => Type::string(),
            'images' => Type::string(),
            'image' => Type::string(),
            'imageWidth' => Type::string(),
            'imageHeight' => Type::string(),
            'code' => Type::string(),
            'width' => Type::string(),
            'height' => Type::string(),
            'aspectRatio' => Type::string(),
            'authorName' => Type::string(),
            'authorUrl' => Type::string(),
            'providerName' => Type::string(),
            'providerUrl' => Type::string(),
        ];

        $property = GqlEntityRegistry::getEntity($typeName)
            ?: GqlEntityRegistry::createEntity($typeName, new OembedFieldResolver([
                'name' => $typeName,
                'description' => 'This entity has all the Oembed Field properties',
                'fields' => function () use ($properties) {
                    return $properties;
                },
            ]));

        TypeLoader::registerType($typeName, function () use ($property) {
            return $property;
        });

        return [$property];
    }

    /**
     * @inheritdoc
     */
    public static function getName($context = null): string
    {
        /** @var OembedField $context */
        return $context->handle . '_OembedField';
    }
}
