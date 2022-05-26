# oEmbed plugin for Craft CMS 3.x

<p align="center">
   <img src="resources/img/plugin-logo.png" alt="oEmbed" />
</p>

<p align="center">
   <img src="https://img.shields.io/badge/license-MIT-green" />
   <a href="https://github.com/wrav/oembed/releases" alt="Releases">
   <img src="https://img.shields.io/github/v/release/wrav/oembed"></a>
   <a href="https://github.com/reganlawton" alt="Maintainer">
      <img src="https://img.shields.io/badge/maintainer-reganlawton-blue" /></a>
   <a href="https://github.com/badges/shields/graphs/contributors" alt="Contributors">
      <img src="https://img.shields.io/github/contributors/wrav/oembed" /></a>
   <a href="https://github.com/wrav/oembed/pulse" alt="Activity">
      <img src="https://img.shields.io/github/commit-activity/y/wrav/oembed" /></a>  
   <a href="https://github.com/wrav/oembed/issues" alt="Issues">
      <img src="https://img.shields.io/github/issues-raw/wrav/oembed" /></a>
   <a href="https://packagist.org/packages/wrav/oembed" alt="Downloads">
      <img src="https://img.shields.io/packagist/dt/wrav/oembed" /></a>   
   
</p>


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

    {{ entry.field.valid }} # Get the embed object
    {{ entry.field.render }} # Renders HTML
    {{ entry.field.embed }} # Get the embed object
    {{ entry.field.media }} # Get the embed object
    
We also provide option to use as a Twig variable

    {{ craft.oembed.valid(url, options) }}
    {{ craft.oembed.render(url, options) }}
    {% set embed = craft.oembed.embed(url, options) %}
    {% set media = craft.oembed.media(url, options) %}
    
Updating the embed URL, such as autoplay, rel, mute paramaters. This allows for you to support features the provider might not yet support

    {{ 
        entry.oembed_field.render({
            params: {
                autoplay: 1,
                rel: 0,
                mute: 0,
                loop: 1,
                autopause: 1,
            },
            attributes: {
                title: 'Main title',
                'data-title': 'Some other title',
            }
        }) 
    }}
    
Updating the width & height attributes on the iframe can be done using the following method, however is CSS is still recommended view for sizing your iframe.

    {{ 
        entry.oembed_field.render({
            width: 640,
            height: 480,
        }) 
    }}
    
or
    
    {{ 
        entry.oembed_field.render({
            attributes: {
                width: 640,
                height: 480,
            }
        }) 
    }}
    
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

Additional Embed information can be found [here](https://github.com/oscarotero/Embed)

## GraphQL

I recommend enabling caching in the plugin settings menu to speed up the API resolve timing.

Below is an example of a Oembed field called "foobar" add accessing properties from the embed object.

```
{
  entries {
    id,
    ... on page_page_Entry {
      foobar {
        code,
        providerUrl,
        aspectRatio
      }
    }
  }
}
```

## Credits

Original built while working at [HutSix](https://hutsix.com.au/) I've since been granted permission to continue development here.

## Change Log

Changes can be viewed [here](https://github.com/wrav/oembed/blob/master/CHANGELOG.md)

## Support

Get in touch via email, Discord, or by [creating a Github issue](/wrav/oembed/issues)
