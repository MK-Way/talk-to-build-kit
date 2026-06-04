---
description: Deploy HTML to WordPress via Talk-to-Build pipeline
execution_scripts: []
---

## Purpose

Deploy HTML files (from Anastasia, Figma Decode, vibe coding tools, or hand-coded) to a WordPress site on xCloud.

**Default behaviour: standard WP page (`page.php`).** Standard pages use the WP theme's shared header and footer — the site nav is managed once in WP Admin → Menus, not duplicated in every HTML file.

**Canvas (`canvas.php`) is the exception**, used only when explicitly requested or when a page genuinely needs its own header/footer (true landing pages, thank-you pages, campaign pages).

This rule exists because using Canvas for every page means the nav is hardcoded into each HTML file — changing one menu link requires re-deploying every page. That is not WordPress. That is a static site pretending to be WordPress.

This is the Talk-to-Build method: Mike talks, Claude builds, site updates live.

---

## Page Type Decision

**Default: `page.php` for everything.**

Canvas (`canvas.php`) is used only in two situations:
1. **The homepage** — when it has a fully custom hero/design that doesn't use the site's standard nav/footer layout
2. **Explicitly requested** — Mike or the client says "this page has no header/footer" or "this is a standalone landing page"

Everything else — About, Contact, Services, Team, Blog, Privacy, Thank You, Insights, Coaching — is `page.php`.

| Situation | Template |
|---|---|
| Normal site page (about, contact, services, team, blog…) | **`page.php`** (default) |
| Thank-you page, confirmation page | **`page.php`** |
| Privacy policy, legal pages | **`page.php`** |
| Homepage with standard nav + hero | **`page.php`** |
| Homepage explicitly designed as a standalone landing page | **`canvas.php`** |
| Explicitly told: "no header/footer on this page" | **`canvas.php`** |

**When in doubt: `page.php`.**

---

## Required Inputs

Ask the user for:
- **HTML file**: Local path (e.g., `C:\Users\mike\projects\rcas\about.html`)
- **Target site**: xCloud domain (e.g., `talktobuild2-2o71.wp1.host`)
- **Page title**: Display name in WordPress
- **Page slug**: URL path (e.g., `about`, `contact`, `coaching`)
- **Page type**: Standard page (default) or Canvas? Use the decision table above if unsure.
- **Set as front page?** (optional): Homepage only. Default: no.

---

## Prerequisites

Before first use on a new site:
1. **Site must exist in xCloud dashboard** — Mike creates this from the UI
2. **Talk-to-Build theme must be installed and active** — see Theme Installation below
3. **SSH access confirmed**: `ssh -i C:/Users/mike/.ssh/xcloud-claude-rsa -o StrictHostKeyChecking=no claude-mcp1@216.128.181.113`
4. **For standard pages: WP theme header/footer must match the creative's design** — see First-Time Site Setup below

### Theme Installation (first time only)

```bash
# 1. Zip the theme
cd C:/Users/mike/projects/mk-way/talk-to-build-kit/talk-to-build-theme && zip -r /tmp/talk-to-build.zip .

# 2. Upload to server
scp -i C:/Users/mike/.ssh/xcloud-claude-rsa /tmp/talk-to-build.zip claude-mcp1@216.128.181.113:/tmp/

# 3. Extract into themes directory
ssh claude-mcp1@216.128.181.113 "echo '<SUDO_PASS>' | sudo -S unzip /tmp/talk-to-build.zip -d /var/www/<SITE>/wp-content/themes/talk-to-build/"
ssh claude-mcp1@216.128.181.113 "echo '<SUDO_PASS>' | sudo -S chown -R <SITE_USER>:<SITE_USER> /var/www/<SITE>/wp-content/themes/talk-to-build/"

# 4. Activate via MySQL
mysql -u <DB_USER> -p'<DB_PASS>' <DB_NAME> -e "UPDATE <PREFIX>options SET option_value='talk-to-build' WHERE option_name IN ('template','stylesheet');"
```

### First-Time Site Setup (standard pages only)

Before deploying standard pages, the WP theme's `header.php` and `footer.php` must reflect the creative's nav and footer design. This is a one-time step per site.

1. Take one of the HTML files (e.g., `index.html` or `about.html`)
2. Extract the `<header>` / `<nav>` block → adapt to PHP → upload as `header.php`
3. Extract the `<footer>` block → adapt to PHP → upload as `footer.php`
4. Register the nav menus in WP Admin → Appearance → Menus
5. All subsequent standard pages will inherit this header/footer automatically

Theme files live at: `/var/www/<SITE>/wp-content/themes/talk-to-build/`

---

