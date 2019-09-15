# oEmbed Changelog

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
