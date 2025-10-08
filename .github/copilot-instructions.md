# Copilot Instructions for AI Agents

## Project Overview
This is a Laravel-based document management system for registrar operations. The codebase follows standard Laravel conventions but includes custom models, helpers, and workflows specific to document processing and authorization.

## Architecture & Key Components
- **app/Models/**: Contains Eloquent models for core entities (e.g., `Student`, `RequestModel`, `DocumentType`, `Envelope`, `Authorization`, `Representative`, `ActivityLog`).
- **app/Helpers/ProcessingDays.php**: Custom business logic for document processing timelines.
- **app/Http/Controllers/**: Request handling and business logic. Admin and user flows are separated by controller.
- **database/migrations/**: Schema definitions for all entities. Migration filenames indicate creation order and entity type.
- **resources/views/**: Blade templates for UI. Follows Laravel's view conventions.

## Developer Workflows
- **Build & Serve**: Use `php artisan serve` for local development. Frontend assets are built with Vite (`npm run dev`).
- **Testing**: Run `php artisan test` or `vendor/bin/pest` for tests. Feature and unit tests are in `tests/Feature` and `tests/Unit`.
- **Database**: SQLite is used for local development (`database/database.sqlite`). Migrate with `php artisan migrate`.
- **Seeding**: Use `php artisan db:seed` to populate sample data.

## Project-Specific Patterns
- **Model Naming**: Models use singular, PascalCase names (e.g., `RequestModel`, not `Requests`).
- **Authorization**: Custom logic in `app/Models/Authorization.php` and related controllers. Check for role-based access in controllers.
- **Activity Logging**: All major actions are logged via `ActivityLog` model.
- **Processing Days**: Business rules for document timelines are centralized in `Helpers/ProcessingDays.php`.
- **Routing**: API routes in `routes/api.php`, web routes in `routes/web.php`, and auth routes in `routes/auth.php`.

## Integration Points
- **External Packages**: Standard Laravel packages, Pest for testing, Tailwind for CSS, Vite for asset bundling.
- **Notifications**: Custom notification logic in `app/Models/Notification.php`.
- **User Roles**: Role checks are enforced in controllers and middleware.

## Examples
- To add a new document type, create a migration, update `DocumentType` model, and add logic to the relevant controller.
- For new business rules, update or extend `Helpers/ProcessingDays.php`.
- To log an action, use the `ActivityLog` model directly in controllers.

## References
- Key files: `app/Models/RequestModel.php`, `app/Helpers/ProcessingDays.php`, `routes/web.php`, `database/migrations/`, `resources/views/`
- For Laravel conventions, see [Laravel Docs](https://laravel.com/docs)

---
Update this file as project conventions evolve. For unclear patterns, ask maintainers for clarification.