## Workflow Steps

### Step 1: Gather Site Credentials

```bash
ssh -i C:/Users/mike/.ssh/xcloud-claude-rsa -o StrictHostKeyChecking=no claude-mcp1@216.128.181.113
grep -E 'DB_NAME|DB_USER|DB_PASSWORD|table_prefix|WP_REDIS' /var/www/<SITE>/wp-config.php
ls -la /var/www/<SITE>/wp-config.php  # note the site user from file owner
```

You need: `DB_USER`, `DB_PASS`, `DB_NAME`, `table_prefix`, `SITE_USER`, `WP_REDIS_PASSWORD`

### Step 2: Scan HTML for External Images

```bash
grep -oP 'src="https?://[^"]+\.(png|jpg|jpeg|gif|svg|webp)"' <HTML_FILE>
grep -oP "src='https?://[^']+\.(png|jpg|jpeg|gif|svg|webp)'" <HTML_FILE>
grep -oP 'url\(https?://[^)]+\)' <HTML_FILE>
```

Common offenders: `imgur.com`, `images.unsplash.com`, `i.pravatar.cc`, `via.placeholder.com`

### Step 3: Download and Upload Images

```bash
# Download locally
mkdir -p C:/Users/mike/projects/<PROJECT>/images/
curl -L -o C:/Users/mike/projects/<PROJECT>/images/<filename> "<URL>"

# Create directory on server
ssh claude-mcp1@216.128.181.113 "echo '<SUDO_PASS>' | sudo -S mkdir -p /var/www/<SITE>/wp-content/uploads/<PROJECT>/"
ssh claude-mcp1@216.128.181.113 "echo '<SUDO_PASS>' | sudo -S chown <SITE_USER>:<SITE_USER> /var/www/<SITE>/wp-content/uploads/<PROJECT>/"

# Upload
scp -i C:/Users/mike/.ssh/xcloud-claude-rsa C:/Users/mike/projects/<PROJECT>/images/* claude-mcp1@216.128.181.113:/tmp/
ssh claude-mcp1@216.128.181.113 "echo '<SUDO_PASS>' | sudo -S mv /tmp/<images> /var/www/<SITE>/wp-content/uploads/<PROJECT>/ && sudo -S chown <SITE_USER>:<SITE_USER> /var/www/<SITE>/wp-content/uploads/<PROJECT>/*"
```

### Step 4: Localize Image URLs

Copy the original HTML and replace all external image URLs with local `/wp-content/uploads/` paths:

```
https://imgur.com/xyz.png  →  /wp-content/uploads/<PROJECT>/logo.png
https://i.pravatar.cc/100  →  /wp-content/uploads/<PROJECT>/avatar-1.jpg
```

Save as `<page>-localized.html`. Keep the original intact.

### Step 4b: Extract WP Content

AI tools and vibe coding tools output a **full HTML document**. WordPress needs specific content depending on page type.

**Run this Python script locally:**

```python
import re, sys

input_file  = '<page>-localized.html'
output_file = '<page>-wp.html'
page_type   = 'standard'   # 'standard' or 'canvas'

with open(input_file, 'r', encoding='utf-8') as f:
    full_html = f.read()

if page_type == 'canvas':
    # Canvas: full body content + head styles (WP provides <html>/<head>/<body> shell)
    styles = re.findall(r'<style[^>]*>[\s\S]*?</style>', full_html, re.IGNORECASE)
    links  = re.findall(r'<link[^>]+rel=["\']stylesheet["\'][^>]*/?>',  full_html, re.IGNORECASE)
    body_match = re.search(r'<body[^>]*>([\s\S]*?)</body>', full_html, re.IGNORECASE)
    body_content = body_match.group(1).strip() if body_match else full_html
    resource_block = '\n'.join(links + styles)
    wp_content = (resource_block + '\n' + body_content).strip() if resource_block else body_content

else:
    # Standard page: main content only — WP theme provides the header and footer
    # Try <main> first, then content between </header> and <footer>
    main_match = re.search(r'<main[^>]*>([\s\S]*?)</main>', full_html, re.IGNORECASE)
    if main_match:
        wp_content = main_match.group(1).strip()
        print('Extracted: <main> element')
    else:
        # Fallback: content between </header> and <footer>
        between = re.search(r'</header>([\s\S]*?)<footer', full_html, re.IGNORECASE)
        if between:
            wp_content = between.group(1).strip()
            print('Extracted: content between </header> and <footer>')
        else:
            # Last resort: full body (flag for manual review)
            body_match = re.search(r'<body[^>]*>([\s\S]*?)</body>', full_html, re.IGNORECASE)
            wp_content = body_match.group(1).strip() if body_match else full_html
            print('WARNING: Could not find <main> or header/footer markers. Full body used — review manually.')

with open(output_file, 'w', encoding='utf-8') as f:
    f.write(wp_content)

print(f'WP content: {len(wp_content)} chars → {output_file}')
```

