# Talk-to-Build → WordPress Kit

Deploy raw HTML (from Figma Decode, vibe coding tools, or hand-coded) to a WordPress site on xCloud with **zero WordPress overhead**. No theme header, footer, styles, or scripts. Just your HTML, served as-is via the Canvas template.

This is the core proof-of-concept for the **Talk-to-Build** method: Mike talks, Claude builds, site updates live.

---

## What's in this kit

| File | Purpose |
|---|---|
| `talk-to-build-theme.zip` | The WordPress theme. Contains the Canvas template (`canvas.php`) that strips all WP chrome and serves your HTML raw. Install once per WordPress site. |
| `t2b-wordpress.md` | The **directive** — full deployment workflow, SQL push script, image localization, cache flush procedure, known errors, and lessons learned. This is what Claude Code reads when you invoke `/t2b-wordpress`. |
| `t2b-wordpress.command.md` | The **slash command** — the trigger that tells Claude Code "run the directive." |
| `README.md` | This file. |

---

## One-time setup (your local Claude Code)

Drop the directive and command into your `~/.claude` folder so Claude Code can find them.

**Windows (PowerShell):**
```powershell
# Create directories if they don't exist
New-Item -ItemType Directory -Force -Path "$env:USERPROFILE\.claude\directives"
New-Item -ItemType Directory -Force -Path "$env:USERPROFILE\.claude\commands"

# Copy directive
Copy-Item ".\t2b-wordpress.md" "$env:USERPROFILE\.claude\directives\t2b-wordpress.md"

# Copy slash command
Copy-Item ".\t2b-wordpress.command.md" "$env:USERPROFILE\.claude\commands\t2b-wordpress.md"
```

**Mac/Linux:**
```bash
mkdir -p ~/.claude/directives ~/.claude/commands
cp t2b-wordpress.md ~/.claude/directives/t2b-wordpress.md
cp t2b-wordpress.command.md ~/.claude/commands/t2b-wordpress.md
```

Restart Claude Code (or open a new conversation) and type `/t2b-wordpress` to confirm it loads.

---

## One-time setup (the WordPress site)

Each WordPress site needs the Talk-to-Build theme installed and activated once. The directive (`t2b-wordpress.md`) has the exact SSH commands under **"Theme Installation (first time only)"**. Short version:

1. Upload `talk-to-build-theme.zip` to the server's `/tmp/`
2. Extract into `/var/www/<site>/wp-content/themes/talk-to-build/`
3. `chown` to the site user
4. Activate via MySQL: `UPDATE <prefix>options SET option_value='talk-to-build' WHERE option_name IN ('template','stylesheet');`

---

## How to use

In Claude Code, say:
> `/t2b-wordpress`

Claude will ask for:
1. **HTML file** — local path to the HTML you want to deploy
2. **Target site** — xCloud domain (e.g., `talktobuild2-2o71.wp1.host`)
3. **Page title** — WP display name
4. **Page slug** — URL path (e.g., `landing-page`)
5. **Front page?** — homepage or accessible at `/<slug>/`

Then it does the full pipeline automatically:
- Scans HTML for external images (Imgur, Unsplash, placeholder avatars)
- Downloads them locally
- Uploads to `/wp-content/uploads/<project>/` on the server
- Rewrites HTML to use local paths → `canvas.html`
- Inserts the page into the WP database with the Canvas template
- Flushes Redis + Nginx FastCGI caches
- Verifies HTTP 200 and that Canvas HTML is served (not WP default output)

**Updates** to an existing page (same slug) take ~2 min. The directive uses `UPDATE` instead of `INSERT` automatically.

---

## What you'll need access to

Ask Mike for:
- **SSH key** to xCloud (`xcloud-claude-rsa`) — or generate your own and add it to the server
- **xCloud sudo password** — needed to write into `/var/www/...`
- **Per-site DB credentials** — read from `wp-config.php` on the server; the directive shows the exact `grep` command
- **Redis password** — in `wp-config.php` as `WP_REDIS_PASSWORD` (it's an array: `["username", "password"]`)

The directive's **"Known Sites"** table has the credentials for `talktobuild2-2o71.wp1.host` (Mike's dev site). For other sites, you fetch the creds on first deploy and add a row.

---

## Why this exists (and where it fits)

Vibe coding tools (Gemini, Base44, Lovable, Figma Decode) spit out beautiful HTML but **can't host images**. They reference `imgur.com`, `i.pravatar.cc`, `via.placeholder.com` — all of which break in production.

Talk-to-Build's answer: **WordPress is the image container**. The Canvas template strips WP's overhead but keeps the uploads directory. You get the speed of static HTML with a real CMS-backed asset pipeline behind it.

Related directives in Mike's stack (not included here, but worth knowing):
- **`figma-decode.md`** — Figma → HTML pipeline (feeds into this one)
- **`website-migration.md`** — Full WP migrations (different use case)

---

## How to extend

The directive is **versioned plain markdown**. To add a new pattern, error fix, or learning:

1. Edit `~/.claude/directives/t2b-wordpress.md`
2. Add an entry to the **"Error Handling"** or **"Learnings & Improvements"** table at the bottom — date it
3. Update **"Known Sites"** when you onboard a new client site

The directive grows with every deploy. Treat it as the source of truth.

If you build a new variant (e.g., multi-page sites, staging→prod promotion, automated A/B variants), spin up a sibling directive (`t2b-wordpress-multipage.md`) rather than bloating this one.

---

## Quick reference: common errors

| Error | Fix |
|---|---|
| `NOAUTH Authentication required` (Redis) | Use `redis-cli --user <u> --pass <p> FLUSHALL`. Both come from `WP_REDIS_PASSWORD` array in wp-config.php. |
| Site shows WP default output after deploy | Nginx FastCGI cache. Flush `/var/run/nginx-cache/` + `nginx -s reload`. |
| `Field 'to_ping' doesn't have a default value` | MySQL strict mode. Add `to_ping, pinged, post_content_filtered` to INSERT with empty string defaults. |
| `ModuleNotFoundError: mysql.connector` | Not installed on xCloud. Use `mysql` CLI: `subprocess.run(['mysql', '-u', user, '-p'+pwd, db], stdin=open(sql_file))`. |
| Canvas redirect not firing | WP recovery mode reset `show_on_front` / `page_on_front`. Re-set via MySQL, flush caches. |

Full error table is in the directive.

---

## Questions

Ping Mike in Slack. The directive is the single source of truth — if it's not in there, the answer doesn't exist yet and the answer becomes the next entry in the **Learnings** table.
