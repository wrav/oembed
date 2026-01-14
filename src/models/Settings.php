<?php
/**
 * oEmbed plugin for Craft CMS 3.x
 *
 * A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.
 *
 * @link      https://github.com/wrav
 * @copyright Copyright (c) 2017 reganlawton
 */

namespace wrav\oembed\models;

use Craft;
use craft\base\Model;

/**
 * @author    reganlawton
 * @package   Oembed
 * @since     1.1.6
 */
class Settings extends Model
{
    /**
     * @var bool
     */
    public $enableCache;

    /**
     * @var bool
     */
    public $enableGdpr;

    /**
     * @var bool
     */
    public $enableGdprCookieBot;

    /**
     * @var string
     */
    public $enableNotifications;

    /**
     * @var string
     */
    public $notificationEmail;

    /**
     * @var string
     */
    public $previewHidden;

    /**
     * @var string
     */
    public $facebookKey;

    /**
     * @var bool
     */
    public $enableCookieCleanup = true;

    /**
     * @var int Cookie file maximum age in seconds (default: 24 hours)
     */
    public $cookieMaxAge = 86400;

    /**
     * @var string Custom cookies directory path (optional)
     */
    public $cookiesPath = '';

    /**
     * @var array
     */
    public $gdprElements = [];

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     */
    public function rules(): array
    {
        return [
            ['enableCache', 'boolean'],
            ['enableCache', 'default', 'value' => false],

            ['enableGdpr', 'boolean'],
            ['enableGdpr', 'default', 'value' => false],

            ['enableGdprCookieBot', 'boolean'],
            ['enableGdprCookieBot', 'default', 'value' => false],

            ['enableNotifications', 'boolean'],
            ['enableNotifications', 'default', 'value' => false],

            ['previewHidden', 'boolean'],
            ['previewHidden', 'default', 'value' => false],

            ['facebookKey', 'string'],
            ['facebookKey', 'default', 'value' => ''],

            ['notificationEmail', 'string'],
            ['notificationEmail', 'default', 'value' => ''],

            ['enableCookieCleanup', 'boolean'],
            ['enableCookieCleanup', 'default', 'value' => true],

            ['cookieMaxAge', 'integer', 'min' => 300], // Minimum 5 minutes
            ['cookieMaxAge', 'default', 'value' => 86400], // 24 hours

            ['cookiesPath', 'string'],
            ['cookiesPath', 'default', 'value' => ''],

            ['gdprElements', 'safe'],
        ];
    }

}
