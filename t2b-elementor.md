---
description: Convert vibe-coded HTML to native Elementor Pro pages — the Talk-to-Build Elementor pipeline
execution_scripts: []
---

## Purpose

Convert HTML files (from Figma Decode, Gemini, Lovable, Anastasia, or hand-coded) into fully native Elementor Pro pages. Unlike the Canvas approach, the output is editable in Elementor's drag-and-drop builder — clients can modify text, images, and layout without touching code.

**Use this directive when:**
- The client requires Elementor Pro (existing workflow or team preference)
- The client will edit content independently after handover
- The project needs Elementor-specific features (popups, forms, Loop Grid, dynamic content)

**Use `/t2b-wordpress` (Canvas) instead when:**
- Speed of deployment is the priority
- The client will never self-edit
- The design is too complex to map to Elementor widgets cleanly

---

## How Elementor Stores Pages

Elementor bypasses `post_content`. Everything lives in WordPress `postmeta`:

| Meta key | Value | Required |
|---|---|---|
| `_elementor_data` | JSON array of containers — the full page layout | ✅ |
| `_elementor_edit_mode` | `"builder"` | ✅ |
| `_wp_page_template` | `"elementor_header_footer"` (uses theme nav/footer) or `"elementor_canvas"` (blank) | ✅ |
| `_elementor_version` | Current Elementor version on the server (e.g., `"3.25.0"`) | Recommended |

`post_content` should be empty for pure Elementor pages.

---

## Page Type Decision

Same rule as standard T2B:

| Page | Template |
|---|---|
| Normal site page (about, contact, services…) | `elementor_header_footer` — uses the theme/Elementor header & footer |
| Homepage with fully custom layout | `elementor_canvas` — blank, no chrome |
| Standalone landing page | `elementor_canvas` |
| When in doubt | `elementor_header_footer` |

---

## Widget Mapping Reference

When analysing HTML, map each element to the most appropriate native Elementor widget. Use the `html` widget only as a last resort.

| HTML pattern | Elementor widget | `widgetType` |
|---|---|---|
| `<h1>` – `<h6>` | Heading | `heading` |
| `<p>`, paragraph blocks | Text Editor | `text-editor` |
| `<img>` standalone | Image | `image` |
| CTA links, `<button>`, `.btn` | Button | `button` |
| `<section>`, layout wrappers | Container | `container` (elType) |
| `<ul><li>` with icons | Icon List | `icon-list` |
| Icon + title + description card | Icon Box | `icon-box` |
| Image + title + description card | Image Box | `image-box` |
| Large number stats (e.g. "500+") | Counter | `counter` |
| FAQ / expandable items | Accordion | `accordion` |
| Tab navigation | Tabs | `tabs` |
| `<hr>` / section separator | Divider | `divider` |
| Whitespace gap | Spacer | `spacer` |
| `<form>` | Form (Pro) | `form` |
| `<nav>` | Nav Menu (Pro) | `nav-menu` |
| Social links row | Social Icons | `social-icons` |
| Google Maps embed | Google Maps | `google_maps` |
| Image grid / gallery | Basic Gallery | `image-gallery` |
| Video embed / `<video>` | Video | `video` |
| Testimonial block | Testimonial Carousel (Pro) | `testimonial-carousel` |
| Star rating | Star Rating | `star-rating` |
| Complex/animated/SVG sections | HTML (fallback) | `html` |

> ❌ **NEVER use the `html` widget for regular page content.**
> The entire purpose of this directive is to produce pages the client can edit in Elementor.
> An `html` widget is a black box — the client cannot change text, colours, or images without knowing HTML.
>
> **`html` widget is allowed ONLY in two situations:**
> 1. **Phase 0** — header and footer in Elementor Theme Builder (unavoidable: containers don't render in Theme Builder context)
> 2. **Truly unmappable sections** — complex CSS animations, SVG diagrams, interactive JS sliders with no Elementor equivalent
>
> **If you are about to use an `html` widget for a heading, a paragraph, an image, or a button — stop. Use the native widget instead.**
> When in doubt, use a simpler native widget that loses some visual fidelity over an HTML widget that loses all editability.

---

## Elementor JSON Structure

### Container (modern, Elementor 3.6+)

```json
{
  "id": "XXXXXXXX",
  "elType": "container",
  "isInner": false,
  "settings": {
    "background_background": "classic",
    "background_color": "#ffffff",
    "padding": { "unit": "px", "top": "80", "right": "40", "bottom": "80", "left": "40", "isLinked": false },
    "flex_direction": "column",
    "content_width": "boxed",
    "width": { "unit": "px", "size": 1200 }
  },
  "elements": [ ...widgets here... ]
}
```

**Two-column layout**: use a container with `flex_direction: "row"` containing two inner containers (`"isInner": true`) each with `width: { "unit": "%", "size": 50 }`.

### Heading

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "heading",
  "isInner": false,
  "settings": {
    "title": "Your Title Here",
    "header_size": "h2",
    "align": "center",
    "title_color": "#1a1a2e",
    "typography_typography": "custom",
    "typography_font_size": { "unit": "px", "size": 48 },
    "typography_font_size_tablet": { "unit": "px", "size": 36 },
    "typography_font_size_mobile": { "unit": "px", "size": 28 },
    "typography_font_weight": "700"
  },
  "elements": []
}
```

### Text Editor

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "text-editor",
  "isInner": false,
  "settings": {
    "editor": "<p>Your paragraph text here. Can include <strong>bold</strong> and <em>italic</em>.</p>",
    "align": "left"
  },
  "elements": []
}
```

### Image

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "image",
  "isInner": false,
  "settings": {
    "image": { "url": "/wp-content/uploads/<PROJECT>/image.jpg" },
    "image_size": "full",
    "align": "center"
  },
  "elements": []
}
```

### Button

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "button",
  "isInner": false,
  "settings": {
    "text": "Get Started",
    "link": { "url": "https://example.com", "is_external": false },
    "align": "center",
    "button_text_color": "#ffffff",
    "background_color": "#0073aa",
    "border_radius": { "unit": "px", "top": 6, "right": 6, "bottom": 6, "left": 6 },
    "padding": { "unit": "px", "top": "14", "right": "32", "bottom": "14", "left": "32", "isLinked": false }
  },
  "elements": []
}
```

