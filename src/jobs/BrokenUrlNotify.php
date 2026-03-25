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
        // Debug logging to track URL values
        Craft::info(
            'BrokenUrlNotify job executing with URL: ' . ($this->url ?? 'NULL'),
            'oembed'
        );

        $email = Oembed::getInstance()->getSettings()->notificationEmail ?? false;
        $subject = Craft::$app->getSystemName() . ' :: oEmbed detected broken URL';

        if (!$email) {
            $email = \craft\helpers\App::mailSettings()->fromEmail;
        }

        // Enhanced validation with logging
        if (!$email) {
            Craft::warning('BrokenUrlNotify: No email address configured for notifications', 'oembed');
            return;
        }

        if (!$this->url || trim($this->url) === '') {
            Craft::warning('BrokenUrlNotify: URL is empty or null - cannot send notification', 'oembed');
            return;
        }

        // Enhanced email body with better formatting
        $htmlBody = sprintf(
            '<p>The following URL is invalid and could not be processed:</p><p><strong>%s</strong></p><p>Please check the URL and try again.</p>',
            htmlspecialchars($this->url, ENT_QUOTES, 'UTF-8')
        );

        try {
            Craft::$app
                ->getMailer()
                ->compose()
                ->setTo($email)
                ->setSubject($subject)
                ->setHtmlBody($htmlBody)
                ->send();

            Craft::info('BrokenUrlNotify: Email sent successfully for URL: ' . $this->url, 'oembed');
        } catch (\Exception $e) {
            Craft::error('BrokenUrlNotify: Failed to send email - ' . $e->getMessage(), 'oembed');
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('app', 'Send notification of broken URL');
    }

}
