---
description: Convert HTML to native Elementor Pro pages — Talk-to-Build Elementor pipeline
---

Load the directive at `~/.claude/directives/t2b-elementor.md` and follow it to convert the provided HTML into a fully native Elementor Pro page.

If no HTML file path is provided, ask for it first. If no site credentials are available, ask for them before proceeding.

**Execution order:**
1. Step 1: SSH → read DB credentials + Elementor version
2. Step 2: Analyse the HTML — list sections, design system, widget plan
3. Step 3: Localize images
4. Step 4: Generate Elementor JSON (output `generate_elementor.py` → `elementor-data.json`)
5. Step 5: Push to WordPress DB
6. Step 6: Flush Elementor CSS + Redis/Nginx caches
7. Step 7: Verify — HTTP 200, Elementor rendering, spot-check in editor
