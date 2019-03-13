# oEmbed plugin for Craft CMS 3.x

![oEmbed](resources/img/plugin-logo.png)

A simple plugin to extract media information from websites, like youtube videos, twitter statuses or blog articles.

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

To use simply call one of the following methods on your field type

    {{ entry.field.embed }} 
    {{ entry.field.render }}
    {{ entry.field.media }}
    
We also provide option to use as a Twig variable

    {{ craft.oembed.render(url, options) }}
    
You can access additional media details using the examples below.

    entry.field.media.title
    entry.field.media.description
    entry.field.media.url
    entry.field.media.type
    entry.field.media.tags
    entry.field.media.images
    entry.field.media.image
    entry.field.media.imageWidth
    entry.field.media.imageHeight
    entry.field.media.code
    entry.field.media.width
    entry.field.media.height
    entry.field.media.aspectRatio
    entry.field.media.authorName
    entry.field.media.authorUrl
    entry.field.media.providerName
    entry.field.media.providerUrl
    entry.field.media.providerIcons
    entry.field.media.providerIcon
    entry.field.media.publishedDate
    entry.field.media.license
    entry.field.media.linkedData
    entry.field.media.feeds

Additional Essense information can be found [here](https://github.com/essence/essence)

## Credits

Original built while working at [HutSix](https://hutsix.com.au/) I've since been granted permission to continue development here.

## Change Log

Changes can be viewed [here](https://github.com/wrav/oembed/blob/master/CHANGELOG.md)

## Support

Get in touch via email or by [creating a Github issue](/wrav/oembed/issues)
