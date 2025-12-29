# Classes CPT Plugin

This plugin registers a `class` custom post type and three taxonomies:

- `subject` (hierarchical)
- `instructor` (non-hierarchical, tag-like)
- `class_level` (hierarchical)

Features:
- Shows taxonomies in admin columns
- REST API support for CPT and taxonomies (Gutenberg compatible)
- Flushes rewrite rules on activation/deactivation

Usage:
- Activate the plugin in WP admin
- Add Classes via the "Classes" menu
- Filter by `subject`, `instructor`, or `class_level` in admin

Extending:
- Hook into `init` to add meta boxes or additional fields.
- Use `register_post_type` arguments in `includes/class-post-type.php` to change supports or rewrite behavior.
