=== Talk-to-Build ===
Contributors: mkway
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Talk-to-Build is a minimal, future-proof WordPress theme designed for AI-powered build workflows.

**Two modes, one theme:**

* **Standard pages** — Clean layout with full block editor support. Clients can edit content, swap images, and manage pages without touching code.
* **Canvas (Blank) template** — Serves raw HTML with zero theme interference. No header, no footer, no WordPress styles. Perfect for AI code pipelines, static HTML deploys, and landing pages that need pixel-perfect control.

**Why Talk-to-Build?**

* Zero external dependencies — no frameworks, no build tools, no JavaScript libraries
* Lightweight — under 15KB total theme size
* Block editor ready — supports wide/full alignment, responsive embeds, and block styles
* Future-proof — minimal PHP, no deprecated functions, works with WordPress 6.0+

Built by the Talk-to-Build community — where creators use AI tools to build real websites.

== Installation ==

1. Upload the `talk-to-build` folder to `/wp-content/themes/`
2. Activate the theme through **Appearance > Themes**
3. To use the Canvas template: edit any page, select "Canvas (Blank)" from the Page Template dropdown

== Frequently Asked Questions ==

= What is the Canvas template for? =

The Canvas template outputs raw HTML content with no WordPress wrapper — no header, footer, styles, or scripts. This is useful for:

* AI-generated landing pages (from tools like Claude Code, Cursor, etc.)
* Static HTML pages deployed via WP-CLI or the REST API
* Any page where you need full control over the HTML output

= Can I use this theme for a regular website? =

Yes. Standard pages use the block editor with a clean, minimal layout. The Canvas template is optional — you only use it when you need raw HTML output.

= Is this theme compatible with page builders? =

The standard template works with the WordPress block editor. The Canvas template bypasses all WordPress output, so page builders won't affect it.

== Changelog ==

= 1.0.0 =
* Initial release
* Standard page template with block editor support
* Canvas (Blank) template for raw HTML output
* Primary and footer navigation menus
* Custom logo support
* Footer widget area

== Copyright ==

Talk-to-Build WordPress Theme, (C) 2026 Talk-to-Build
Talk-to-Build is distributed under the terms of the GNU GPL v2 or later.

== Resources ==

* Based on starter theme best practices from WordPress.org Theme Handbook
* System font stack — no external font resources loaded
