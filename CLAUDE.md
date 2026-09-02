# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

Micrositio for the **Órgano Interno de Control Municipal (OICM)** — a Mexican municipal internal-control office. The requirements live in `context/`:

- `context/promptinicial.md` — the client's own brief (Spanish). Read it first; it is the source of truth for stack, styling and process expectations, and it is edited over time.
- `context/docs/ATD-DIT-F0003 (ERS) Micrositio OICM.pdf` — the formal requirements spec (ERS) the build is based on. The other PDFs in `context/docs/` are supporting material.
- `context/plandetrabajo.md` — the phased work plan with checklists. It is expected to exist and be kept up to date: mark each phase's checklist as it is completed.

The codebase is currently an **unmodified Laravel 13 skeleton** (default `welcome` route, `User` model, stock migrations). Almost everything described in the brief is still to be built, so expect to create structure rather than follow it.

## Commands

```sh
composer setup                 # install deps, .env, key, migrate, npm install + build
composer dev                   # php artisan dev — server, queue worker, logs, vite together
composer test                  # clears config, then php artisan test
php artisan test --filter=SomeTest          # single test / single method
php artisan test tests/Feature/ExampleTest.php
vendor/bin/pint                # code style (Laravel preset); vendor/bin/pint --test to check only
npm run dev / npm run build    # Vite (Tailwind v4 via @tailwindcss/vite)
php artisan pail               # tail application logs
```

Sail (`compose.yaml`) provides MySQL 8.4 + Mailpit; run commands through `vendor/bin/sail` when using it. Mailpit UI on `:8025`.

## Environment note

`.env` and `.env.example` disagree on purpose: `.env.example` is the SQLite default (`database/database.sqlite` exists), while the live `.env` targets the Sail MySQL service (`DB_HOST=mysql`, forwarded on `3307`). The brief specifies **MySQL**, so treat MySQL as the real target and don't "fix" `.env` back to SQLite. Tests run against `DB_DATABASE=testing` per `phpunit.xml`.

## Stack decisions from the brief

The brief mandates **Laravel + Livewire + Jetstream + MySQL**. Neither Livewire nor Jetstream is installed yet — installing them is part of the work, not an assumption you can already rely on. Laravel Boost is likewise referenced by `AGENTS.md` but is **not** installed (`composer require laravel/boost --dev && php artisan boost:install`).

## Conventions required by the client

These come from `context/promptinicial.md` and override default habits:

- Wrap fallible logic in `try/catch`; apply null-safety throughout.
- Comment each block you write with what it does (the client reads the code).
- User-facing messages go through **SweetAlert2**, not plain Blade alerts or flash strings.
- Run the relevant tests after each implemented feature or phase, then update the checklist in `context/plandetrabajo.md`.
- Apply SAST/DAST-minded hardening (validation, escaping, authorization, no injectable queries).
- Act as a senior Laravel developer for code, and as a senior UX/UI designer for anything visual.

## Design language

- Apple-like (apple.com/mx) visual language: generous whitespace, large type, restrained motion.
- **Do not** ship default Laravel/Jetstream scaffolding views — build custom, visually polished ones.
- Mobile-first and responsive.
- Palette: `#265b4d` (primary green), `#cdde00` (accent lime), `#ffffff`, `#afafaf`.
- Logos/styling reference: the sibling project `../mejorav4`.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.5. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Test every code change by adding or updating a test.
- Run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
