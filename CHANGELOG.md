# oEmbed Changelog

## 1.2.0
- Added Field Preivews (by boscho87) #15
- Added translations

## 1.1.2
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
