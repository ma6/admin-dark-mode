# AGENTS.md — Onygo Admin Dark Mode

A standalone WordPress plugin. It currently lives inside the `onygo.26`
monorepo under `plugins/`, but it has **no dependency on the Onygo theme or
on Neon** and is meant to be lifted into its own repository unchanged —
keep it that way.

## What it is

An accessible dark colour scheme for wp-admin, the login screen and the
block editor. Colours only: every CSS declaration touches `color`,
`background`, `border-color`, `box-shadow` or `outline` — never layout.

## Why it does not consume Neon

wp-admin is not a Neon surface. Neon's token layer is not enqueued there and
loading it would fight core's own reset. So this plugin uses raw hex, which
would be a violation anywhere in the theme. Each value is picked against a
**calculated** WCAG 2.2 target — the figures are in the header comment of
`assets/admin-dark.css` and in `readme.txt`. If you change a colour,
recalculate; do not estimate.

## Non-negotiables (same spirit as the theme)

- **WCAG 2.2 AA is the floor**, contrast calculated. Text pairs AA or
  better; control/input borders clear SC 1.4.11 (3:1).
- **Never colour alone.** The toolbar switcher says "Appearance: Dark", not
  a swatch; the active profile radio is `checked`, not just tinted.
- **No JavaScript.** The switch is a nonce-checked link to `admin-post.php`;
  the scheme is applied via a stylesheet `media` attribute. No body class,
  no inline script, no FOUC. Keep it that way.
- **Semantic HTML.** The profile control is a real `fieldset` + `legend` +
  radios.
- **`!important` is allowed here** — it is overriding core's mature
  stylesheet — but only on paint properties, never on layout.
- **Light and dark both work**: "light" means the sheet is not enqueued at
  all, so the user gets stock wp-admin untouched.

## Mechanism

`oad_pref()` reads `oad_scheme` user meta (`auto` | `light` | `dark`,
default `auto`). `oad_media()` maps that to the stylesheet `media`
attribute: `all` forces dark, `(prefers-color-scheme: dark)` defers to the
OS, `''` means do not enqueue. The editor canvas is an iframe that never
sees an enqueued admin sheet, so its CSS goes in through
`block_editor_settings_all` — wrapped in a `prefers-color-scheme` query for
`auto`, injected raw for `dark`.

## Before calling a change done

1. Renders in all three states: Auto on a light OS (stock), Auto on a dark
   OS (dark), forced Dark, forced Light (stock).
2. Contrast of anything new **calculated**.
3. Keyboard only: toolbar switcher and profile radios reachable, focus
   visible, nothing trapped.
4. No console errors; no JavaScript added.
5. Check a third-party option page (WP Super Cache is the reference case)
   and the block editor, not just core screens.
6. Anything decided along the way goes in the repo's `DECISIONS.md` while
   the plugin still lives in the monorepo.
