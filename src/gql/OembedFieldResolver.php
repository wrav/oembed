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

use craft\gql\base\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class OembedFieldResolver extends ObjectType
{

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        var_dump($source);
        die;

        switch($resolveInfo->fieldName) {
            case 'url':
//                var_dump($resolveInfo);
//                die;
                return 'Working';
            default:
                return $source->{$resolveInfo->fieldName} ?? null;
        }
    }
}
