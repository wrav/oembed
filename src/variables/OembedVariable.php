<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\variables;

use Craft;
use DOMElement;
use DOMDocument;
use DOMException;
use wrav\oembed\Oembed;

/**
 * OembedVariable Variable
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     1.0.0
 */
class OembedVariable
{
    /**
     * Call it like this:
     *
     *     {{ craft.oembed.render(url, options) }}
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public function render($url, array $options = [], array $cacheProps = [])
    {
        if (empty($url)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->render($url, $options, $cacheProps);
    }

    /**
     * Call it like this:
     *
     *     {{ craft.oembed.embed(url, options) }}
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public function embed($url, array $options = [], array $cacheProps = [])
    {
        if (empty($url)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->embed($url, $options, $cacheProps);
    }

    /**
     * Call it like this:
     *
     *     {{ craft.oembed.media(url, options) }}
     *
     * @param $url
     * @param array $options
     * @return mixed
     */
    public function media($url, array $options = [], array $cacheProps = [])
    {
        return $this->embed($url, $options, $cacheProps);
    }

    /**
     * Call it like this:
     *
     *     {{ craft.oembed.valid(url, options) }}
     *
     * @param $url
     * @param array $options
     * @return bool
     */
    public function valid($url, array $options = [], array $cacheProps = [])
    {
        if (empty($url)) {
            return false;
        }

        $media = $this->embed($url, $options, $cacheProps);
        return (!empty($media) && isset($media->code));
    }

    /**
     * Call it like this:
     *
     *     {{ craft.oembed.parseTags(input, options) }}
     *
     * @param $input
     * @param array $options
     * @return string
     */
    public function parseTags($input, array $options = [], array $cacheProps = [])
    {
        if (empty($input)) {
            return null;
        }

        return Oembed::getInstance()->oembedService->parseTags($input, $options, $cacheProps);
    }


    /**
     * Call it like this:
     *
     *     {{ craft.oembed.addConsentChecksToEmbed(code) }}
     *
     * @param $input
     * @param array $options
     * @return string
     */
    public function addConsentChecksToEmbed(string $code): string
    {
        try {
            $dom = new DOMDocument();

            // Disable errors - TikTok puts a <section> in a <blockquote> for some reason which raises a PHP warning
            libxml_use_internal_errors(true);
            $dom->loadHTML($code);
            libxml_use_internal_errors(false);

            // Ensure there is a root level element
            $body = $dom->getElementsByTagName('body')->item(0);
            $childNodes = iterator_to_array($body->childNodes);
            // Get the nodes under body which are elements
            /** @var DOMElement[] $childElements */
            $childElements = array_filter(
                $childNodes,
                function ($node) {
                    return $node instanceof DOMElement;
                }
            );

            if (count($childElements) === 1) {
                $root = $childElements[0];
            } else {
                // Wrap the elements in a div
                $root = $dom->createElement('div');
                if (!$root) {
                    Craft::error('Could not create root element', __METHOD__);
                    return '';
                }

                foreach ($childNodes as $child) {
                    $root->appendChild($child);
                }
                $body->appendChild($root);
            }

            /** @var DOMElement $script */
            foreach($dom->getElementsByTagName('script') as $script) {
                $script->setAttribute('data-cookieconsent', 'marketing');
                $script->setAttribute('type', 'text/plain');
            }

            /** @var DOMElement $iframe */
            foreach($dom->getElementsByTagName('iframe') as $iframe) {
                $iframe->setAttribute('data-cookieconsent', 'marketing');
                $iframe->setAttribute('data-cookieblock-src', $iframe->getAttribute('src'));
                $iframe->removeAttribute('src');
            }

            return $dom->saveHTML($root);
        } catch (DOMException $e) {
            Craft::error($e->getMessage(), __METHOD__);
            return '';
        }
    }
}