### Icon Box

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "icon-box",
  "isInner": false,
  "settings": {
    "selected_icon": { "library": "fa-solid", "value": "fas fa-star" },
    "icon_color": "#0073aa",
    "title_text": "Feature Title",
    "description_text": "Feature description text goes here.",
    "position": "top",
    "title_size": "h4"
  },
  "elements": []
}
```

### Counter

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "counter",
  "isInner": false,
  "settings": {
    "starting_number": 0,
    "ending_number": 500,
    "number_prefix": "",
    "number_suffix": "+",
    "duration": { "size": 2000 },
    "title": "Happy Clients",
    "title_typography_font_size": { "unit": "px", "size": 16 },
    "number_typography_font_size": { "unit": "px", "size": 48 }
  },
  "elements": []
}
```

### Accordion

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "accordion",
  "isInner": false,
  "settings": {
    "tabs": [
      { "_id": "a1b2", "tab_title": "Question 1?", "tab_content": "Answer 1 content." },
      { "_id": "c3d4", "tab_title": "Question 2?", "tab_content": "Answer 2 content." }
    ],
    "title_html_tag": "h3"
  },
  "elements": []
}
```

### Social Icons

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "social-icons",
  "isInner": false,
  "settings": {
    "social_icon_list": [
      { "_id": "a1b2", "social_icon": { "value": "fab fa-instagram", "library": "fa-brands" }, "link": { "url": "https://instagram.com/profile" } },
      { "_id": "c3d4", "social_icon": { "value": "fab fa-linkedin", "library": "fa-brands" }, "link": { "url": "https://linkedin.com/in/profile" } }
    ],
    "icon_size": { "unit": "px", "size": 24 },
    "icon_color": "#1a1a2e",
    "icon_hover_color": "#0073aa"
  },
  "elements": []
}
```

### HTML Fallback (complex/animated sections)

```json
{
  "id": "XXXXXXXX",
  "elType": "widget",
  "widgetType": "html",
  "isInner": false,
  "settings": {
    "html": "<style>/* section-specific CSS here */</style>\n<div class=\"your-section\">...</div>"
  },
  "elements": []
}
```

---

## Responsive Settings Pattern

Append `_tablet` and `_mobile` to any setting key for responsive overrides:

```json
"typography_font_size":        { "unit": "px", "size": 48 },
"typography_font_size_tablet": { "unit": "px", "size": 36 },
"typography_font_size_mobile": { "unit": "px", "size": 26 },
"padding":        { "unit": "px", "top": "80", "right": "40", "bottom": "80", "left": "40" },
"padding_tablet": { "unit": "px", "top": "60", "right": "24", "bottom": "60", "left": "24" },
"padding_mobile": { "unit": "px", "top": "40", "right": "16", "bottom": "40", "left": "16" }
```

---

## Phase 0: Theme Builder — Header & Footer (Full-Site Migrations)

**Run this phase first for any full-site migration.** Skip it only when adding a single new page to an existing site that already has a working header/footer.

The header and footer must be built once in Elementor Theme Builder and set to display on the Entire Site. All pages then use `elementor_header_footer` so the shared header/footer appears automatically — you never duplicate nav or footer in page JSON.

---

### Step 0a: Analyse Header & Footer HTML

From the source HTML (Netlify site), extract:

**Header:**
- Logo: image URL or text
- Navigation links: labels + URLs
- Any CTA button in the header
- Background colour + height

**Footer:**
- Logo (if repeated)
- Footer nav columns (labels + URLs)
- Copyright text
- Social media links + platforms

Make a list of these before generating any JSON.

---

### Step 0b: Create the WP Navigation Menu

```bash
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP>

# Create menu and add items (replace with actual links from the site)
wp --path=~/files menu create "Main Navigation"
wp --path=~/files menu item add-custom "Main Navigation" "Home"     "/"
wp --path=~/files menu item add-custom "Main Navigation" "About"    "/about/"
wp --path=~/files menu item add-custom "Main Navigation" "Services" "/services/"
wp --path=~/files menu item add-custom "Main Navigation" "Contact"  "/contact/"

# Assign to primary location (if theme registers one)
wp --path=~/files menu location assign "Main Navigation" primary

# Confirm
wp --path=~/files menu list
```

If the site has a footer menu, create a second one: `"Footer Navigation"`.

---

### Step 0c: Generate Header & Footer JSON

> ⚠️ **Theme Builder uses `section/column` — NOT containers.**
> Elementor 3.6+ containers (flexbox) do not render correctly in the Theme Builder context (header/footer templates). Always use the classic `section → column → widget` structure for Phase 0. Containers work fine for regular page content (Steps 2+).

The reliable approach for Theme Builder: one `section`, one full-width `column`, one `html` widget with the complete header/footer HTML+CSS extracted from the source file.

```python
import json, secrets

def uid(): return secrets.token_hex(4)

def make_theme_builder_template(html_content):
    """Wrap HTML content in section/column — required for Theme Builder."""
    return [
        {
            "id": uid(), "elType": "section", "isInner": False,
            "settings": {
                "padding": {"unit": "px", "top": "0", "right": "0", "bottom": "0", "left": "0", "isLinked": True}
            },
            "elements": [
                {
                    "id": uid(), "elType": "column", "isInner": False,
                    "settings": {"_column_size": 100, "_inline_size": None},
                    "elements": [
                        {
                            "id": uid(), "elType": "widget", "widgetType": "html",
                            "isInner": False,
                            "settings": {"html": html_content},
                            "elements": []
                        }
                    ]
                }
            ]
        }
    ]

# Extract header HTML+CSS from source file
# Include <style> tags and the <header> element — strip <html>/<head>/<body>
header_html = """
<style>
/* paste header-specific CSS here */
</style>
<header class="site-header">
  <!-- paste header HTML here -->
</header>
"""

footer_html = """
<style>
/* paste footer-specific CSS here */
</style>
<footer class="site-footer">
  <!-- paste footer HTML here -->
</footer>
"""

with open('header-data.json', 'w', encoding='utf-8') as f:
    json.dump(make_theme_builder_template(header_html), f, ensure_ascii=False)
print('header-data.json written')

with open('footer-data.json', 'w', encoding='utf-8') as f:
    json.dump(make_theme_builder_template(footer_html), f, ensure_ascii=False)
print('footer-data.json written')
```

