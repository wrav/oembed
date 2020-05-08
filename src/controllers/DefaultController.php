<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */


namespace wrav\oembed\controllers;

use wrav\oembed\Oembed;

use Craft;
use craft\web\Controller;

/**
 * @author    reganlawton
 * @package   oembed
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    protected $allowAnonymous = false;

    // Public Methods
    // =========================================================================

    public function actionPreview()
    {
        $url = Craft::$app->request->getRequiredParam('url');
        $options = Craft::$app->request->getParam('options') ?? [];

        try {
            return $this->renderTemplate(
                'oembed/preview',
                [
                    'url' => $url,
                    'options' => $options,
                    'settings' => Oembed::getInstance()->getSettings(),
                ]
            );
        } catch(\Exception $exception) {
            throw new $exception;
        }
    }
}
