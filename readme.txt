=== Presslyte AI Search Check ===
Contributors: presslyte
Tags: ai seo, content review, readability, seo
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Spot common content and search-readiness issues on a WordPress page, with private, read-only checks in your browser.

== Description ==

Spot common content issues while viewing your WordPress page. Presslyte AI Search Check gives logged-in editors a focused review of headings, images, links, and text, without changing the content.

Open a post or page you can edit, select **AI Search Check** in the front-end admin bar, and review the findings alongside your page. Results use **Ready**, **Review**, and **Needs attention** labels. Where a finding points to an element, select it to highlight that element. Open the normal WordPress editor when you are ready to make changes.

= What it checks =

* HTML robots meta tags that contain a noindex directive.
* Missing or multiple H1 headings and skipped heading levels.
* Visible images missing an alt attribute and images that failed to load.
* Common vague link labels, such as "click here".
* Visible placeholder text and possible unrendered shortcodes.
* Paragraphs longer than 120 words.

= Private, read-only checks =

Checks run locally in your browser using fixed rules. The plugin does not use an AI model or send your content to an AI provider. No separate account, API key, or subscription is required.

The checker does not change your content, crawl your entire site, create database tables, schedule background tasks, or collect telemetry.

= Scope and limits =

The plugin reviews the page you are viewing, one page at a time. It looks for the page's content area and falls back to the full page when it cannot identify one. Theme markup can affect what is included in a check.

Its findings are prompts for an editor to review. It does not assess factual accuracy, confirm search-engine indexing, inspect HTTP response headers, or guarantee search rankings or AI citations. A Ready result means the included checks did not flag an issue; it is not a complete SEO or accessibility audit.

Presslyte AI Search Check works independently. The separate Presslyte content editor is not required.

= Optional Presslyte resources =

The plugin's dashboard includes clearly labeled links to Presslyte documentation, support, and commercial plans. These open only when you select them. The plugin makes no automatic request to Presslyte, and the front-end results panel contains no sales link.

== Installation ==

1. Upload and activate the plugin.
2. Open **AI Search Check** in the WordPress dashboard for the quick-start guide.
3. Visit a post or page while logged in as a user who can edit it.
4. Select **AI Search Check** from the front-end admin bar.

== Frequently Asked Questions ==

= Does this plugin use AI? =

No. AI Search Check uses fixed browser-based rules to flag common content and search-readiness issues. It does not run an AI model or predict whether an AI service will cite your page.

= Do I need the main Presslyte plugin? =

No. AI Search Check is a standalone plugin.

= Does this plugin change my content? =

No. The checker is read-only.

= Does it send my content to Presslyte or an AI provider? =

No. Version 0.1.3 runs the page checks in your browser and makes no automatic external request.

= Does it guarantee search rankings or AI citations? =

No. It provides a small set of practical review suggestions and makes no ranking or citation guarantee.

= Does the plugin contact an external service? =

No automatic external request is made. The dashboard includes optional links to Presslyte documentation, support, and plans; your browser visits Presslyte only if you select one of those links.

== Changelog ==

= 0.1.3 =

* Show every finding instead of silently limiting the results list.
* Remove empty passed-check sections and nonfunctional disabled result controls.
* Tighten the admin-bar trigger and results-panel relationship.
* Add a focused Presslyte-branded quick-start dashboard.
* Keep promotional links out of the checker panel and place optional Presslyte resources in the dashboard.

= 0.1.2 =

* Add the official Presslyte icon and clearer Ready, Review, and Needs attention labels.
* Improve accessible-link detection and page-content targeting.
* Improve mobile panel containment, control sizes, contrast, and new-tab disclosure.
* Preserve the free plugin's read-only, local, one-page-at-a-time scope.

= 0.1.1 =

* Narrow the page area used for content checks and disclose whole-page fallback behavior.
* Improve HTML noindex detection and clarify that HTTP response headers are not checked.
* Respect accessible link names and reduced-motion preferences.
* Make checker result messages translation-ready.

= 0.1.0 =

* Initial release.