**What to put in `header_html` / `footer_html`:**
1. Extract `<style>` blocks that apply to the header/footer from the source HTML
2. Extract the `<header>` element (or the nav wrapper) — everything between `<body>` and the first `<main>`
3. Extract the `<footer>` element — everything after `</main>`
4. Do NOT include `<html>`, `<head>`, `<body>` tags
5. Localise all image `src` attributes to `/wp-content/uploads/<project>/`

**Multi-column header alternative** — if the design needs columns within the header, use multiple `column` elements inside the same `section`. Do NOT nest sections or use containers:

```python
def make_theme_builder_columns(columns):
    """columns: list of (size_pct, html_content) tuples. Sizes must sum to 100."""
    col_elements = [
        {
            "id": uid(), "elType": "column", "isInner": False,
            "settings": {"_column_size": size, "_inline_size": size},
            "elements": [
                {"id": uid(), "elType": "widget", "widgetType": "html",
                 "isInner": False, "settings": {"html": html}, "elements": []}
            ]
        }
        for size, html in columns
    ]
    return [
        {"id": uid(), "elType": "section", "isInner": False,
         "settings": {"padding": {"unit": "px", "top": "0", "right": "0", "bottom": "0", "left": "0", "isLinked": True}},
         "elements": col_elements}
    ]

# Example: logo (25%) | nav (50%) | CTA (25%)
# header_data = make_theme_builder_columns([
#     (25, '<img src="/wp-content/uploads/rcas/logo.png" alt="Logo">'),
#     (50, '<nav>...</nav>'),
#     (25, '<a href="/contact/" class="btn">Get Started</a>'),
# ])
```

**Footer example** — same pattern, two columns:

```python
footer_data = make_theme_builder_columns([
    (50, """
<style>/* footer left CSS */</style>
<div class="footer-left">
  <img src="/wp-content/uploads/rcas/logo-white.png" alt="Logo">
  <!-- footer nav links -->
</div>"""),
    (50, """
<style>/* footer right CSS */</style>
<div class="footer-right">
  <!-- social icons + copyright -->
</div>"""),
])

---

### Step 0d: Push Header & Footer Templates to DB

Theme Builder templates are `elementor_library` posts, not regular pages.

```python
import subprocess, json

def push_theme_template(json_file, title, template_type, db_user, db_pass, db_name, prefix):
    with open(json_file, 'r', encoding='utf-8') as f:
        raw_json = f.read()
    escaped = raw_json.replace('\\', '\\\\').replace("'", "\\'")
    slug = title.lower().replace(' ', '-')

    sql = f"""
INSERT INTO {prefix}posts
  (post_author, post_date, post_date_gmt, post_content, post_title,
   post_excerpt, post_status, comment_status, ping_status, post_name,
   post_type, post_modified, post_modified_gmt, to_ping, pinged, post_content_filtered)
VALUES
  (1, NOW(), NOW(), '', '{title}', '', 'publish', 'closed', 'closed',
   '{slug}', 'elementor_library', NOW(), NOW(), '', '', '');

SET @tpl_id = LAST_INSERT_ID();

INSERT INTO {prefix}postmeta (post_id, meta_key, meta_value) VALUES (@tpl_id, '_elementor_data',          '{escaped}');
INSERT INTO {prefix}postmeta (post_id, meta_key, meta_value) VALUES (@tpl_id, '_elementor_edit_mode',     'builder');
INSERT INTO {prefix}postmeta (post_id, meta_key, meta_value) VALUES (@tpl_id, '_elementor_template_type', '{template_type}');
INSERT INTO {prefix}postmeta (post_id, meta_key, meta_value) VALUES (@tpl_id, '_elementor_conditions',    '["include/general"]');
"""

    sql_file = f'/tmp/insert-{template_type}.sql'
    with open(sql_file, 'w') as f:
        f.write(sql)

    result = subprocess.run(
        ['mysql', '-u', db_user, '-p' + db_pass, db_name],
        stdin=open(sql_file), capture_output=True, text=True
    )
    if result.returncode == 0:
        print(f'SUCCESS: {template_type} template "{title}" inserted')
    else:
        print('ERROR:', result.stderr)

# Push header
push_theme_template('header-data.json', 'Main Header', 'header',
                    db_user='<DB_USER>', db_pass='<DB_PASS>', db_name='<DB_NAME>', prefix='<PREFIX>')

# Push footer
push_theme_template('footer-data.json', 'Main Footer', 'footer',
                    db_user='<DB_USER>', db_pass='<DB_PASS>', db_name='<DB_NAME>', prefix='<PREFIX>')
```

```bash
scp -i <YOUR_SSH_KEY_PATH> header-data.json footer-data.json push-theme-builder.py \
    <SITE_USER>@<IP>:/tmp/
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP> "python3 /tmp/push-theme-builder.py"
```

---

### Step 0e: Flush & Verify Theme Builder

```bash
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP> "cd ~/files && \
  wp elementor flush_css && \
  redis-cli --user <REDIS_USER> --pass '<REDIS_PASS>' FLUSHALL && \
  wp cache flush"
```

Then open the site URL — the header and footer should appear on every page.

**If the conditions don't apply automatically:**
Go to WP Admin → Templates → Theme Builder → click the Header template → click **Display Conditions** → add **Entire Site** → Save. Repeat for Footer. This is a 10-second manual step and is more reliable than guessing the options cache format.

---

### Step 0e.1: Fix Elementor Header Wrapper (always run)

**This step is mandatory — do not skip.** Elementor wraps every Theme Builder template in a `<div class="elementor-location-header">` block element. If the original header CSS uses `position: fixed` or `position: absolute`, this wrapper pushes page content down instead of letting the header float over it.

**Check the original header CSS** — scan the source HTML for the header/nav element:

```bash
grep -i 'position\s*:\s*fixed\|position\s*:\s*absolute' \
  "<LOCAL_HTML_PATH>/index.html"
