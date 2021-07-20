<?php
namespace wrav\oembed\events;

use craft;
use craft\base\ElementInterface;
use craft\behaviors\DraftBehavior;
use yii\base\Event;

/**
 * Broken Url event class.
 *
 * @since 1.2.6
 */
class BrokenUrlEvent extends Event
{
    /**
     * @var string The url which is broken
     */
    public $url;

}
