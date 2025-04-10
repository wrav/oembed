# oEmbed Changelog

## 3.1.4 - 2025-03-06

### Update

- Allow oEmbed code that includes an iframe to render all of it's HTML instead of just the iframe

## 3.1.3 - 2024-12-10

### Update

- Passing the option & cache variables to the anonymous function for parsing tags. Thanks @JeroenJRP

## 3.1.2 - 2024-12-09

### Added

- New parser to output embeds added in CKEditor using it's Media embed feature. Thanks @JeroenJRP

## 3.1.1 - 2024-09-09

### Update

- Removed Assets from console. Relates to [#158](https://github.com/wrav/oembed/issues/158). Thanks @jeffreyzant

## 3.1.0 - 2024-09-09

### Added

- Added support for adapter extractor support to allow custom site handling

### Update

- Refactor EmbedAdapter to handle extractor object to support better fallback for some websites

## 3.0.9 - 2024-07-26

### Update

- Resolves error when querying an entry with a empty oEmbed URL. Thanks to @jaapfaes for PR #157

## 3.0.8 - 2024-02-26

### Update

- Resolved GraphQL issues. Special thanks to @davidwebca 

## 3.0.7 - 2023-12-15

### Update

- Resolve YT loop issues. Reported by @holiday-jan in issue #146. Thanks @holiday-jan

## 3.0.6 - 2023-12-15

### Update

- Fixed issue with src attribute not being set on iframe. Reported by @holiday-jan in issue #146. Thanks @holiday-jan
- Rework preview handling
- Updated README docs
- Fallback .code for certain providers, this completely blocked GraphQL users. Reported by @yoannisj in issue #129. Thanks @yoannisj

## 3.0.5 - 2023-12-08

### Update

- Modified handling of empty embedded media codes

## 3.0.4 - 2023-12-07

### Update

- Adds version to cache key to prevent cache key collisions.

## 3.0.3 - 2023-12-04

### Update

- Add GDPR compliance with CookieBot settings and functionality. Reported by @joepagan in issue #131. Thanks @joepagan and @iparr for discussion and logging.

## 3.0.2 - 2023-12-01

### Update

- Add data mapping and fallback for image properties. Reported by @maxfenton in issue #143 and @helleholmsen in issue #145. Thanks @maxfenton and @helleholmsen

## 3.0.1 - 2023-11-17

### Update

- Handle all exceptions in OembedService to prevent edge case errors. Reported by @holiday-jan in issue #141. Thanks @holiday-jan

## 3.0.0 - 2023-11-04

### Update

- Update embed/embed to v4 to allow for support with newer plugins.

## 2.3.1 - 2023-10-11

### Update

- Added fix for issue #138, caused by static return type on the JobInterface. Thanks @laurabennett
- Added @sgtpenguin 's PR to avoid GraphQL bug by defaulting the height and width to NULL for the fallback adapter. Thanks @sgtpenguin

## 2.3.0 - 2023-02-02

### Update

- Added default caching keys to resolve missing field data while cached

## 2.2.2 - 2022-08-26

### Update

- Fix Craft 4 type compatibility. THANKS @raymondelooff

## 2.2.1 - 2022-06-30

### Update

- Fix a side effect of #101 fix where the preview and embed will fallback to the default Craft site if no or invalid URL is provided (@juban)

## 2.2.0 - 2022-06-30

### Update

- Converting DOMDocument to JSON and back to StdClass due to error "Serialization of 'DOMDocument' is not allowed" with caching in PHP 8.
- Fixed `required` option not applying

## 2.1.0 - 2022-06-29

### Added

- Added `ext-dom` requirement

### Updated

- Fix for issue #101 for the v2 version track for Craft v4. Thanks again @juban

## 2.0.0 - 2022-06-18

### Updated

- Updated plugin to support Craft v4
- Removed requirement for Craft v3 and requiremnt is now PHP to >=8.0.2

## 1.3.15 - 2022-03-09

### Updated

- Fix for issue #101, where the plugin is unable to set a webpage as a valid source URL. Thanks @juban

## 1.3.13 - 2021-10-21

### Added

- Add referrer to Embed for videos with privacy restrictions.

### Updated

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
