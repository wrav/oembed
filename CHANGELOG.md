# oEmbed Changelog

## 1.3.17 - 2022-06-24

### Update
- Fix issue with composer and packagist version syncing. Thanks @juban

## 1.3.15 - 2022-03-09

### Update
- Fix for issue #101, where the plugin is unable to set a webpage as a valid source URL. Thanks @juban

## 1.3.13 - 2021-10-21

### Added
- Add referrer to Embed for videos with privacy restrictions.

### Update
- Changes to the handle of FallbackAdapter with no URL.
- Preview changes

## 1.3.12 - 2021-08-05

### Added
- Fix issue with blank URLs where fallback adapter sets URL to '/'. This also fixes #88 issue.

## 1.3.11 - 2021-07-21

### Added
- Added fallback adapter for invalid URLs and PHP 8 issues, thanks [@gglnx](https://github.com/gglnx). This also fixes #81, #76 issues.

## 1.3.10 - 2021-07-20

### Updated
- Fixes issue with caching key, thanks [@mijewe](https://github.com/mijewe).
- Fixes to 'class@anonymous' handling, this should help resolve issues [#81](https://github.com/wrav/oembed/issues/81), [#50](https://github.com/wrav/oembed/issues/50), [#10](https://github.com/wrav/oembed/issues/10).
- Updated CHANGELOG dates.

## 1.3.9 - 2021-06-16

### Updated
- Fixes issue with `params` option not applying if query / GET params not yet set, this resolves issues [#53](https://github.com/wrav/oembed/issues/53).

## 1.3.8 - 2021-06-15

### Updated
- Fixes issue with code changes due to PHP namespacing.
- Updates to caching key to support embed options, this resolves issues [#73](https://github.com/wrav/oembed/issues/73). 

## 1.3.7 - 2021-06-15

### Updated
- Updates to OembedModel / GraphQL bugfixes, this resolves issues [#74](https://github.com/wrav/oembed/issues/74) and PR [#75](https://github.com/wrav/oembed/issues/75). Thank you to @joshuabaker, @denisyilmaz and anyone else I missed. 
- Clean up bugfixes changes

## 1.3.6 - 2020-10-28

### Added
- Added new render option called `attributes` to allow custom attributes to bet added to iframe element, this resolves issue ([#51](https://github.com/wrav/oembed/issues/51)).

## 1.3.5 - 2020-10-28

### Updated
- Add settings-input for Facebook/Instagram access token. Thanks Floris aka @FlorisDerks

## 1.3.4 - 2020-06-29

### Updated
- Fix to normalizeValue function on GraphQL field's `__get()` magic method. Thanks @joshuabaker

## 1.3.3 - 2020-06-29

### Updated
- Fix to GraphQL bug caused by PHP NULL coalescing operator from issue ([#46](https://github.com/wrav/oembed/issues/46)).

## 1.3.2 - 2020-06-05

### Updated
- Fixed GraphQL bug `Internal Server Error: GraphQL fails to get oembed fields` ([#46](https://github.com/wrav/oembed/issues/46)).

## 1.3.1 - 2020-05-08

### Added
- *(NEW FEATURE)* Added GDPR setting to transform URL's for Youtube and Vimeo to their GDPR versions. This happen's without needing to change existing URL's.

## 1.3.0 - 2020-03-05

### Added
- *(NEW FEATURE)* Added setting to hide preview iframe in the new a collapsable UI component.
- *(NEW FEATURE)* Added notification by email for broken URLs which can be set up in plugin settings. This feature is still under development and new feature's like Slack, Microsoft Team notification channels will be added including support to update the notify message copy.

### Updated
- Preview iframe is now rendered into a collapsable component to save space.
- Add `overflow:hidden` to iframe to prevent overflow ([#37](https://github.com/wrav/oembed/issues/37)).

## 1.2.5 - 2020-02-10

### Updated
- Allow users to set width and height on the iframe ([#35](https://github.com/wrav/oembed/issues/35)).

## 1.2.4 - 2020-02-05

### Updated
- Removed package dependacy to get PHP 7.0-compatible ([#33](https://github.com/wrav/oembed/issues/33))

## 1.2.3 - 2020-02-05

### Fixed
- Fixed "Upgrading to 1.2.0+ disables admin UI ([#32](https://github.com/wrav/oembed/issues/32))" bug caused by LibXML rendering self closing `iframe`.

## 1.2.2 - 2020-01-30

### Updated
- Updated README with usage of new feature.

### Added
- *(NEW FEATURE)* Add new `params` option to allow you to set missing URL query params that are supported by the providers oembed protocol. ([#24](https://github.com/wrav/oembed/issues/24) & [#30](https://github.com/wrav/oembed/issues/30))

## 1.2.1 - 2020-01-19

### Updated
- Re-add `rel` feature missing due to merge conflict. ([#24](https://github.com/wrav/oembed/issues/24))

## 1.2.0 - 2020-01-19

### Updated
- Support `rel` URL propety via DOM manipulation. ([#24](https://github.com/wrav/oembed/issues/24))
- Added GraphQL support. ([#25](https://github.com/wrav/oembed/issues/25))
- Updated docs

### Fixed
- Fixed "Undefined index: autoplay" warnings. ([#26](https://github.com/wrav/oembed/issues/26))

## 1.1.8 - 2019-09-16

### Updated
- Support Youtube and Vimeo `autoplay`, `loop` and `autopause` embed feature via DOM manipulation. ([#14](https://github.com/wrav/oembed/issues/14))

### Fixed
- Array to string conversion bug due to lack of a recursive function. ([#17](https://github.com/wrav/oembed/issues/17))

## 1.1.7 - 2019-09-16

### Updated
- Support custom control panel (`cpTrigger`) configurations.

## 1.1.6 - 2019-04-22

### Updated
- Caching is now a field you enable in new settings area.

## 1.1.5 - 2019-04-15

### Updated
- Updated to prevent <script/> rendering outsite `/admin/entries`, with support if `admin` isn't the CP URL trigger.

## 1.1.4 - 2019-04-04

### Updated
- Updated to allow better support for sites which don't quite meet the oEmbed format requirements. 

## 1.1.3 - 2019-03-29

### Added
- Added `valid` method to handle errors gracefully (Thanks @iparr). 
- Added data caching for parsed URLs to help increase page response time. 

### Updated
- Updated docs. 

## 1.1.2 - 2019-02-18

### Updated
- Version bump.

## 1.1.1 - 2019-02-18

### Updated
- Fix bug in field type rendering.

## 1.1.0 - 2019-03-13
> {note} The plugin’s dependence has changed from `essence/essence` to `embed/embed`. After updating to oEmbed 1.1.0 or later, make sure you reference to the README and test your site for possible missing/ renamed twig object keys.

### Updated
- Updated composer package `essence/essence` to `embed/embed` to handle more edge case URLs.

## 1.0.5 - 2019-02-08

### Updated
- Fix bug where field is in Matrix field and the AJAX event action isn't bound / fired until after entry initial saved

## 1.0.4 - 2019-01-17

### Updated
- Allowing support for CraftCMS v3.1

## 1.0.3 - 2018-12-07

### Updated
- Prevent JS asset rendering on frontend
- Revert javascript to use jQuery

## 1.0.2 - 2018-12-06

### Updated
- Refactored javascript to use native JS over jQuery

## 1.0.1 - 2018-11-26

### Updated
- Changed preview controller action access to prevent anonymous access
- Refactored the preview action to use a template with wrapper to allow for future styling and updates

## 1.0.0 - 2018-11-18

### Added
- Initial release and migration from previous project
