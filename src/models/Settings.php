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
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $enableCache;

}
