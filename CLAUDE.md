# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Craft CMS plugin called "oEmbed" that extracts media information from websites like YouTube videos, Twitter statuses, and blog articles. The plugin provides field types, GraphQL support, caching, and GDPR compliance features.

## Development Commands

**IMPORTANT**: All development and testing must be done using Docker. The project requires a PostgreSQL database and specific environment setup that is only available through the Docker containers.

### Docker Setup (Required)
1. Start development environment: `docker compose up -d`
2. Verify containers are running: `docker ps`
3. Copy test environment: `cp tests/.env.example tests/.env`
4. Configure API tokens in `.env` for Meta, Twitter, etc. for full test coverage

### Testing (Docker Required)
- Run all tests: `docker exec app vendor/bin/codecept run`
- Run specific test file: `docker exec app vendor/bin/codecept run {your_file}`
- Run with coverage: `docker exec app sh -c "XDEBUG_MODE=coverage vendor/bin/codecept run --coverage"`
- Run embed-specific tests: `docker exec app vendor/bin/codecept run tests/unit/embeds/`

### Development Shell Access
- Access container shell: `docker exec -it app sh`
- Run commands inside container: `docker exec app [command]`

### Common Development Tasks
- Install dependencies: `docker exec app composer install`
- Check logs: `docker logs app`
- Stop environment: `docker compose down`

## Code Architecture

### Core Components

#### Main Plugin Class
- `src/Oembed.php` - Main plugin class that registers services, fields, variables, and event handlers

#### Service Layer
- `src/services/OembedService.php` - Core service handling URL parsing, embed extraction, caching, and GDPR compliance
  - Uses the `embed/embed` package for oEmbed protocol support
  - Handles YouTube, Vimeo, and other provider-specific logic
  - Implements fallback adapters for unsupported URLs
  - Manages DOM manipulation for iframe customization

#### Field Implementation
- `src/fields/OembedField.php` - Custom Craft CMS field type for oEmbed URLs
  - Provides CP interface with preview functionality
  - GraphQL support with configurable options
  - URL validation and normalization

#### Models
- `src/models/OembedModel.php` - Data model for oEmbed field values
  - Lazy loading of embed data
  - Template-friendly methods (render, embed, media, valid)
- `src/models/Settings.php` - Plugin settings model

#### Adapters
- `src/adapters/EmbedAdapter.php` - Primary adapter using embed/embed library
- `src/adapters/FallbackAdapter.php` - Fallback for unsupported URLs

### Key Features

#### Caching System
- Configurable caching with different durations for successful/failed requests
- Custom cache key props for additional field caching
- Default cached properties include title, description, code, dimensions, provider info

#### GDPR Compliance
- YouTube no-cookie domain switching
- Vimeo DNT parameter support
- CookieBot integration attributes
- Configurable privacy settings

#### URL Parameter Management
- Support for autoplay, loop, mute, rel parameters
- YouTube playlist parameter handling for loops
- Custom iframe attributes via options

### GraphQL Integration
- `src/gql/OembedFieldResolver.php` - GraphQL field resolver
- `src/gql/OembedFieldTypeGenerator.php` - GraphQL type generator
- Supports JSON-encoded options and cache props arguments

### Template Usage Patterns
The plugin supports multiple template access methods:
- Field methods: `entry.field.render()`, `entry.field.embed()`, `entry.field.media()`
- Variable methods: `craft.oembed.render(url, options, cacheFields)`
- Options support both legacy format and new params/attributes structure

### Testing Structure
- Uses Codeception framework with Craft CMS testing module
- Functional tests in `tests/functional/`
- Unit tests in `tests/unit/` including provider-specific tests
- Coverage reports generated in `tests/_output/coverage/`

## Important Notes

- Plugin supports Craft CMS 4.0+ and 5.0+
- Requires PHP 8.2+
- Uses `embed/embed` v4.4+ for oEmbed protocol support
- Many providers require embed URLs rather than standard URLs for proper oEmbed data
- Failed requests are cached for shorter duration (15 minutes vs 1 hour)