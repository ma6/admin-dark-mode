=== Onygo Admin Dark Mode ===
Contributors: martingude
Tested up to: 6.7
Requires at least: 6.3
Requires PHP: 7.4
Stable tag: 0.1.7
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An accessible dark colour scheme for wp-admin. Colours only. No options page.

== Description ==

Most admin dark-mode plugins do one of two things wrong: they bury a colour
switch under fifty other features, or they tint the page and leave input
fields, borders and focus rings invisible. This one does neither.

* **Colours only.** Every rule touches colour, background, border or focus —
  never layout. Nothing moves, nothing reflows, nothing to break.
* **Three states, per user.** Auto (follows your operating system via
  `prefers-color-scheme`), Light, or Dark. Switch from the toolbar
  ("Appearance: …") or from your profile screen.
* **No JavaScript.** The switch is a plain link; the theme is applied through
  a stylesheet `media` attribute, so there is no flash of the light theme on
  load and an OS change is picked up live in Auto.
* **Accessible on purpose.** Every text/background pair is WCAG 2.2 AA or
  better (most are AAA); control and input borders clear SC 1.4.11's 3:1.
  Focus is a 2px ring with a 2px offset that also survives forced-colours
  mode. Never colour alone.
* **Covers third-party settings pages.** Core screens, the block editor
  (chrome and canvas), the login screen, the generic `fieldset` / `legend`
  / plain-table markup most option pages use, the `@wordpress/components`
  modal/popover UI that modern plugins render into, plus named coverage for
  WP Super Cache, W3 Total Cache, Really Simple Security, BackWPup and
  Autoptimize. Tailwind-style admin UIs (`.bg-white` / `.bg-grey-*`) get a
  heuristic pass.

== Palette (calculated, sRGB, WCAG 2.1) ==

* text `#e6e8eb` on canvas `#1a1c1f` — 13.9:1 (AAA)
* text `#e6e8eb` on surface `#242730` — 11.2:1 (AAA)
* muted `#a7adba` on canvas — 7.6:1 (AAA)
* link `#5cb8ff` on canvas — 7.9:1; on a field `#2b2f38` — 6.2:1
* primary-button text `#0a1520` on `#5cb8ff` — 8.6:1
* input border `#727d90` vs field `#2b2f38` — 3.2:1; vs canvas — 4.1:1
* focus ring `#8fd0ff` vs canvas — 10.3:1

== Known limits ==

* A plugin that ships its own high-specificity `background:#fff` in an
  enqueued stylesheet, or inline `style=""`, can still show a light patch.
  The common option-page markup and the popular plugins above are covered;
  another plugin with a fully custom React/Tailwind UI may need ~10 lines
  added to the "named third-party option pages" block in admin-dark.css.
* The Tailwind heuristic recolours anything using `.bg-white` /
  `.bg-grey-*` inside the admin content — intended for plugin dashboards,
  but it would also catch a plugin using `.bg-white` for something that
  must stay white (rare).
* Block-editor chrome uses Gutenberg class names that change between
  releases — that section is best-effort and may need a refresh after a
  major WordPress update.
* Forcing the editor canvas dark means it no longer matches the (light)
  front end while you write. Set your preference to Light, or switch the
  canvas back to chrome-only (see the repo's DECISIONS.md), if that matters
  more than the dark canvas.

== Installation ==

1. Upload the `onygo-admin-dark` folder to `wp-content/plugins/` (or install
   the .zip from Plugins → Add New → Upload Plugin).
2. Activate it.
3. Pick a scheme from the toolbar or your profile. New users start on Auto.

== Changelog ==

= 0.1.7 =
* Block editor: dark block-inserter panel (tabbed sidebar + search field)
  and dark `.components-notice` banners incl. the privacy-policy help notice.

= 0.1.6 =
* BackWPup: the remaining faint bits — `text-primary-*` labels, the
  "Backup Now" button, and pencil/gear icons that hard-code fill="#041515"
  as an SVG attribute.

= 0.1.5 =
* BackWPup: fixed near-black text and fill="#000" monochrome icons across
  its Tailwind-utility pages (dashboard, onboarding, jobs).
* Really Simple Security: dark Material-UI dropdown lists (Autocomplete /
  Select popups render in a body-level portal).
* Dark Privacy Settings + Policy Guide (core).
* Dark "Add Plugins → Upload Plugin" panel (core).

= 0.1.4 =
* Dark "Connectors" screen (WP 7.1 core, options-connectors.php) — a React
  SPA whose bootstrap forces `body.js { background:#fff }`.
* `@wordpress/components` Card / Surface / Item / ItemGroup dark, so any
  plugin built on those goes dark too.
* Media Library grid: dark thumbnail tiles and filename labels.

= 0.1.3 =
* Dark core screens the earlier passes missed: Site Health, Themes, the
  category checklist + publish box (classic editor, Links), and the media
  modal's attachment-details sidebar.
* Named coverage for the Really Simple Security Settings tab (incl. its
  Material-UI inputs and the pro-lock overlay) and WPS Limit Login.
* Generic Material-UI input styling for any MUI-based plugin.

= 0.1.2 =
* Dark "Add Plugins" screen (plugin-install.php) — cards, filter bar and the
  popular-tags cloud.

= 0.1.1 =
* Named dark coverage for Really Simple Security, BackWPup and Autoptimize.
* Dark `@wordpress/components` modals / guides (used by many modern plugins).
* Heuristic pass for Tailwind-style admin UIs (`.bg-white` / `.bg-grey-*`).
* Fixed white patches in the Dashboard Activity / Welcome widgets and
  several block-editor chrome elements (document bar, canvas frame,
  featured-image button, form toggles).

= 0.1.0 =
* Initial release.
