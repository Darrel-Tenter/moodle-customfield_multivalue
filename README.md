# Multi-Select Custom Field for Moodle

A Moodle plugin that adds a **multi-select field type** to Moodle's modern `customfield_*` API — available anywhere Moodle uses that API: courses, programs, certifications, and more.

## The Problem

Moodle's built-in custom field types include text, number, checkbox, date, and single-select. There is no built-in option for selecting **multiple values from a defined list**. If you need to tag a course with multiple categories, topics, roles, or attributes from a fixed option list, the standard field types can't do it cleanly.

## The Solution

`customfield_multiselect` adds a multi-select field type that integrates natively with Moodle's `customfield_*` API. Once installed, it appears alongside the built-in field types wherever custom fields are configured — course settings, Workplace programs, Workplace certifications, and any other component that uses the modern customfield system.

### Features

- Select one or more values from an administrator-defined options list
- Options list is configured per-field (one option per line in the field settings)
- Stored as a comma-separated string — queryable with standard SQL (`FIND_IN_SET`)
- Supports empty selection (stores empty string, not NULL)
- Works anywhere Moodle's modern `customfield_*` API is used
- Compatible with Moodle 5.0+

## Installation

### Via git

Clone into the `customfield` directory of your Moodle installation:

```bash
git clone https://github.com/[your-org]/moodle-customfield_multiselect.git customfield/multiselect
```

Then log in as admin and go to **Site Administration → Notifications** to trigger installation.

### Via zip

1. Download the zip from GitHub
2. Unzip and rename the folder to `multiselect`
3. Place it at `customfield/multiselect/` in your Moodle root
4. Go to **Site Administration → Notifications** to install

**Deployment classification: Major Release** — install on a dev/staging site first and verify field creation and storage before deploying to production.

## Usage

After installation, the **Multi-select** field type appears in the custom fields configuration UI (e.g., **Site Administration → Courses → Course custom fields**).

### Creating a field

1. Go to the relevant custom fields admin page
2. Add a new field and select **Multi-select** as the type
3. Enter your options list — one option per line
4. Optionally set a default value and display size
5. Save

### Using the field

The field renders as an autocomplete multi-select input on the relevant edit form (course settings, certification settings, etc.). Users can select one or more values from the configured options list.

### Querying stored values

Values are stored as a comma-separated string in `mdl_customfield_data.value`. For example, selecting `Option A` and `Option C` stores:

```
Option A,Option C
```

To query records tagged with a specific value, use MySQL's `FIND_IN_SET`:

```sql
SELECT c.id, c.fullname
  FROM {course} c
  JOIN {customfield_data} cf ON cf.instanceid = c.id
   AND cf.fieldid = :fieldid
 WHERE FIND_IN_SET(:value, cf.value) > 0
```

## File Structure

```
customfield_multiselect/
├── classes/
│   ├── data_controller.php     # Saves/retrieves values; implements export_value()
│   ├── field_controller.php    # Admin form: options list, default, display size
│   └── privacy/
│       └── provider.php        # null_provider — core customfield handles data storage
├── lang/
│   └── en/
│       └── customfield_multiselect.php   # All strings
└── version.php                 # Plugin metadata
```

## Architecture Notes

### API pattern

Moodle's `customfield_*` plugin type requires two classes:

- **`field_controller`** — defines the admin configuration form for the field (the options list, default value, display size). Extends `\core_customfield\field_controller`. Configuration is stored as JSON in `mdl_customfield_field.configdata`.
- **`data_controller`** — handles saving and retrieving values for individual records. Extends `\core_customfield\data_controller`. Provides the form element shown to end users and implements `export_value()` for external output.

`customfield_checkbox` is the simplest reference implementation. `customfield_select` is the closest functional reference for option-list handling.

### Storage contract

| Rule | Detail |
|---|---|
| Storage column | `mdl_customfield_data.value` (string) |
| Format | Comma-separated option text values, no spaces around commas |
| `intvalue` column | Not used — always set to `0` |
| Empty selection | Stored as empty string `''`, not `NULL` |
| Whitespace | Trimmed from each value before joining |

Example: selecting `SE 49` and `SE 51` stores `SE 49,SE 51`. `FIND_IN_SET('SE 49', 'SE 49,SE 51')` returns `1`.

### What this plugin does NOT do

- **Does not support the legacy `user_info_field` system** — Moodle has two separate custom field systems. This plugin works with the modern `customfield_*` API only (courses, programs, certifications). User profile fields use a different architecture and are not supported.
- **Does not use `intvalue`** — Unlike `customfield_checkbox`, this plugin stores all data in the `value` column. Do not query `intvalue` for multi-select fields.
- **Does not validate options against the configured list on retrieval** — If the options list changes after data has been saved, existing stored values are preserved as-is.

## Compatibility

- **Moodle**: 5.0+ (built and tested on Moodle Workplace 5.1.3)
- **PHP**: 8.2+
- **Database**: MySQL / MariaDB (uses `FIND_IN_SET` for queries — not available on PostgreSQL without modification)
- **Themes**: All themes (field type has no theme-specific output)

## Contributing

Bug reports and pull requests are welcome via GitHub Issues and Pull Requests.

## License

GNU GPL v3 or later — https://www.gnu.org/copyleft/gpl.html