`<page>-wp.html` is what gets pushed to WordPress. Review the output before pushing — especially for standard pages, confirm the header/footer were correctly stripped.

### Step 5: Upload to Server

```bash
scp -i C:/Users/mike/.ssh/xcloud-claude-rsa <page>-wp.html claude-mcp1@216.128.181.113:/tmp/<PROJECT>/<page>-wp.html
```

### Step 6: Push to WordPress Database

Create locally, SCP to server, execute:

```python
import subprocess, sys

html_file  = '/tmp/<PROJECT>/<page>-wp.html'
db_user    = '<DB_USER>'
db_pass    = '<DB_PASS>'
db_name    = '<DB_NAME>'
prefix     = '<TABLE_PREFIX>'
page_title = '<PAGE_TITLE>'
page_slug  = '<PAGE_SLUG>'
page_type  = 'standard'   # 'standard' or 'canvas'

with open(html_file, 'r', encoding='utf-8') as f:
    content = f.read()

escaped = content.replace('\\', '\\\\').replace("'", "\\'")

sql = []

# INSERT (new page)
sql.append(
    "INSERT INTO {0}posts (post_author, post_date, post_date_gmt, post_content, post_title, "
    "post_excerpt, post_status, comment_status, ping_status, post_name, post_type, "
    "post_modified, post_modified_gmt, to_ping, pinged, post_content_filtered) "
    "VALUES (1, NOW(), NOW(), '{1}', '{2}', '', 'publish', 'closed', 'closed', "
    "'{3}', 'page', NOW(), NOW(), '', '', '');".format(prefix, escaped, page_title, page_slug)
)
sql.append("SET @page_id = LAST_INSERT_ID();")

# Template: canvas.php only if canvas page; default otherwise
if page_type == 'canvas':
    sql.append(
        "INSERT INTO {0}postmeta (post_id, meta_key, meta_value) "
        "VALUES (@page_id, '_wp_page_template', 'canvas.php');".format(prefix)
    )
# Standard pages: no postmeta needed — WP uses page.php by default

# Front page (uncomment if needed)
# sql.append("UPDATE {0}options SET option_value='page' WHERE option_name='show_on_front';".format(prefix))
# sql.append("UPDATE {0}options SET option_value=@page_id WHERE option_name='page_on_front';".format(prefix))

full_sql = '\n'.join(sql)
sql_file = '/tmp/<PROJECT>/insert.sql'

with open(sql_file, 'w') as f:
    f.write(full_sql)

result = subprocess.run(
    ['mysql', '-u', db_user, '-p' + db_pass, db_name],
    stdin=open(sql_file, 'r'), capture_output=True, text=True
)

if result.returncode == 0:
    template_label = 'Canvas' if page_type == 'canvas' else 'Standard (page.php)'
    print(f'SUCCESS: "{page_title}" created — template: {template_label}')
else:
    print('ERROR:', result.stderr)

sys.exit(result.returncode)
```

**For UPDATE (existing page):**
```sql
UPDATE <PREFIX>posts
SET post_content = '<ESCAPED_HTML>', post_modified = NOW(), post_modified_gmt = NOW()
WHERE post_name = '<PAGE_SLUG>' AND post_type = 'page';
```

```bash
scp -i C:/Users/mike/.ssh/xcloud-claude-rsa push-page.py claude-mcp1@216.128.181.113:/tmp/<PROJECT>/push-page.py
ssh claude-mcp1@216.128.181.113 "python3 /tmp/<PROJECT>/push-page.py"
```

### Step 7: Flush ALL Caches

```bash
# Redis
redis-cli --user <REDIS_USER> --pass <REDIS_PASS> FLUSHALL

# Nginx FastCGI
echo '<SUDO_PASS>' | sudo -S find /var/run/nginx-cache/ /tmp/nginx-cache/ /var/cache/nginx/ -type f -delete 2>/dev/null
echo '<SUDO_PASS>' | sudo -S nginx -s reload
```

`WP_REDIS_PASSWORD` in wp-config.php is an array: `["username", "password"]`. Use both.

### Step 8: Verify

