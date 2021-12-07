<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\services;

use craft;
use craft\helpers\Template;
use craft\base\Component;
use DOMDocument;
use Embed\Adapters\Adapter;
use Embed\Embed;
use Embed\Exceptions\InvalidUrlException;
use Embed\Http\CurlDispatcher;
use wrav\oembed\adapters\EmbedAdapter;
use wrav\oembed\events\BrokenUrlEvent;
use wrav\oembed\Oembed;
use yii\log\Logger;

/**
 * OembedService Service
 *
 * @author    reganlawton
 * @package   Oembed
 * @since     1.0.0
 */
class OembedService extends Component
{
    /**
     * @param $url
     * @param array $options
     * @return array|string
     */
    public function embed($url, array $options = [])
    {
        try {
            $hash = md5(json_encode($options));
        } catch (\Exception $exception) {
            $hash = '';
        }
        $cacheKey = $url.'_'.$hash;
        $data = [];

        if (Oembed::getInstance()->getSettings()->enableCache && Craft::$app->cache->exists($cacheKey)) {
            return \Craft::$app->cache->get($cacheKey);
        }

        if (Oembed::getInstance()->getSettings()->facebookKey) {
            $options['facebook']['key'] = Oembed::getInstance()->getSettings()->facebookKey;
        }

        try {
            array_multisort($options);

            $embed = new Embed();
            $infos = $embed
                ->get($url)
            ;
            $infos->setSettings($options);
            $media = $infos->getOEmbed();
            $data = $infos->getOEmbed()->all();
        } catch (InvalidUrlException $e) {
            Craft::info($e->getMessage(), 'oembed');

            // Trigger notification
            if (Oembed::getInstance()->getSettings()->enableNotifications) {
                Oembed::getInstance()->trigger(Oembed::EVENT_BROKEN_URL_DETECTED, new BrokenUrlEvent([
                    'url' => $url,
                ]));
            }
        }
        finally {
            if (empty($data)) {
                $data = [
                    'html' => $url ? '<iframe src="'.$url.'"></iframe>': null
                ];
            }

            // Wrapping to be safe :)
            try {
                $dom = new DOMDocument;
                $dom->loadHTML($data['html']);

                $iframe = $dom->getElementsByTagName('iframe')->item(0);
                $src = $iframe->getAttribute('src');

                $src = $this->manageGDPR($src);

                // Solved issue with "params" options not applying
                if (!preg_match('/\?(.*)$/i', $src)) {
                    $src .= "?";
                }

                if(!empty($options['params'])) {
                    foreach((array)$options['params'] as $key => $value) {
                        $src = preg_replace('/\?(.*)$/i', '?'.$key.'='. $value .'&${1}', $src);
                    }
                }

                // Autoplay
                if (!empty($options['autoplay']) && strpos($html, 'autoplay=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?autoplay='. (!!$options['autoplay'] ? '1' : '0') .'&${1}', $src);
                }

                // Width - Override
                if (!empty($options['width']) && is_int($options['width'])) {
                    $iframe->setAttribute('width', $options['width']);
                }

                // Height - Override
                if (!empty($options['height']) && is_int($options['height'])) {
                    $iframe->setAttribute('height', $options['height']);
                }

                // Looping
                if (!empty($options['loop']) && strpos($html, 'loop=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?loop='. (!!$options['loop'] ? '1' : '0') .'&${1}', $src);
                }

                // Autopause
                if (!empty($options['autopause']) && strpos($html, 'autopause=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?autopause='. (!!$options['autopause'] ? '1' : '0') .'&${1}', $src);
                }

                // Rel
                if (!empty($options['rel']) && strpos($src, 'rel=') === false && $src) {
                    $src = preg_replace('/\?(.*)$/i', '?rel='. (!!$options['rel'] ? '1' : '0') .'&${1}', $src);
                }

                if(!empty($options['attributes'])) {
                    foreach((array)$options['attributes'] as $key => $value) {
                        $iframe->setAttribute($key, $value);
                    }
                }

                $iframe->setAttribute('src', $src);
                $iframeXML = $dom->saveXML($iframe, LIBXML_NOEMPTYTAG);

                $data['html'] = $iframeXML;
            } catch (\Exception $exception) {
                Craft::info($exception->getMessage(), 'oembed');
            }
            finally {
                if (Oembed::getInstance()->getSettings()->enableCache) {
                    // Cache failed requests only for 15 minutes
                    $duration = 60 * 60;

                    Craft::$app->cache->set($cacheKey, $media, $duration);
                }

                return new EmbedAdapter($data);
            }
        }
    }

    private function manageGDPR($url)
    {
        if (Oembed::getInstance()->getSettings()->enableGdpr) {
            $skip = false;
            $youtubePattern = '/(?:http|https)*?:*\/\/(?:www\.|)(?:youtube\.com|m\.youtube\.com|youtu\.be|youtube-nocookie\.com)/i';
            preg_match($youtubePattern, $url, $matches, PREG_OFFSET_CAPTURE);

            if(count($matches)) {
                $url = preg_replace($youtubePattern, 'https://www.youtube-nocookie.com', $url);
                $skip = true;
            }

            if(!$skip) {
                if (strpos($url, 'vimeo.com') !== false ) {
                    if (strpos($url, 'dnt=') === false) {
                        preg_match('/\?.*$/', $url, $matches, PREG_OFFSET_CAPTURE);
                        if(count($matches)) {
                            $url = preg_replace('/(\?(.*))$/i', '?dnt=1&${2}', $url);
                        } else {
                            $url = $url.'?dnt=1';
                        }
                    }

                    $url = preg_replace('/(dnt=(1|0))/i', 'dnt=1', $url);
                    $skip = true;
                }
            }
        }

        return $url;
    }

    /**
     * @param $url
     * @param array $options
     * @return string
     */
    public function render($url, array $options = [])
    {
        /** @var Media $media */
        $media = (object)$this->embed($url, $options);

        if (!empty($media)) {
            return Template::raw($media->html ?? '');
        } else {
            return null;
        }
    }
}
