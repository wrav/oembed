<?php
namespace wrav\oembed\jobs;

use craft;
use craft\base\ElementInterface;
use craft\behaviors\DraftBehavior;
use craft\queue\BaseJob;
use wrav\oembed\Oembed;

/**
 * BrokenUrlNotify job class.
 *
 * @since 1.2.6
 */
class BrokenUrlNotify extends BaseJob
{
    public $url = null;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        $email = Oembed::getInstance()->getSettings()->notificationEmail ?? false;
        $subject = Craft::$app->getSystemName() . ' :: oEmbed detected broken URL';

        if (!$email) {
            $email = \craft\helpers\App::mailSettings()->fromEmail;
        }

        if (!$email || !$this->url) {
            return;
        }

        Craft::$app
            ->getMailer()
            ->compose()
            ->setTo($email)
            ->setSubject($subject)
            ->setHtmlBody('The following URL is invalid: '.$this->url)
            ->send();
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('app', 'Send notification of broken URL');
    }

}