```bash
# HTTP 200
curl -s -o /dev/null -w "%{http_code}" https://<SITE>/<SLUG>/

# WP hooks are firing (wp_head active)
curl -s https://<SITE>/<SLUG>/ | grep -i 'generator.*WordPress'

# For standard pages: confirm theme header is present (e.g. nav class or logo)
curl -s https://<SITE>/<SLUG>/ | grep 'site-header'

# For canvas pages: confirm canvas content is present
curl -s https://<SITE>/<SLUG>/ | grep '<a known class or text from your HTML>'

# Images
curl -s -o /dev/null -w "%{http_code}" https://<SITE>/wp-content/uploads/<PROJECT>/logo.png
```

---

## Updating an Existing Page

1. Get updated HTML from Anastasia
2. Replace images if changed (Step 3)
3. Localize image URLs (Step 4)
4. Extract WP content (Step 4b) — same page type as original
5. Upload (Step 5)
6. Run UPDATE query (Step 6 — update variant)
7. Flush caches (Step 7)
8. Verify (Step 8)

~2 minutes once credentials are known.

---

## Multi-Page Sites

- **Standard pages** share the WP theme header/footer — update nav once in WP Admin → Menus
- **Canvas pages** each have their own header/footer in the HTML
- Only ONE page is set as front page (`show_on_front = 'page'`, `page_on_front = <ID>`)
- Blog posts use WP's native post system (`single.php` / `archive.php`)
- 404 page: edit `404.php` in the theme, or set via WP Admin

---

## Known Sites

| Site | Domain | DB User | DB Name | Prefix | Site User | Redis Auth |
|------|--------|---------|---------|--------|-----------|------------|
| Talk-to-Build LP | talktobuild2-2o71.wp1.host | u29_talk_to_build_2 | db29_talk_to_build_2 | bkhn_ | u29_talk_to_bui | u29_talk_to_bui / CP8LAN3A246uD5caQVQW9wSj6zW4t0UC |

---

## Error Handling

| Error | Cause | Solution |
|---|---|---|
| `Field 'to_ping' doesn't have a default value` | MySQL strict mode | Add `to_ping, pinged, post_content_filtered` with empty string defaults |
| `NOAUTH Authentication required` (Redis) | Redis needs user+pass | `redis-cli --user <u> --pass <p> FLUSHALL` |
| Site shows WP default output after deploy | Nginx FastCGI cache | Flush Nginx cache + reload |
| Site shows WP default output (no cache) | `show_on_front` / `page_on_front` reset by WP recovery mode | Re-set via MySQL, flush caches |
| `ModuleNotFoundError: mysql.connector` | Not installed on xCloud | Use `mysql` CLI approach |
| `LOAD_FILE returns NULL` | MySQL FILE privilege missing | Read file in Python, don't use LOAD_FILE |
| PHP syntax error in functions.php | Bad `$` escaping in heredoc | NEVER write PHP via bash heredoc. SCP files instead |
| Standard page shows creative's nav instead of WP nav | Content extraction included the `<header>` block | Re-run Step 4b — check `<main>` extraction worked correctly |
| Standard page has no styling | Page content extracted correctly but theme CSS doesn't match | Theme `header.php`/`footer.php` needs to match creative's CSS |

---

## Learnings & Improvements

| Date | Learning | Change Made |
|---|---|---|
| 2026-03-09 | `mysql.connector` not installed on xCloud | Push script uses `subprocess.run(['mysql', ...])` with SQL piped to stdin |
| 2026-03-09 | MySQL strict mode requires `to_ping`, `pinged`, `post_content_filtered` | Added these fields with empty string defaults |
| 2026-03-09 | WP recovery mode resets `show_on_front` / `page_on_front` | Always verify front page settings after theme changes |
| 2026-03-09 | Redis uses ACL auth (username + password) | Get both from `WP_REDIS_PASSWORD` array in wp-config.php |
| 2026-03-09 | NEVER write PHP via bash heredoc — `$` escaping fails | Always SCP files to server |
| 2026-03-09 | Nginx FastCGI cache is the #1 cause of "deploy didn't work" | Always flush Nginx after any DB change |
| 2026-03-09 | Vibe coding tools can't host images | WordPress uploads directory is the image container |
| 2026-06-04 | Canvas for all pages = nav duplicated in every HTML file = unmanageable | Default to `page.php`. Canvas only for true landing pages or when explicitly requested |
| 2026-06-04 | Canvas template was doing early exit before wp_head/wp_footer | Rewrote canvas.php to be WP-compliant — all plugins (SEO, analytics, security) now work |

---

## Related

- **Theme source**: `C:\Users\rita\OneDrive\Ambiente de Trabalho\mk-way\talk-to-build-kit\talk-to-build-theme\`
- **Directives**: [figma-decode.md](figma-decode.md), [website-migration.md](website-migration.md)
