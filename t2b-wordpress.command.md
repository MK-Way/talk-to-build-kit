---
description: Deploy HTML to WordPress via Canvas template — the Talk-to-Build pipeline
allowed-tools: Bash, Read, Write, Edit, Glob, Grep, TodoWrite, Agent, AskUserQuestion
---

## Talk-to-Build → WordPress

Deploy raw HTML (from Anastasia, Figma Decode, or any vibe coding tool) to a WordPress site on xCloud using the Canvas template. Zero WordPress overhead — pure HTML served directly.

**Use this for:**
- Deploying landing pages, marketing pages, or client sites built with vibe coding
- Updating existing Canvas pages with new HTML
- Setting up a fresh xCloud WordPress site with the Talk-to-Build theme + Canvas template

**NOT for:**
- Traditional WordPress theme development
- Elementor/Gutenberg page building
- Non-WordPress deployments (use Wonderland/Docker pipeline)

## Directive Reference
This command executes: **`directives/t2b-wordpress.md`**

See the directive file for full workflow, image handling, cache flush procedures, and lessons learned.