```

**If `position: fixed` or `position: absolute` is found** → the header floats over content in the original design. Add this CSS fix to the `<style>` block inside the header HTML widget:

```css
/* Force Elementor's wrapper out of normal block flow — required when
   the original header uses position: fixed or absolute */
.elementor-location-header {
    position: fixed;   /* use absolute if original was absolute */
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    pointer-events: auto;
}
```

**Then add padding compensation for pages without a full-bleed hero** — so the header does not overlap regular page content. Add to the footer HTML widget or a global CSS widget:

```css
/* Pages where content starts immediately (no hero overlap) need top padding */
body:not(.home) .elementor-location-header + * {
    padding-top: <HEADER_HEIGHT>px;  /* measure from source — typically 70-100px */
}
```

Or handle per-page: add a spacer/padding to the first section of every non-hero page when building those pages in Steps 2+.

**If `position: static` / `position: relative` (normal flow)** → no fix needed. The Elementor wrapper behaves correctly by default.

---

### Step 0f: Set Page Template for All Pages

Now that Theme Builder is providing the header/footer, all pages (Steps 1–7 below) must use:

```
_wp_page_template = "elementor_header_footer"
```

Do **not** use `elementor_canvas` — that bypasses Theme Builder entirely and pages will have no header or footer.

---

## Workflow Steps

### Step 1: Gather Site Credentials

```bash
ssh -i <YOUR_SSH_KEY_PATH> -o StrictHostKeyChecking=no <SITE_USER>@<IP>
grep -E 'DB_NAME|DB_USER|DB_PASSWORD|table_prefix|WP_REDIS' ~/files/wp-config.php
# Also check Elementor version:
wp --path=~/files plugin get elementor --field=version
```

### Step 1b: Inventory Existing Pages (migration only)

**Run this before generating any JSON** whenever the WP site already has pages (staging clone, existing site). This gives you the exact slugs to use in the push script and prevents both duplicates and missed updates.

```bash
wp --path=~/files post list \
  --post_type=page \
  --post_status=publish,draft \
  --fields=ID,post_title,post_name \
  --format=table
```

Example output:
```
+-----+------------------+--------------+
| ID  | post_title       | post_name    |
+-----+------------------+--------------+
| 2   | Sample Page      | sample-page  |
| 5   | About Us         | about-us     |
| 8   | Contact          | contact      |
| 11  | Services         | services     |
+-----+------------------+--------------+
```

Now list the pages you are about to migrate from the Netlify HTML (by their URLs), and build a **slug mapping table** before writing any code:

| Netlify URL | WP page found? | `page_slug` to use | Action |
|---|---|---|---|
| `/about/` | ✅ "About Us" | `about-us` | UPDATE existing |
| `/contact/` | ✅ "Contact" | `contact` | UPDATE existing |
| `/services/` | ✅ "Services" | `services` | UPDATE existing |
| `/pricing/` | ❌ not found | `pricing` | INSERT new |

Use the `post_name` column (not the title) as the `page_slug` value in the push script. If the Netlify URL and WP slug differ (e.g. `/about/` → `about-us`), use the **WP slug** — that keeps the existing page's URL and internal links intact.

**When there is any ambiguity, stop and ask before generating JSON.** Present the mapping to the user in this format and wait for confirmation:

```
Before I proceed, I need to confirm the page mapping.

Here are the existing WP pages I found:
  ID 5  — "About Us"   (slug: about-us)
  ID 8  — "Contact"    (slug: contact)
  ID 11 — "Services"   (slug: services)

Here is how I'm planning to match them to the Netlify pages:

  ✅  /services/     →  services    (exact match — will UPDATE)
  ⚠️  /about/        →  about-us?   (slug differs — is this the same page?)
  ⚠️  /contact-us/   →  contact?    (slug differs — is this the same page?)
  🆕  /pricing/      →  (no match)  — will CREATE a new page. Confirm?

Please confirm, correct, or skip any of these before I continue.
```

Only proceed once the user has confirmed the full mapping. A wrong match updates the wrong page; a missed match creates a duplicate.

### Step 1c: Staging Pre-flight Cleanup (staging clone only)

**Run this when the staging site is a clone of a production site.** Production sites have their own Elementor Theme Builder templates (header, footer, popups). These conflict with the new ones we are about to build. This step wipes them cleanly without touching posts, pages, media, or any content.

```bash
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP> "
mysql -u <DB_USER> -p'<DB_PASS>' <DB_NAME> <<'SQL'

-- Remove all existing Theme Builder templates (header/footer/popup/single)
-- Posts and pages are NOT affected — only elementor_library entries
DELETE pm FROM <PREFIX>postmeta pm
JOIN <PREFIX>posts p ON pm.post_id = p.ID
WHERE p.post_type = 'elementor_library';

DELETE FROM <PREFIX>posts WHERE post_type = 'elementor_library';

SQL
echo 'Theme Builder templates cleared'
"
```

Then flush so Elementor stops trying to apply the deleted templates:

```bash
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP> "
  wp --path=~/files elementor flush_css && \
  wp --path=~/files cache flush && \
  echo 'Cache flushed — clean slate ready'
"
```

**What this deletes:** all `elementor_library` posts (Theme Builder header, footer, popups, single templates, archive templates).
**What this keeps:** all pages, posts, media, menus, plugin settings, Elementor Pro licence, WP users.

After this step, Phase 0 creates the new header/footer from scratch with no conflicts.

---

### Step 1d: Check & Install talk-to-build-theme

Before generating any JSON, confirm the theme is installed and active. Run:

```bash
wp --path=~/files theme list --format=table
```

**If `talk-to-build` is listed as active** → proceed.

**If not installed or not active** → install directly from GitHub:

```bash
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP> "
  cd /tmp && \
  curl -sL https://github.com/MK-Way/talk-to-build-kit/archive/refs/heads/main.tar.gz \
       -o t2b.tar.gz && \
  tar -xzf t2b.tar.gz && \
  cp -r talk-to-build-kit-main/talk-to-build-theme ~/files/wp-content/themes/ && \
  rm -rf t2b.tar.gz talk-to-build-kit-main && \
  wp --path=~/files theme activate talk-to-build && \
  echo 'talk-to-build-theme installed and activated'
