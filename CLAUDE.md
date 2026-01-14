# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Craft CMS plugin that extracts media information from websites using the oEmbed protocol. Provides field types, GraphQL support, caching, and GDPR compliance features.

- Supports Craft CMS 4.0+ and 5.0+
- Requires PHP 8.2+
- Uses `embed/embed` v4.4+ for oEmbed protocol

## Development Commands

**All development and testing requires Docker** (PostgreSQL-backed Craft test environment).

```bash
# Setup
docker compose up -d
cp tests/.env.example tests/.env  # Add API tokens for full coverage

# Testing
docker exec app vendor/bin/codecept run                    # All tests
docker exec app vendor/bin/codecept run tests/unit/embeds/ # Embed tests
docker exec app vendor/bin/codecept run {path/to/test}     # Single test
docker exec app sh -c "XDEBUG_MODE=coverage vendor/bin/codecept run --coverage"

# Shell access
docker exec -it app sh
docker exec app composer install
```

## Code Architecture

### Request Flow
1. URL enters via `OembedField` or `craft.oembed` Twig variable
2. `OembedService` checks cache, then delegates to adapters
3. `EmbedAdapter` uses `embed/embed` library; `FallbackAdapter` handles unsupported URLs
4. `OembedModel` provides lazy-loaded template methods (`render`, `embed`, `media`, `valid`)

### Core Components
- `src/Oembed.php` - Main plugin class, registers services/fields/events
- `src/services/OembedService.php` - URL parsing, embed extraction, caching, GDPR, DOM manipulation
- `src/fields/OembedField.php` - Craft field type with CP preview and GraphQL support
- `src/models/OembedModel.php` - Data model with template-friendly methods
- `src/adapters/` - `EmbedAdapter` (primary) and `FallbackAdapter` (fallback)
- `src/gql/` - GraphQL resolver and type generator

### Key Behaviors
- **Caching**: Successful requests cached 1 hour, failed 15 minutes
- **GDPR**: YouTube no-cookie domain, Vimeo DNT, CookieBot attributes
- **URL Params**: autoplay, loop, mute, rel supported via options
- **Many providers require embed URLs** (e.g., player.vimeo.com) not standard URLs

### Testing
- Codeception framework with unit (`tests/unit/`) and functional (`tests/functional/`) suites
- Provider-specific tests in `tests/unit/embeds/`
- Name tests after feature/provider (e.g., `VimeoTest.php`)

## Code Style

- PSR-4 namespaces under `wrav\oembed\`
- 4-space indentation, `camelCase` methods/variables, `UPPER_SNAKE_CASE` constants
- Keep Craft-specific logic in plugin/field classes; services handle business logic

## Commit Messages

Use format: `type: summary` (e.g., `fix:`, `test:`, `docs:`, `chore:`, `ci:`, `refactor:`)