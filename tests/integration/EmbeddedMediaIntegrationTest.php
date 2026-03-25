<?php

namespace wrav\oembed\tests\integration;

use Codeception\Test\Unit;
use Craft;
use craft\elements\Entry;
use craft\enums\PropagationMethod;
use craft\fieldlayoutelements\CustomField;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use wrav\oembed\fields\OembedField;
use wrav\oembed\Oembed;
use wrav\oembed\services\OembedService;

class EmbeddedMediaIntegrationTest extends Unit
{
    public function testEmbeddedMediaTemplateRendersWithEntryField(): void
    {
        $suffix = strtolower(preg_replace('/[^a-z0-9]/', '', Craft::$app->security->generateRandomString(6)));
        if ($suffix === '') {
            $suffix = 'test';
        }

        $entriesService = Craft::$app->getEntries();
        $fieldsService = Craft::$app->getFields();
        $sitesService = Craft::$app->getSites();

        $field = new OembedField([
            'name' => 'Media Url ' . $suffix,
            'handle' => 'mediaUrl' . $suffix,
        ]);
        $this->assertTrue($fieldsService->saveField($field));

        $layout = new FieldLayout([
            'type' => Entry::class,
        ]);
        $tab = new FieldLayoutTab([
            'name' => 'Content',
            'layout' => $layout,
        ]);
        $tab->setElements([new CustomField($field)]);
        $layout->setTabs([$tab]);
        $this->assertTrue($fieldsService->saveLayout($layout));

        $entryType = new EntryType([
            'name' => 'Embed Entry ' . $suffix,
            'handle' => 'embedEntry' . $suffix,
        ]);
        $entryType->setFieldLayout($layout);
        $this->assertTrue($entriesService->saveEntryType($entryType));

        $site = $sitesService->getPrimarySite();
        $section = new Section([
            'name' => 'Embed Section ' . $suffix,
            'handle' => 'embedSection' . $suffix,
            'type' => Section::TYPE_CHANNEL,
            'enableVersioning' => false,
            'propagationMethod' => PropagationMethod::All,
        ]);
        $section->setEntryTypes([$entryType]);
        $section->setSiteSettings([
            $site->id => new Section_SiteSettings([
                'siteId' => $site->id,
                'enabledByDefault' => true,
                'hasUrls' => false,
                'uriFormat' => null,
                'template' => null,
            ]),
        ]);
        $this->assertTrue($entriesService->saveSection($section));

        $originalService = Oembed::getInstance()->oembedService;
        $stubService = new class extends OembedService {
            public function embed($url, array $options = [], array $cacheProps = [], $factories = [])
            {
                return (object)['code' => '<iframe src="' . $url . '"></iframe>'];
            }
        };
        Oembed::getInstance()->set('oembedService', $stubService);

        try {
            $urls = [
                'https://vimeo.com/123456',
                'https://www.facebook.com/watch/?v=987654321',
            ];

            foreach ($urls as $url) {
                $entry = new Entry();
                $entry->sectionId = $section->id;
                $entry->typeId = $entryType->id;
                $entry->siteId = $site->id;
                $entry->title = 'Embed Test ' . $suffix;
                $entry->slug = 'embed-test-' . $suffix;
                $entry->setFieldValue($field->handle, $url);
                $this->assertTrue(Craft::$app->getElements()->saveElement($entry));

                $view = Craft::$app->getView();
                $view->setTemplatesPath(CRAFT_TEMPLATES_PATH);
                $rendered = $view->renderTemplate('embedded-media', [
                    'entry' => $entry,
                    'mediaFieldHandle' => $field->handle,
                ]);

                $this->assertStringContainsString('<iframe src="' . $url . '"></iframe>', $rendered);
            }
        } finally {
            Oembed::getInstance()->set('oembedService', $originalService);
        }
    }
}
