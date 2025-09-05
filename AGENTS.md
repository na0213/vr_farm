# Repository Guidelines

## Project Structure & Module Organization
- `app/`: Laravel domain code (Models, `Http/Controllers`, Policies, Jobs).
- `routes/`: HTTP entry points (`web.php`, `api.php`).
- `resources/`: Views and frontend (`views/`, `js/`, `css/`) built via Vite + Tailwind.
- `database/`: `migrations/`, `seeders/`, `factories/` for schema and test data.
- `tests/`: PHPUnit tests (`Feature/`, `Unit/`). Config in `phpunit.xml`.
- `public/`: Web root; built assets land in `public/build`.

## Build, Test, and Development Commands
- `composer install` / `npm install`: Install PHP and JS dependencies.
- `cp .env.example .env && php artisan key:generate`: Bootstrap app key.
- `php artisan migrate`: Apply database migrations.
- `php artisan serve`: Run locally at http://127.0.0.1:8000.
- `npm run dev`: Vite dev server with HMR.
- `npm run build`: Production build to `public/build`.
- `php artisan test` or `./vendor/bin/phpunit`: Run tests.
- `./vendor/bin/pint`: Format PHP code (PSR-12).

## Coding Style & Naming Conventions
- PHP: PSR-12 via Pint; 4-space indentation.
- Classes: StudlyCase (e.g., `app/Models/FarmPlot.php`). Methods/vars: camelCase; const: UPPER_SNAKE_CASE.
- Controllers end with `Controller`; form requests end with `Request`.
- Routes: kebab-case URIs; route names use dot.notation (e.g., `plots.show`).
- Blade components and view files use snake-case (e.g., `resources/views/plots/index.blade.php`).

## Testing Guidelines
- Framework: PHPUnit with Laravel test helpers.
- Location: `tests/Feature` for HTTP flows; `tests/Unit` for pure logic.
- Naming: `*Test.php` (e.g., `UserLoginTest.php`).
- Data: Use factories/seeders; prefer `RefreshDatabase` where safe.
- Run: `php artisan test` locally before opening a PR.

## Commit & Pull Request Guidelines
- Commits: Prefer Conventional Commits (e.g., `feat:`, `fix:`, `chore:`) with imperative tone.
- Branches: `feature/short-topic`, `fix/issue-123`.
- PRs: clear description, linked issues, repro steps; screenshots for UI changes.
- Checks: tests pass, Pint formatted, Vite build succeeds; no secrets or `.env` in diffs.

## Security & Configuration Tips
- Never commit secrets. Configure `.env` (set `APP_URL`, `DB_*`).
- `php artisan storage:link` to expose user uploads.
- Restrict `public/` write paths at deploy; ensure correct file permissions.

## Design Agent Guidelines

### Goals
- Make the UI clean, consistent, and modern using Tailwind.
- Establish a clear typography scale, spacing rhythm, and minimal color palette.
- Componentize repeated patterns (Card, Button, Section, Heading, Container).
- Improve accessibility (semantic HTML, focus states, contrast).

### Constraints
- No visual regressions: show diffs and ask approval before large edits.
- Keep dependencies minimal; avoid new UI libs unless approved.
- Follow existing brand hints: primary `#3be7ed`, surfaces `#f6eeff` / `#ffe6e6`.

### Review Checklist
- Typography: heading hierarchy, line-height, letter-spacing.
- Spacing: consistent vertical rhythm between sections.
- Color: limited palette, accessible contrast.
- Components: reusable, documented props.
- Responsiveness: `md`/`lg` breakpoints well-defined.
- A11y: roles, aria-labels, alt text, focus-visible.
