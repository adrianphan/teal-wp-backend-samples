# teal-wp-backend-samples
WordPress/PHP backend code samples demonstrating custom plugins, REST APIs, Gutenberg blocks, WP-CLI, caching, and security best practices.

# Teal Code Samples (WordPress/PHP)

This repo contains a few small, self-contained samples that demonstrate how I structure backend WordPress work with security, performance, and maintainability in mind.

## 1) MU-plugin: `teal-sample-core`
A minimal “core” plugin that registers a CPT (`resource`), adds a REST API endpoint with validation and caching, and includes an admin Settings page.
Highlights:
- Capability checks, nonce verification, strict sanitization/escaping
- REST responses with stable schema + pagination
- Object cache wrapper with safe fallback
- Simple structured logging pattern

How to use:
- Place `wp-content/mu-plugins/teal-sample-core/` into a WordPress install.
- Visit wp-admin to see “Teal Settings”.
- REST endpoint example:
  `/wp-json/teal/v1/resources?per_page=10&page=1&type=guide`

## 2) Dynamic Block: `blocks/stat-card`
A server-rendered block using `block.json` and a PHP render callback.
Highlights:
- Safe attribute validation
- Escaping and accessible markup

## 3) WP-CLI Import: `cli/teal-import-resources.php`
A WP-CLI command that imports Resources from a CSV file.
Highlights:
- Idempotent behavior (updates existing items by unique slug)
- Clear logging and error handling
- Batch-friendly design

These are intentionally small, but mirror patterns I use on client projects: clear separation of concerns, predictable interfaces, and safe defaults.
