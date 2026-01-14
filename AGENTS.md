# Repository Guidelines

## Project Structure & Module Organization
- `src/` holds the Craft CMS plugin code (services, fields, models, adapters, GraphQL, console controllers).
- `src/templates/` contains Craft CP templates for settings/preview.
- `resources/` and `cpresources/` store plugin assets (images, static resources).
- `tests/` contains Codeception suites, Craft fixtures, and helpers.
- `docker-compose.yml` defines the local dev/test environment.

## Build, Test, and Development Commands
All development and testing runs inside Docker.
- `docker compose up -d` starts the dev containers.
- `docker exec app composer install` installs PHP dependencies in the container.
- `docker exec -it app sh` opens a shell in the container.
- `docker exec app vendor/bin/codecept run` runs the full Codeception suite.
- `docker exec app vendor/bin/codecept run tests/unit/embeds/` runs embed unit tests.
- `docker exec app sh -c "XDEBUG_MODE=coverage vendor/bin/codecept run --coverage"` runs with coverage.

## Coding Style & Naming Conventions
- PHP follows the existing style in `src/`: 4-space indentation, PSR-style namespaces, and StudlyCaps class names.
- Methods and variables use `camelCase`; constants use `UPPER_SNAKE_CASE`.
- Prefer small, focused methods in services and adapters; keep Craft-specific logic in plugin/field classes.

## Testing Guidelines
- Framework: Codeception (see `tests/` suites).
- Add unit tests in `tests/unit/` and integration/functional tests in `tests/integration/` or `tests/functional/`.
- Name tests after the feature or provider (e.g., `tests/unit/embeds/VimeoTest.php`).
- For full coverage, copy `tests/.env.example` to `tests/.env` and add provider API tokens.

## Commit & Pull Request Guidelines
- Commit messages follow a lightweight convention like `type: summary` (e.g., `fix:`, `test:`, `docs:`, `chore:`, `ci:`, `refactor:`).
- Keep commits focused and scoped; update `CHANGELOG.md` for release-related changes.
- Pull requests should include a clear description, test results, and any required setup notes (e.g., `.env` tokens or Docker steps).

## Security & Configuration Tips
- Keep API tokens out of version control; use `tests/.env`.
- Docker is required for reliable test execution and the PostgreSQL-backed Craft test environment.
