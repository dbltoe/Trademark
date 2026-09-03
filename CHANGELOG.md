# Changelog

All notable changes to this project are recorded here. This project follows
[Semantic Versioning](https://semver.org/).

## [2.0.0] — 2026-09-03

**Rewritten as an encapsulated zc_plugin.** Install it from Plugin Manager and
the mark is on every page; nothing in your template is edited, copied or
overridden, and uninstalling puts everything back.

If you're running 1.1.0 or earlier — the file-based version that replaced
`tpl_header.php` — the plugin notices the mark your template already carries
and adds nothing, so you can install it first and remove the old edits when
convenient. See *Upgrading from the file-based mod* in the readme.

### Added

- One codebase for Zen Cart v1.5.8 through v3.0.0 and PHP 7.4 through 8.5,
  verified against all six release branches.
- Works with responsive_classic and ZCA Bootstrap (3.3.0 through 3.8.0), and
  with clones of either, with nothing to configure.
- **Custom Anchor** setting for any other template: name an element by id or
  class (`id=navMain`, `class=navbar`) and the mark goes inside it. An exact
  HTML fragment also works where the admin lets you type one.
- Settings for the text (your store name unless you say otherwise), an optional
  ™ / ® / ℠ symbol, the color, font size and font weight, and which of the two
  responsive_classic locations to use.
- An optional top-of-page fallback for a page where nothing else matches, off
  by default.
- The stylesheet can be overridden from your template (a `trademark.css` in its
  `css/` directory is used instead) or switched off entirely if your template
  already styles `.tradeMark`.
- Self-contained `readme.html` shipped with the plugin and linked, with the
  GitHub project page, as buttons in the Plugin Manager info panel.
- Idempotent installer. Settings you've changed are never reset by an upgrade;
  labels and help text are refreshed to the current version.
- While the plugin is switched off, Plugin Manager lists it as *Trademark - Mod
  Not Turned On*, on every supported release.

### Fixed

- **1.1.0 white-screened the phone and tablet layouts on PHP 8.** Its
  `tpl_header.php` files referred to `HEADER_TITLE_TRADEMARK`, whose language
  file had been removed from the package, and on PHP 8 an undefined constant is
  a fatal error. The plugin has no language constant to lose: the text comes
  from your settings or your store name.
- 1.1.0's responsive_classic files for 1.5.8 and 2.0.0 never showed the mark in
  the desktop navigation, despite the README saying they did. The plugin shows
  it there on every layout.

### Removed

- The per-version `tpl_header.php` replacements and the ZCA Bootstrap edit
  instructions. Neither is needed any longer.

## [1.1.0] — 2026-01-06

File-based mod: replacement `tpl_header.php` for responsive_classic on Zen Cart
1.5.7d, 1.5.8a and 2.0.0, plus instructions for editing ZCA Bootstrap's header
by hand. Superseded by 2.0.0.

[2.0.0]: https://github.com/dbltoe/Trademark/releases/tag/2.0.0
[1.1.0]: https://github.com/dbltoe/Trademark/releases/tag/1.1.0
