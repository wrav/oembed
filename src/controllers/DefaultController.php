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

    protected $allowAnonymous = true;

    // Public Methods
    // =========================================================================

    public function actionPreview()
    {
        $data = Craft::$app->request->getQueryParams();

        try {
            if (isset($data['url'])) {
                echo Oembed::getInstance()->oembedService->render($data['url'], []);
            }   
        } catch(\Exception $exception) {
            if (getenv('ENVIRONMENT') === 'dev') {
                throw new $exception;
            }
        } finally {
            Craft::$app->end();
        }

    }
}
