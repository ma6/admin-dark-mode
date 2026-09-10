# Admin Dark Mode

A WordPress plugin: an accessible dark colour scheme for wp-admin, the login
screen and the block editor.

**Colours only** — every rule touches colour, background, border or focus,
never layout, so nothing moves or reflows. Per-user **Auto / Light / Dark**,
switchable from the toolbar ("Appearance: …") and the profile screen. No
JavaScript and no flash of the light theme on load: the scheme is applied
through a stylesheet `media` attribute, so an OS change is picked up live in
Auto. Every text/background pair meets WCAG 2.2 AA (most are AAA); control and
input borders clear SC 1.4.11.

## Install

No release packages yet — build the ZIP from a checkout:

```bash
git clone https://github.com/ma6/admin-dark-mode.git
zip -r admin-dark-mode.zip admin-dark-mode -x '.git/*'
```

Then **Plugins → Add New → Upload Plugin**, or copy the `admin-dark-mode`
folder into `wp-content/plugins/`. Activate, then pick a scheme from the
toolbar or your profile screen; new users start on Auto. Requires WordPress
6.3+ and PHP 7.4+.

## Scope

Core screens, the login screen, the block editor (chrome and canvas), the
generic `fieldset` / `legend` / plain-table markup most option pages use, the
`@wordpress/components` modal/popover UI, plus named coverage for a handful of
popular plugins. A plugin that ships its own high-specificity `background:#fff`
(or inline styles) can still show a light patch — [`readme.txt`](readme.txt)
has the calculated palette figures and the known limits.

## Documentation

- [`AGENTS.md`](AGENTS.md) — how it is built and the rules that govern changes.
  Canonical; `CLAUDE.md` points here.
- [`readme.txt`](readme.txt) — WordPress-format readme: calculated palette,
  known limits, changelog.
- [`CONTRIBUTING.md`](CONTRIBUTING.md) — a one-person project: no support,
  issues opened by the maintainer only, no pull requests.

## Licence

GPL-2.0-or-later — see [`LICENSE`](LICENSE).
