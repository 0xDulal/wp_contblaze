# ContBlaze

> The AI Engine for Content Growth.

## Overview

ContBlaze is a WordPress plugin that brings AI-powered content creation, optimization, and management directly into the WordPress admin. It helps bloggers, businesses, and SEO agencies plan, produce, refine, publish, and measure content from a single workspace — without leaving WordPress.

## Vision

To become the default AI content engine for WordPress — turning every WordPress site into a growth machine by automating the full content lifecycle: ideation, creation, optimization, repurposing, publishing, and analytics — while keeping the user in full control of brand, quality, and workflow.

## Target Audience

- **Bloggers** — individual creators who want to publish faster and more consistently.
- **Businesses** — marketing teams producing and maintaining site content at scale.
- **SEO agencies** — teams managing content operations and rankings across many client sites.

## Planned Modules

1. **AI Writer** — AI-assisted drafting and long-form article generation inside the editor.
2. **SEO Assistant** — on-page SEO analysis, keyword guidance, and optimization suggestions.
3. **Content Planner** — editorial calendar and content ideation pipeline.
4. **AI Repurposing** — transform one piece of content into social posts, summaries, and other formats.
5. **Auto Publisher** — scheduled, hands-off publishing of generated and approved content.
6. **Bulk Generator** — generate many posts at once from keywords, topics, or data sources.
7. **Content Refresh** — identify and update outdated content with AI assistance.
8. **Brand Voice** — learn and enforce a consistent tone of voice across all generated content.
9. **Content Workflow** — review, approval, and collaboration states for content production.
10. **Content Analytics** — performance tracking and AI-driven insights for published content.

## Technology Requirements

- PHP 8.3+
- WordPress APIs (hooks, options, post meta, REST API, admin UI)
- JavaScript (ES2022+, admin UI)
- HTML5 / CSS3
- Local development: WordPress Studio (Windows, Git Bash, VS Code)

## Development Approach

- Modular architecture: each module is self-contained and can be enabled/disabled independently.
- WordPress coding standards (WPCS) for all PHP.
- Security-first: nonces, capability checks, input sanitization, output escaping on every surface.
- Feature-branch workflow with small, meaningful commits; `develop` is the integration branch.
- Each module is planned, built, tested, and merged individually before the next begins.

## Status

- **Current status:** Environment setup.
- **License:** Proprietary; final license terms to be determined.
