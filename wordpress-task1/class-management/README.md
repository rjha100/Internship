# Class Management Plugin

A WordPress plugin for managing classes with instructors using native WordPress functionality.

## File Structure

```
class-management/
├── class-management.php          # Main plugin file (includes all modules)
├── includes/
│   ├── post-types.php           # Class custom post type registration
│   ├── taxonomies.php           # Class Subject & Level taxonomies
│   ├── user-roles.php           # Instructor role and permissions
│   ├── meta-boxes.php           # Class meta boxes and save functions
│   └── instructor-profile.php   # Instructor user profile fields
└── js/
    └── instructor-profile.js    # Media uploader for instructor photos
```

## Features

### Custom Post Type: Classes
- Custom capability type for granular permissions
- Support for title, editor, thumbnail, and excerpt
- Custom meta fields for class details
- Two custom taxonomies: Subjects and Levels

### Taxonomies
- **Class Subject**: Hierarchical taxonomy for categorizing classes (e.g., Fiction, Poetry, Creative Writing)
  - Instructors can create and assign subjects
  - Admins can delete subjects
- **Class Level**: Hierarchical taxonomy with predefined levels
  - Beginner
  - Intermediate
  - Advanced
  - Automatically created on plugin activation

### User Role: Instructor
- Can view all classes
- Can only edit classes where they are assigned or authored
- Auto-assigned as instructor when creating new classes
- Can assign and create class subjects
- Can assign class levels
- Custom profile fields: degree, bio, testimonial, photo

### Class Meta Fields
- **Instructor** (auto-assigned for instructors, selectable for admins)
- **Start Date** (Y-m-d format for database queries)
- **End Date** (Y-m-d format for database queries)
- **Start Time** (H:i format, 12-hour input, stored as 24-hour)
- **End Time** (H:i format, 12-hour input, stored as 24-hour)
- **Location** (Address & City/State/Zip)
- **Pricing** (Primary & Member with custom labels)
- **Registration Deadline** (datetime format)
- **Class Schedule Dates** (textarea, one date per line)
- **Similar Classes** (select up to 3 related classes)

### Instructor Profile Fields
- Display Name (required)
- Degree
- Bio (required, WYSIWYG editor)
- Testimonial Text
- Testimonial Author
- Display Image (WordPress Media Library, stored as attachment ID)

## Installation

1. Upload the `class-management` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create instructor users with the "Instructor" role
4. Start creating classes!

## Data Storage

All date and time fields are stored in query-friendly formats:
- **Dates**: Y-m-d format (e.g., 2026-01-15)
- **Times**: H:i format (e.g., 14:00 for 2:00 PM)
- **Datetimes**: Y-m-d H:i:s format

This ensures efficient filtering and searching capabilities.

## Development Notes

- Uses native WordPress meta boxes (no ACF dependency)
- Instructor data stored in `wp_usermeta` table
- Class data stored in `wp_postmeta` table
- Permission system uses `map_meta_cap` filter
- Modular file structure for easy maintenance
- Instructor photo stored as attachment ID for flexibility
- Taxonomy terms auto-created on activation (Class Levels)