"
```

This always installs the latest version from the `main` branch of `MK-Way/talk-to-build-kit`. No manual upload needed — any server, any project.

---

### Step 2: Analyse the HTML

Read the full HTML file. Before generating any JSON:

1. **List all sections** — identify each top-level block and its purpose (hero, features, testimonials, pricing, CTA, footer, etc.)
2. **Extract the design system:**
   - Primary colour (most used non-neutral colour)
   - Secondary colour
   - Background colours per section
   - Font stack (body and heading)
   - Base font size
3. **Plan the widget map** — for each section, decide which Elementor widgets to use. List this plan before writing any JSON.
4. **Flag complex sections** — anything with heavy CSS animations, SVG, or JS that cannot be mapped to native widgets → mark for `html` fallback widget.

### Step 2.1: Extract Design Tokens (run once per project)

Before generating any page JSON, extract the full design system from the source CSS and save it as `design-tokens.json`. This file is used to verify every page after generation — catching colour/font mismatches before push.

```python
import re, json, glob

def extract_design_tokens(source_folder):
    """Extract colours, font sizes, and image positioning from all source HTML files."""
    colors = set()
    font_sizes = set()
    font_families = []
    bg_positions = set()
    bg_sizes = set()
    object_positions = set()
    object_fits = set()

    html_files = glob.glob(f"{source_folder}/*.html")
    for path in html_files:
        with open(path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()

        # Colours
        colors.update(re.findall(r'#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})\b', content))
        colors.update(re.findall(r'rgba?\([^)]+\)', content))

        # Typography
        font_sizes.update(re.findall(r'font-size\s*:\s*([\d.]+(?:px|rem|em))', content))
        font_families += re.findall(r'font-family\s*:\s*([^;}{]+)', content)

        # Background image positioning
        bg_positions.update(v.strip() for v in
            re.findall(r'background-position\s*:\s*([^;}{]+)', content))
        bg_sizes.update(v.strip() for v in
            re.findall(r'background-size\s*:\s*([^;}{]+)', content))

        # Image object positioning
        object_positions.update(v.strip() for v in
            re.findall(r'object-position\s*:\s*([^;}{]+)', content))
        object_fits.update(v.strip() for v in
            re.findall(r'object-fit\s*:\s*([^;}{]+)', content))

    tokens = {
        "colors":           sorted(colors),
        "font_sizes":       sorted(font_sizes),
        "font_families":    list(dict.fromkeys(f.strip().strip("'\"") for f in font_families))[:6],
        "bg_positions":     sorted(bg_positions),
        "bg_sizes":         sorted(bg_sizes),
        "object_positions": sorted(object_positions),
        "object_fits":      sorted(object_fits),
    }

    with open('design-tokens.json', 'w') as f:
        json.dump(tokens, f, indent=2)

    print(f"Design tokens extracted from {len(html_files)} files:")
    print(f"  {len(tokens['colors'])} colours")
    print(f"  {len(tokens['font_sizes'])} font sizes")
    print(f"  bg-position values: {tokens['bg_positions']}")
    print(f"  bg-size values:     {tokens['bg_sizes']}")
    print(f"  object-position:    {tokens['object_positions']}")
    print(f"  object-fit:         {tokens['object_fits']}")
    return tokens

tokens = extract_design_tokens("C:/path/to/source/html/folder")
```

Run this once. Commit `design-tokens.json` to the project repo — it is the style reference for the entire migration.

### Step 3: Localize Images

Before generating JSON, scan for external images and localize them (same as `/t2b-wordpress` Step 2–3):

```bash
grep -oP 'src="https?://[^"]+\.(png|jpg|jpeg|gif|svg|webp)"' <HTML_FILE>
```

Download externals, upload to `/wp-content/uploads/<PROJECT>/`, rewrite URLs in the HTML.

In the Elementor JSON, all image URLs must be `/wp-content/uploads/<PROJECT>/...` — never external.

### Step 4: Generate Elementor JSON

> ⚠️ **Every section must be built with native widgets — no exceptions for regular content.**
> Do NOT use the `html` widget for page content. If a section has headings, text, images, or buttons, map them to `heading`, `text-editor`, `image`, `button` (see Widget Mapping Reference above).
> Only use `html` fallback if a section has JS interactions or CSS animations that have **no native Elementor equivalent** — and document every such case with a comment explaining why.

**Convert the HTML section by section.** For each section:

1. Create a top-level container with section background settings (colour, image, padding)
2. For multi-column layouts, nest inner containers with `"isInner": true`
3. Add widgets inside containers following the Widget Mapping Reference
4. Extract all text, colours, font sizes, and spacing from the HTML/CSS
5. Add `_tablet` and `_mobile` responsive variants for font sizes and padding
6. Generate a unique 8-char hex ID for every element: `secrets.token_hex(4)` in Python

**Conversion rules:**
- Every colour value → hex (convert rgb/rgba if needed)
- Font sizes → always in `px`, extracted from inline style or class
- Padding/margin → extracted from CSS, applied to container settings
- `<strong>` / `<em>` inside paragraphs → preserved in `text-editor` HTML content
- Background images → use `background_background: "classic"` + `background_image: { "url": "..." }`
- Gradient backgrounds → `background_background: "gradient"` + `background_gradient_color` + `background_gradient_color_b`

**When Elementor widget settings are not enough — use `custom_css`:**

Elementor widget settings cover common properties but not everything. When the source design uses `letter-spacing`, `line-height`, `text-transform`, `object-position`, `z-index`, or other CSS not directly available as a widget setting, add a `custom_css` key to that element's `settings`. Elementor scopes it automatically to that element via `.selector`.

```json
{
  "id": "a1b2c3d4",
  "elType": "widget",
  "widgetType": "heading",
  "settings": {
    "title": "Stop Thinking If,",
    "header_size": "h1",
    "title_color": "#ffffff",
    "typography_font_size": {"unit": "px", "size": 72},
    "custom_css": ".selector { letter-spacing: -2px; line-height: 1.05; text-transform: uppercase; }"
  },
  "elements": []
}
```

For containers/sections, `custom_css` scopes to the section wrapper:

```json
{
  "elType": "container",
  "settings": {
    "background_background": "classic",
    "background_color": "#0b1220",
    "custom_css": ".selector { background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/wp-content/uploads/rcas/hero.jpg') center/cover no-repeat !important; }"
  }
}
```

**Rule:** Extract ALL CSS properties from the source element. Map what you can to Elementor settings. Put everything else in `custom_css`. Never leave a styling difference unresolved — the output must be pixel-perfect before push.

**Output**: a Python file `generate_elementor.py` that builds and writes the JSON array to `elementor-data.json`.

```python
import json, secrets, re

def uid(): return secrets.token_hex(4)

def make_heading(text, tag='h2', align='center', color='#1a1a2e', size=36):
    return {"id": uid(), "elType": "widget", "widgetType": "heading", "isInner": False,
            "settings": {"title": text, "header_size": tag, "align": align,
                         "title_color": color,
                         "typography_typography": "custom",
                         "typography_font_size": {"unit": "px", "size": size},
                         "typography_font_size_tablet": {"unit": "px", "size": int(size * 0.75)},
                         "typography_font_size_mobile": {"unit": "px", "size": int(size * 0.6)},
                         "typography_font_weight": "700"}, "elements": []}

def make_text(html, align='left'):
    return {"id": uid(), "elType": "widget", "widgetType": "text-editor", "isInner": False,
            "settings": {"editor": html, "align": align}, "elements": []}

def make_button(text, url, bg='#0073aa', color='#ffffff', align='center'):
    return {"id": uid(), "elType": "widget", "widgetType": "button", "isInner": False,
            "settings": {"text": text, "link": {"url": url}, "align": align,
                         "button_text_color": color, "background_color": bg,
                         "padding": {"unit": "px", "top": "14", "right": "32",
                                     "bottom": "14", "left": "32", "isLinked": False}}, "elements": []}

def make_image(url, align='center', size='full'):
    return {"id": uid(), "elType": "widget", "widgetType": "image", "isInner": False,
            "settings": {"image": {"url": url}, "image_size": size, "align": align}, "elements": []}

def make_spacer(px=40):
    return {"id": uid(), "elType": "widget", "widgetType": "spacer", "isInner": False,
            "settings": {"space": {"unit": "px", "size": px}}, "elements": []}

def make_html(html):
    return {"id": uid(), "elType": "widget", "widgetType": "html", "isInner": False,
            "settings": {"html": html}, "elements": []}

def make_container(elements, bg_color=None, bg_image=None, padding=None,
                   direction='column', is_inner=False, width_pct=None):
    settings = {"flex_direction": direction, "content_width": "boxed" if not is_inner else "full"}
    if bg_color: settings["background_background"] = "classic"; settings["background_color"] = bg_color
    if bg_image: settings["background_background"] = "classic"; settings["background_image"] = {"url": bg_image}
    if padding:  settings["padding"] = padding
    if width_pct: settings["width"] = {"unit": "%", "size": width_pct}
    return {"id": uid(), "elType": "container", "isInner": is_inner,
            "settings": settings, "elements": elements}

# ─── BUILD PAGE ───────────────────────────────────────────────────────────────
# Replace the sections below with your actual page content

page_data = [
    # SECTION: Hero
    make_container(
        bg_color="#0b1220",
        padding={"unit": "px", "top": "120", "right": "40", "bottom": "120", "left": "40", "isLinked": False},
        elements=[
            make_heading("Your Hero Title", tag="h1", color="#ffffff", size=56),
            make_spacer(24),
            make_text('<p style="color:#cccccc;font-size:20px;">Your hero subtitle or description.</p>', align='center'),
            make_spacer(32),
            make_button("Get Started", "https://example.com/contact"),
        ]
    ),
    # Add more sections here...
]

with open('elementor-data.json', 'w', encoding='utf-8') as f:
    json.dump(page_data, f, ensure_ascii=False)

print(f'Generated {len(page_data)} sections → elementor-data.json')
```

Run locally, review the JSON, then proceed to push.

### Step 4.1: Style Verification (run before every push)

After generating the JSON for a page and **before pushing to the server**, verify that every colour and font size in the JSON exists in `design-tokens.json`. This catches copy-paste errors, wrong hex values, and off-brand colours before they go live.

```python
import json, re

def verify_page_styles(json_file, tokens_file='design-tokens.json'):
    with open(json_file, 'r', encoding='utf-8') as f:
        page_data = json.load(f)
    with open(tokens_file, 'r', encoding='utf-8') as f:
        tokens = json.load(f)

    known_colors = set(c.lower() for c in tokens['colors'])
    known_sizes       = set(tokens['font_sizes'])
    known_bg_pos      = set(v.lower() for v in tokens.get('bg_positions', []))
    known_bg_sizes    = set(v.lower() for v in tokens.get('bg_sizes', []))
    known_obj_pos     = set(v.lower() for v in tokens.get('object_positions', []))
    known_obj_fits    = set(v.lower() for v in tokens.get('object_fits', []))
    issues = []

    COLOR_KEYS  = ['color', 'text_color', 'title_color', 'background_color',
                   'button_text_color', 'border_color', 'icon_color',
                   'heading_color', 'tab_color']
    SIZE_KEYS   = ['typography_font_size', 'font_size']
    BG_POS_KEYS = ['background_position']
    BG_SZ_KEYS  = ['background_size']

    def walk(el, path=''):
        label = el.get('widgetType') or el.get('elType', '?')
        settings = el.get('settings', {})

        for key in COLOR_KEYS:
            val = settings.get(key)
            if isinstance(val, str) and val.strip():
                if val.lower() not in known_colors:
                    issues.append(f"  COLOUR  [{label}] {key}: '{val}' — not in design tokens")

        for key in SIZE_KEYS:
            val = settings.get(key)
            if isinstance(val, dict):
                size_str = f"{val.get('size')}{val.get('unit','px')}"
                if size_str not in known_sizes:
                    issues.append(f"  SIZE    [{label}] {key}: {size_str} — not in design tokens")

        # Background image positioning
        for key in BG_POS_KEYS:
            val = settings.get(key, '')
            if val and known_bg_pos and val.lower() not in known_bg_pos:
                issues.append(f"  BG-POS  [{label}] {key}: '{val}' — not in design tokens (source uses: {sorted(known_bg_pos)})")

        for key in BG_SZ_KEYS:
            val = settings.get(key, '')
            if val and known_bg_sizes and val.lower() not in known_bg_sizes:
                issues.append(f"  BG-SIZE [{label}] {key}: '{val}' — not in design tokens (source uses: {sorted(known_bg_sizes)})")

        # Image widget object-position (stored in custom CSS or _css_custom_mobile)
        custom_css = settings.get('custom_css', '') or settings.get('_element_custom_code', '')
        if custom_css:
            found_obj_pos = re.findall(r'object-position\s*:\s*([^;}{]+)', custom_css)
            for v in found_obj_pos:
                if known_obj_pos and v.strip().lower() not in known_obj_pos:
                    issues.append(f"  OBJ-POS [{label}] object-position: '{v.strip()}' — not in design tokens (source uses: {sorted(known_obj_pos)})")

        for child in el.get('elements', []):
            walk(child, path + '/' + label)

    for section in page_data:
        walk(section)

    if issues:
        print(f"⚠️  {len(issues)} style issue(s) in {json_file}:")
        for i in issues: print(i)
        print("\nCorrect these before pushing — or confirm they are intentional overrides.")
        return False
    else:
        print(f"✅  {json_file} — all styles match design tokens")
        return True

# Run before push:
verify_page_styles('generated/pages/home-data.json')
```

**If issues are found:** correct the JSON values to match the source design tokens. Only proceed to push when the verification passes — or when you have explicitly confirmed a value is an intentional override (e.g. a hover state or accessibility improvement).

---

### Step 5: Push to WordPress Database

The push script uses **UPSERT logic** — if a page with the same slug already exists it updates it in place; if not it creates a new one. This prevents duplicate pages when migrating a site that already has WP pages.

SCP the JSON file and push script to server, then execute:

```python
import subprocess, sys, json, re

json_file  = '/tmp/<PROJECT>/elementor-data.json'
db_user    = '<DB_USER>'
db_pass    = '<DB_PASS>'
db_name    = '<DB_NAME>'
prefix     = '<TABLE_PREFIX>'
page_title = '<PAGE_TITLE>'
page_slug  = '<PAGE_SLUG>'
template   = 'elementor_header_footer'
el_version = '3.25.0'  # match server's Elementor version

with open(json_file, 'r', encoding='utf-8') as f:
    raw_json = f.read()

escaped_json = raw_json.replace('\\', '\\\\').replace("'", "\\'")

# ── 1. Check if page already exists by slug ──────────────────────────────────
check_sql = (
    f"SELECT ID FROM {prefix}posts "
    f"WHERE post_name = '{page_slug}' AND post_type = 'page' "
    f"AND post_status != 'trash' LIMIT 1;"
)

check = subprocess.run(
    ['mysql', '-u', db_user, '-p' + db_pass, '-N', '-s', db_name],
    input=check_sql, capture_output=True, text=True
)
existing_id = check.stdout.strip()

# ── 2. Build SQL based on whether page exists ─────────────────────────────────
elementor_meta_keys = "'_elementor_data','_elementor_edit_mode','_wp_page_template','_elementor_version'"

if existing_id:
    # UPDATE existing page — never creates duplicates
    print(f'Page "{page_slug}" exists (ID {existing_id}) — updating in place')
    sql = f"""
UPDATE {prefix}posts
   SET post_title = '{page_title}', post_modified = NOW(), post_modified_gmt = NOW(), post_content = ''
 WHERE ID = {existing_id};

DELETE FROM {prefix}postmeta
 WHERE post_id = {existing_id} AND meta_key IN ({elementor_meta_keys});

INSERT INTO {prefix}postmeta (post_id, meta_key, meta_value) VALUES
  ({existing_id}, '_elementor_data',      '{escaped_json}'),
  ({existing_id}, '_elementor_edit_mode', 'builder'),
  ({existing_id}, '_wp_page_template',    '{template}'),
  ({existing_id}, '_elementor_version',   '{el_version}');
"""
else:
    # INSERT new page — slug does not exist yet
    print(f'Page "{page_slug}" not found — creating new page')
    sql = f"""
INSERT INTO {prefix}posts
  (post_author, post_date, post_date_gmt, post_content, post_title,
   post_excerpt, post_status, comment_status, ping_status, post_name,
   post_type, post_modified, post_modified_gmt, to_ping, pinged, post_content_filtered)
VALUES
  (1, NOW(), NOW(), '', '{page_title}', '', 'publish', 'closed', 'closed',
   '{page_slug}', 'page', NOW(), NOW(), '', '', '');

SET @page_id = LAST_INSERT_ID();

INSERT INTO {prefix}postmeta (post_id, meta_key, meta_value) VALUES
  (@page_id, '_elementor_data',      '{escaped_json}'),
  (@page_id, '_elementor_edit_mode', 'builder'),
  (@page_id, '_wp_page_template',    '{template}'),
  (@page_id, '_elementor_version',   '{el_version}');
"""

# ── 3. Execute ────────────────────────────────────────────────────────────────
sql_file = f'/tmp/<PROJECT>/upsert-{page_slug}.sql'
with open(sql_file, 'w') as f:
    f.write(sql)

result = subprocess.run(
    ['mysql', '-u', db_user, '-p' + db_pass, db_name],
    stdin=open(sql_file), capture_output=True, text=True
)

if result.returncode == 0:
    action = 'updated' if existing_id else 'created'
    print(f'SUCCESS: "{page_title}" ({page_slug}) {action}')
else:
    print('ERROR:', result.stderr)

sys.exit(result.returncode)
```

```bash
scp -i <YOUR_SSH_KEY_PATH> elementor-data.json push-elementor.py <SITE_USER>@<IP>:/tmp/<PROJECT>/
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP> "python3 /tmp/<PROJECT>/push-elementor.py"
```

### Step 6: Flush CSS + Caches

After any Elementor DB insert, Elementor's CSS cache must also be flushed — not just Redis/Nginx:

```bash
ssh -i <YOUR_SSH_KEY_PATH> <SITE_USER>@<IP> "cd ~/files && \
  wp elementor flush_css && \
  redis-cli --user <REDIS_USER> --pass '<REDIS_PASS>' FLUSHALL && \
  wp cache flush && \
  wp nginx-helper purge-all"
```

`wp elementor flush_css` regenerates the page's compiled CSS. Without this, the page may render without any styling.

### Step 7: Verify

```bash
# HTTP 200
curl -s -o /dev/null -w "%{http_code}" https://<SITE>/<SLUG>/

# Elementor is rendering (not WP default)
curl -s https://<SITE>/<SLUG>/ | grep -i 'elementor'

# Page is editable in Elementor admin
# Log into WP Admin → Pages → Edit with Elementor → confirm widgets load
```

**Final check**: open the page in Elementor editor and confirm each section is editable. Spot-check 3 widgets — click to edit, confirm text/colour is accessible.

---

## Updating an Existing Page

The push script in Step 5 already handles this automatically — it detects the page by slug and updates in place. No manual SQL needed, no duplicate pages.

For subsequent updates (Anastasia sends a revised HTML):

1. Re-run the analysis (Step 2) — identify what changed
2. Regenerate the affected sections in `generate_elementor.py`
3. Re-run the Step 5 push script with the same `page_slug` — it will UPDATE, not INSERT
4. Flush CSS + caches (Step 6)
5. Verify in editor

---

## Handling Complex Sections

Some vibe-coded sections cannot be cleanly mapped to native Elementor widgets. Use the `html` fallback widget but follow these rules:

1. **Inline the CSS** — move any section-specific CSS into a `<style>` tag inside the HTML widget
2. **Localize all images** — no external URLs inside HTML widgets
3. **Isolate the scope** — prefix all CSS classes with the section name to avoid conflicts: `.hero-v2-card` not `.card`
4. **Document it** — add a comment: `<!-- HTML WIDGET: complex animation — Elementor cannot edit this natively -->`

Mixed pages (some native widgets + some HTML fallback) are valid and common. Native widgets where possible, HTML fallback where necessary.

---


## Error Handling

| Error | Cause | Solution |
|---|---|---|
| Page loads with no styling | Elementor CSS cache not flushed | Run `wp elementor flush_css` |
| "This content cannot be edited" in Elementor | `_elementor_edit_mode` not set to `builder` | Check postmeta insert — add missing meta |
| Widget shows as "Unknown widget" | Widget not registered (plugin missing) | Use `html` fallback or install required plugin |
| Layout broken on mobile | Responsive variants missing | Add `_tablet` and `_mobile` settings to containers and headings |
| Images not loading | External URLs in JSON or wrong upload path | Re-run localization, check `/wp-content/uploads/<PROJECT>/` |
| Elementor editor crashes on page open | Malformed JSON in `_elementor_data` | Validate JSON with `python3 -c "import json; json.load(open('elementor-data.json'))"` |
| Page shows WP default (not Elementor) | `_elementor_edit_mode` missing or wrong template | Check all 3 postmeta: `_elementor_data`, `_elementor_edit_mode`, `_wp_page_template` |
| Theme Builder header/footer not showing | Display condition not applied or cache stale | Flush with `wp elementor flush_css`; if still missing, set condition manually in WP Admin → Templates → Theme Builder |
| Pages have no header/footer at all | Wrong page template — `elementor_canvas` used instead of `elementor_header_footer` | Update `_wp_page_template` postmeta to `elementor_header_footer` for all affected pages |

---

## Learnings & Improvements

| Date | Learning | Change Made |
|---|---|---|
| 2026-06-09 | Elementor stores layout as `_elementor_data` JSON array in postmeta — `post_content` is empty | Push script writes to postmeta, not post_content |
| 2026-06-09 | `wp elementor flush_css` is mandatory after DB insert — Redis/Nginx flush alone is not enough | Added as Step 6, before cache flush |
| 2026-06-09 | Elementor 3.6+ uses Containers (flexbox) — older Section/Column format still works but containers are the standard | Directive uses containers; fallback to section/column if server runs Elementor < 3.6 |
| 2026-06-10 | `elementor_canvas` bypasses Theme Builder — pages have no header/footer even when Theme Builder is configured | All pages in full-site migrations use `elementor_header_footer`; Phase 0 must run first to build the shared header/footer |
| 2026-06-10 | Elementor 3.6+ containers (flexbox) do NOT render in Theme Builder context (header/footer templates) — content renders empty | Phase 0 always uses classic `section → column → html widget` format; containers only for regular page content (Steps 2+) |
| 2026-06-10 | Elementor wraps Theme Builder header in `.elementor-location-header` block div — pushes content down even when original header CSS uses `position: fixed/absolute` | Step 0e.1 now mandatory: detect fixed/absolute header in source CSS and add `.elementor-location-header { position: fixed; }` fix to header template |
| 2026-06-10 | HTML widget was applied to regular page content instead of native widgets — client cannot edit anything in Elementor | Added ❌ rule after Widget Mapping table and ⚠️ warning at top of Step 4: html widget is ONLY for Phase 0 and truly unmappable sections |
| 2026-06-10 | `background-position` and `object-position` mismatches on sections and images are a common source of visual bugs not caught by colour/font checks | Step 2.1 now extracts bg_positions, bg_sizes, object_positions, object_fits from source; Step 4.1 verifies them before every push |

---

## Related

- **Canvas deploy (no Elementor)**: Use `/t2b-wordpress` — faster, no Elementor required
- **Site setup**: Use `/empathyhost` Operation 10A for demolish + theme install
- **Theme source**: `https://github.com/MK-Way/talk-to-build-kit`
- **Elementor developer docs**: `https://developers.elementor.com/docs/data-structure/`
