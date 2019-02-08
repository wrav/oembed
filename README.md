# oEmbed plugin for Craft CMS 3.x

![oEmbed](resources/img/plugin-logo.png)

A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.

[![Beerpay](https://beerpay.io/wrav/oembed/badge.svg)](https://beerpay.io/wrav/oembed)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

If use are looking for CraftCMS 2.5 support use previous project [version 1.0.4](https://github.com/hut6/oembed/tree/1.0.4) 
which is the latest release for CraftCMS 2.5.

## Installing

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require wrav/oembed

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for oEmbed.

## Using oEmbed

To use simply call the `embed` method on your field type

    {{ entry.field.render }}
    
We also provide option to use as a Twig variable

    {{ craft.oembed.render(url, options) }}
    
You can access additional media details using the examples below.

    entry.field.media.type
    entry.field.media.version
    entry.field.media.url
    entry.field.media.title
    entry.field.media.description
    entry.field.media.authorName
    entry.field.media.authorUrl
    entry.field.media.providerName
    entry.field.media.providerUrl
    entry.field.media.cacheAge
    entry.field.media.thumbnailUrl
    entry.field.media.thumbnailWidth
    entry.field.media.thumbnailHeight
    entry.field.media.html
    entry.field.media.width
    entry.field.media.height
    
Additional Essense information can be found [here](https://github.com/essence/essence)

## Credits

Original built while working at [HutSix](https://hutsix.com.au/) I've since been granted permission to continue development here.

## Change Log

Changes can be viewed [here](https://github.com/wrav/oembed/blob/master/CHANGELOG.md)

## Support

Get in touch via email or by [creating a Github issue](/wrav/oembed/issues)
