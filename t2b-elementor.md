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

## Workflow Steps

### Step 1: Gather Site Credentials

```bash
ssh -i <YOUR_SSH_KEY_PATH> -o StrictHostKeyChecking=no <SITE_USER>@<IP>
grep -E 'DB_NAME|DB_USER|DB_PASSWORD|table_prefix|WP_REDIS' ~/files/wp-config.php
# Also check Elementor version:
wp --path=~/files plugin get elementor --field=version
```

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

SCP the JSON file and push script to server, then execute:

```python
import subprocess, sys, json

json_file  = '/tmp/<PROJECT>/elementor-data.json'
db_user    = '<DB_USER>'
db_pass    = '<DB_PASS>'
db_name    = '<DB_NAME>'
prefix     = '<TABLE_PREFIX>'
page_title = '<PAGE_TITLE>'
page_slug  = '<PAGE_SLUG>'
template   = 'elementor_header_footer'  # or 'elementor_canvas'
el_version = '3.25.0'  # match server's Elementor version

with open(json_file, 'r', encoding='utf-8') as f:
    raw_json = f.read()

# Escape for MySQL
escaped_json = raw_json.replace('\\', '\\\\').replace("'", "\\'")

sql = []

# 1. Insert page
sql.append(
    "INSERT INTO {0}posts (post_author, post_date, post_date_gmt, post_content, post_title, "
    "post_excerpt, post_status, comment_status, ping_status, post_name, post_type, "
    "post_modified, post_modified_gmt, to_ping, pinged, post_content_filtered) "
    "VALUES (1, NOW(), NOW(), '', '{1}', '', 'publish', 'closed', 'closed', "
    "'{2}', 'page', NOW(), NOW(), '', '', '');".format(prefix, page_title, page_slug)
)
sql.append("SET @page_id = LAST_INSERT_ID();")

# 2. Elementor postmeta
sql.append("INSERT INTO {0}postmeta (post_id, meta_key, meta_value) VALUES (@page_id, '_elementor_data', '{1}');".format(prefix, escaped_json))
sql.append("INSERT INTO {0}postmeta (post_id, meta_key, meta_value) VALUES (@page_id, '_elementor_edit_mode', 'builder');".format(prefix))
sql.append("INSERT INTO {0}postmeta (post_id, meta_key, meta_value) VALUES (@page_id, '_wp_page_template', '{1}');".format(prefix, template))
sql.append("INSERT INTO {0}postmeta (post_id, meta_key, meta_value) VALUES (@page_id, '_elementor_version', '{1}');".format(prefix, el_version))

full_sql = '\n'.join(sql)
sql_file = '/tmp/<PROJECT>/insert-elementor.sql'

with open(sql_file, 'w') as f:
    f.write(full_sql)

result = subprocess.run(
    ['mysql', '-u', db_user, '-p' + db_pass, db_name],
    stdin=open(sql_file), capture_output=True, text=True
)

if result.returncode == 0:
    print(f'SUCCESS: "{page_title}" created as native Elementor page')
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

For subsequent updates (Anastasia sends a revised HTML):

1. Re-run the analysis (Step 2) — identify what changed
2. Regenerate the affected sections in `generate_elementor.py`
3. Run UPDATE instead of INSERT in the push script:

```sql
UPDATE <PREFIX>postmeta
SET meta_value = '<ESCAPED_JSON>'
WHERE post_id = (SELECT ID FROM <PREFIX>posts WHERE post_name = '<SLUG>' AND post_type = 'page')
  AND meta_key = '_elementor_data';
```

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

## Theme Builder (Header & Footer in Elementor)

If the client wants their header and footer built in Elementor (not the WP theme):

1. In Elementor Pro: Templates → Theme Builder → Header (New)
2. Build the nav using the Nav Menu widget (Pro) — assign a WP menu
3. Set display condition: Entire Site
4. Repeat for Footer

This replaces the WP theme header/footer entirely. The page template for all pages becomes `elementor_canvas` since Elementor provides the header/footer.

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

---

## Learnings & Improvements

| Date | Learning | Change Made |
|---|---|---|
| 2026-06-09 | Elementor stores layout as `_elementor_data` JSON array in postmeta — `post_content` is empty | Push script writes to postmeta, not post_content |
| 2026-06-09 | `wp elementor flush_css` is mandatory after DB insert — Redis/Nginx flush alone is not enough | Added as Step 6, before cache flush |
| 2026-06-09 | Elementor 3.6+ uses Containers (flexbox) — older Section/Column format still works but containers are the standard | Directive uses containers; fallback to section/column if server runs Elementor < 3.6 |

---

## Related

- **Canvas deploy (no Elementor)**: Use `/t2b-wordpress` — faster, no Elementor required
- **Site setup**: Use `/empathyhost` Operation 10A for demolish + theme install
- **Theme source**: `https://github.com/MK-Way/talk-to-build-kit`
- **Elementor developer docs**: `https://developers.elementor.com/docs/data-structure/`
