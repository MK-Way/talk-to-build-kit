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

Same Python approach as page content — build the JSON locally, write to files.

**Header example** — logo left, nav centre, CTA right:

```python
import json, secrets

def uid(): return secrets.token_hex(4)

header_data = [
    {
        "id": uid(), "elType": "container", "isInner": False,
        "settings": {
            "flex_direction": "row",
            "flex_align_items": "center",
            "content_width": "boxed",
            "background_background": "classic",
            "background_color": "#ffffff",
            "padding": {"unit": "px", "top": "20", "right": "40", "bottom": "20", "left": "40", "isLinked": False},
            "width": {"unit": "px", "size": 1200}
        },
        "elements": [
            # Logo (left)
            {
                "id": uid(), "elType": "widget", "widgetType": "theme-site-logo",
                "isInner": False,
                "settings": {"width": {"unit": "px", "size": 140}, "align": "left"},
                "elements": []
            },
            # Nav menu (centre — flex-grow)
            {
                "id": uid(), "elType": "widget", "widgetType": "nav-menu",
                "isInner": False,
                "settings": {
                    "menu": "main-navigation",  # WP menu slug
                    "layout": "horizontal",
                    "align_items": "center",
                    "typography_font_size": {"unit": "px", "size": 15},
                    "color": "#1a1a2e",
                    "color_hover": "#0073aa"
                },
                "elements": []
            },
            # CTA button (right)
            {
                "id": uid(), "elType": "widget", "widgetType": "button",
                "isInner": False,
                "settings": {
                    "text": "Get Started",
                    "link": {"url": "/contact/"},
                    "align": "right",
                    "button_text_color": "#ffffff",
                    "background_color": "#0073aa",
                    "border_radius": {"unit": "px", "top": 6, "right": 6, "bottom": 6, "left": 6},
                    "padding": {"unit": "px", "top": "10", "right": "24", "bottom": "10", "left": "24", "isLinked": False}
                },
                "elements": []
            }
        ]
    }
]

with open('header-data.json', 'w', encoding='utf-8') as f:
    json.dump(header_data, f, ensure_ascii=False)
print('header-data.json written')
```

**Footer example** — two columns: links left, copyright + socials right:

```python
footer_data = [
    {
        "id": uid(), "elType": "container", "isInner": False,
        "settings": {
            "flex_direction": "row",
            "flex_align_items": "flex-start",
            "content_width": "boxed",
            "background_background": "classic",
            "background_color": "#0b1220",
            "padding": {"unit": "px", "top": "60", "right": "40", "bottom": "40", "left": "40", "isLinked": False}
        },
        "elements": [
            # Left: logo + footer nav
            {
                "id": uid(), "elType": "container", "isInner": True,
                "settings": {"flex_direction": "column", "width": {"unit": "%", "size": 50}},
                "elements": [
                    {
                        "id": uid(), "elType": "widget", "widgetType": "theme-site-logo",
                        "isInner": False,
                        "settings": {"width": {"unit": "px", "size": 120}, "align": "left"},
                        "elements": []
                    },
                    {
                        "id": uid(), "elType": "widget", "widgetType": "nav-menu",
                        "isInner": False,
                        "settings": {
                            "menu": "footer-navigation",
                            "layout": "vertical",
                            "color": "#aaaaaa",
                            "color_hover": "#ffffff",
                            "typography_font_size": {"unit": "px", "size": 14}
                        },
                        "elements": []
                    }
                ]
            },
            # Right: socials + copyright
            {
                "id": uid(), "elType": "container", "isInner": True,
                "settings": {"flex_direction": "column", "flex_align_items": "flex-end", "width": {"unit": "%", "size": 50}},
                "elements": [
                    {
                        "id": uid(), "elType": "widget", "widgetType": "social-icons",
                        "isInner": False,
                        "settings": {
                            "social_icon_list": [
                                {"_id": uid(), "social_icon": {"value": "fab fa-instagram", "library": "fa-brands"}, "link": {"url": "https://instagram.com/yourprofile"}},
                                {"_id": uid(), "social_icon": {"value": "fab fa-linkedin", "library": "fa-brands"}, "link": {"url": "https://linkedin.com/company/yourco"}}
                            ],
                            "icon_color": "#aaaaaa",
                            "icon_hover_color": "#ffffff",
                            "icon_size": {"unit": "px", "size": 20}
                        },
                        "elements": []
                    },
                    {
                        "id": uid(), "elType": "widget", "widgetType": "text-editor",
                        "isInner": False,
                        "settings": {
                            "editor": "<p style=\"color:#777777;font-size:13px;\">© 2026 Company Name. All rights reserved.</p>",
                            "align": "right"
                        },
                        "elements": []
                    }
                ]
            }
        ]
    }
]

with open('footer-data.json', 'w', encoding='utf-8') as f:
    json.dump(footer_data, f, ensure_ascii=False)
print('footer-data.json written')
```

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

### Step 3: Localize Images

Before generating JSON, scan for external images and localize them (same as `/t2b-wordpress` Step 2–3):

```bash
grep -oP 'src="https?://[^"]+\.(png|jpg|jpeg|gif|svg|webp)"' <HTML_FILE>
```

Download externals, upload to `/wp-content/uploads/<PROJECT>/`, rewrite URLs in the HTML.

In the Elementor JSON, all image URLs must be `/wp-content/uploads/<PROJECT>/...` — never external.

### Step 4: Generate Elementor JSON

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

---

## Related

- **Canvas deploy (no Elementor)**: Use `/t2b-wordpress` — faster, no Elementor required
- **Site setup**: Use `/empathyhost` Operation 10A for demolish + theme install
- **Theme source**: `https://github.com/MK-Way/talk-to-build-kit`
- **Elementor developer docs**: `https://developers.elementor.com/docs/data-structure/`
