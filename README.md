# Presslyte AI Search Check

A small, standalone WordPress plugin that helps editors review common content issues while viewing a page. Checks run locally in the browser and do not modify content.

This repository contains the public WordPress.org release, version **0.1.3**, with this GitHub overview added. The six plugin files are unchanged from the published distribution.

- [WordPress.org plugin page](https://wordpress.org/plugins/presslyte-ai-search-check/)
- [Public release download](https://downloads.wordpress.org/plugin/presslyte-ai-search-check.0.1.3.zip)
- [Presslyte](https://presslyte.com/)

## What it checks

- HTML robots meta tags containing a `noindex` directive.
- Missing or multiple H1 headings and skipped heading levels.
- Images with missing alt attributes or loading failures.
- Vague link labels.
- Placeholder text and possible unrendered shortcodes.
- Paragraphs longer than 120 words.

Findings appear beside the page with Ready, Review, and Needs attention labels. Editors can highlight a flagged element and open the standard WordPress editor to make changes.

## Code overview

- `presslyte-ai-search-check.php` registers the WordPress dashboard and admin-bar entry, checks editing permissions, and loads the required assets and translated messages.
- `assets/js/check.js` selects the page content, evaluates fixed rules, and renders the findings panel and element highlights.
- `assets/css/check.css` styles the front-end panel and highlights.
- `assets/css/admin.css` styles the dashboard guide.
- `readme.txt` contains installation instructions, compatibility information, limitations, and the release history.

The runtime uses WordPress PHP APIs and browser JavaScript. It does not require a build process, an AI model, an API key, or a separate account. It has no content-saving endpoints, database tables, scheduled jobs, telemetry, or automatic external requests. Optional dashboard links open only when selected.

## Installation

Use the WordPress.org download for an installable release. Alternatively, place the files from this repository in `wp-content/plugins/presslyte-ai-search-check`, activate the plugin, and visit a post or page while signed in as an editor with permission to edit it.

Requirements: WordPress 6.0 or later and PHP 7.4 or later. The public release declares testing through WordPress 7.1.

## Scope

The checker examines one page at a time. It does not confirm indexing, inspect HTTP response headers, assess factual accuracy, or guarantee search rankings or AI citations. Its suggestions are not a complete SEO or accessibility audit.

The separate Presslyte content editor is not required. This repository contains only the published AI Search Check plugin and does not include the Presslyte editor, commercial licensing code, or private development history.

## License

GPL v2 or later, as declared in the original plugin header and `readme.txt`. See [GNU General Public License version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).
