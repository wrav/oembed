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

## Seeking a New Maintainer

It's been nearly nine years since I released the first version of Oembed. While it's been an incredible journey, I no longer have the time or energy to actively maintain the project. Since 2019, I've been deeply involved in building a data and analytics startup, which has grown significantly and now demands most of my focus.

To keep this library alive and thriving, I'm looking for someone passionate about its future to take over its maintenance and development. If you're interested, please reach out by opening an issue or contacting me directly.

In the meantime, I’ll continue merging pull requests from the community to ensure the project doesn’t stagnate, though I won’t be actively contributing new features or updates. Thank you for your support over the years!

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Versions

| Version | CraftCMS Version       | Embed Version | PHP Version   | Branch                          | Status        |
|---------|------------------------|---------------|---------------|---------------------------------|---------------|
| v1      | ^3.0.0-beta.23          | ^3.3          | >=7.2.5       | [v1](https://github.com/wrav/oembed/tree/v1) | Discontinued  |
| v2      | ^4.0                    | ^3.3          | ^8.0.2        | [v2](https://github.com/wrav/oembed/tree/v2) | Discontinued  |
| v3      | ^3.0 \| ^4.0 \| ^5.0    | ^v4.4         | ^8.2          | [v3](https://github.com/wrav/oembed/tree/v3) | Active        |
| dev-v3-php74-support | ^3.0 \| ^4.0 \| ^5.0 | ^v4.4         | ^7.4          | [dev-v3-php74-support](https://github.com/wrav/oembed/tree/dev-v3-php74-support) | Active (PHP 7.4 Support) |

## Quick FYI on URL issues

Many websites, such as Vimeo, are restricting access to standard URLs (e.g. www.vimeo.com) and sometimes require using the embed URL (e.g., player.vimeo.com) to retrieve oEmbed data.

You can typically find the embed URL through the "share" options on video and oEmbed-supported sites. While the plugin provides general rich content as a fallback, its true potential comes from utilizing the oEmbed protocol and the associated metadata from the content provider.

If you're experiencing issues, check whether the provider or page offers an embed version, and use that URL to ensure you're accessing the most accurate data.

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

    {{ craft.oembed.valid(url, options, cacheFields) }}
    {{ craft.oembed.render(url, options, cacheFields) }}
    {% set embed = craft.oembed.embed(url, options, cacheFields) %}
    {% set media = craft.oembed.media(url, options, cacheFields) %}
    
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
    
We still support hte old legacy method, however this might be deprecated in future versions.

    {{ 
        entry.oembed_field.render({
             autoplay: 1,
             rel: 0,
             mute: 0,
             loop: 1,
             autopause: 1,
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
    
You can access additional media details using the examples below, these are the default keys.

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

You can access additional media details from the data array. These will be snake_case so be aware.

   {{ dump(entry.field.media.data) }}

Additional Embed information can be found [here](https://github.com/oscarotero/Embed)

## Cache

By default, the plugin will cache the following keys on the oembed object. The plugin can cache additional missing fields using the cache prop parameter which will take an array of strings.

   {{ 
      entry.oembed_field.render(
         {
            width: 640,
            height: 480,
         }, 
         [
            'cacheable_key'
         ]
      ) 
   }}

### Default Keys

- title
- description
- url
- type
- tags
- images
- image
- imageWidth
- imageHeight
- code
- width
- height
- aspectRatio
- authorName
- authorUrl
- providerName
- providerUrl
- providerIcons
- providerIcon
- publishedDate
- license
- linkedData
- feeds

## Cookie Management

The plugin includes automatic cookie file cleanup to prevent server storage issues from accumulating embed-cookie files created by the underlying `embed/embed` library.

### Settings

Cookie cleanup can be configured in the plugin settings:

- **Enable Cookie Cleanup** (default: `true`) - Enable/disable automatic cookie file cleanup
- **Cookie Max Age** (default: `86400` seconds / 24 hours) - Maximum age of cookie files before cleanup (minimum: 300 seconds)
- **Cookies Path** (optional) - Custom directory path for cookie files (uses system temp directory if empty)

### Manual Cleanup Commands

You can manually manage cookie files using the console commands:

```bash
# Clean up old cookie files
php craft oembed/cookie/cleanup

# Get information about cookie files
php craft oembed/cookie/info
```

### Automatic Cleanup

Cookie cleanup runs automatically on plugin initialization with built-in throttling to prevent performance impact:

- Cleanup runs at most once per hour
- Only processes files older than the configured `cookieMaxAge`
- Only removes files matching the pattern `embed-cookies-*`
- Preserves non-cookie files and recently created files

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

## Testing

This project uses Codeception and Docker Compose (Locally) and I would strongly ask for unit tests, however I understand sometimes this may not be needed.

***NOTE:*** If your wanting to run the all project tests you'll need to set up the required Meta, Twitter, etc API tokens in the `.env` file.

```bash
# Setting ENV for testing and edit
cp tests/.env.example tests/.env

# Spin up docker
docker compose up -d

# Access shell 
docker exec -it app sh

# Run tests via Codeception
vendor/bin/codecept run {your_file}

# Run with Coverage
XDEBUG_MODE=coverage vendor/bin/codecept run --coverage
```

## Credits

Original built while working at [HutSix](https://hutsix.com.au/) I've since been granted permission to continue development here.

## Change Log

Changes can be viewed [here](https://github.com/wrav/oembed/blob/master/CHANGELOG.md)

## Support

Get in touch via email, Discord, or by [creating a Github issue](/wrav/oembed/issues)
