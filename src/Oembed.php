<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed;

use Craft;
use craft\web\View;
use yii\base\Event;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\services\Fields;

use craft\services\Plugins;
use craft\events\PluginEvent;
use wrav\oembed\models\Settings;
use wrav\oembed\fields\OembedField;
use wrav\oembed\jobs\BrokenUrlNotify;
use wrav\oembed\events\BrokenUrlEvent;
use craft\events\RegisterUrlRulesEvent;
use wrav\oembed\services\OembedService;
use wrav\oembed\variables\OembedVariable;
use craft\web\twig\variables\CraftVariable;

use craft\events\RegisterComponentTypesEvent;
use wrav\oembed\assetbundles\oembed\OembedAsset;

/**
 * @author    reganlawton
 * @package   oEmbed
 * @since     1.0.0
 *
 * @property  OembedService $oembedService
 */
class Oembed extends Plugin
{
    const EVENT_BROKEN_URL_DETECTED = 'oembedBrokenUrlDetected';

    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Oembed::$plugin
     *
     * @var Oembed
     */
    public static $plugin;

    /**
     * @var string|null The plugin’s schema version number
     */
    public string $schemaVersion = '1.0.1';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Oembed::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'oembedService' => OembedService::class,
        ]);

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = OembedField::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('oembed', OembedVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::info(
                        Craft::t(
                            'oembed',
                            'oEmbed plugin installed'
                        ),
                        __METHOD__
                    );
                }
            }
        );

        // Register Assets
        if (!Craft::$app->getRequest()->getIsSiteRequest() && !Craft::$app->getRequest()->getIsConsoleRequest()) {
            Craft::$app->getView()->registerAssetBundle(OembedAsset::class);
        }

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['oembed/preview'] = 'oembed/default/preview';
            }
        );

        Event::on(
            Oembed::class,
            Oembed::EVENT_BROKEN_URL_DETECTED,
            function (BrokenUrlEvent $event) {
                // Validate URL before queuing notification job
                if (!$event->url || trim($event->url) === '') {
                    Craft::warning('BrokenUrlEvent: Cannot queue notification for empty URL', 'oembed');
                    return;
                }

                Craft::info('BrokenUrlEvent: Queuing notification job for URL: ' . $event->url, 'oembed');
                
                Craft::$app->getQueue()->push(new BrokenUrlNotify([
                    'url' => trim($event->url),
                ]));
            }
        );

        Craft::info(
            Craft::t(
                'oembed',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?craft\base\Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'oembed/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }

}
