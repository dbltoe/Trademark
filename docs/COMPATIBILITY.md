# Compatibility notes: Zen Cart v1.5.8 → v3.0.0, PHP 7.4 → 8.5

This plugin runs unmodified across six Zen Cart release branches. That comes
from choosing, at each decision point, the mechanism that exists in *every*
supported version rather than the newest one. This document records those
choices so a future maintainer knows which are load-bearing.

## How it's verified

Not by assertion. The maintainer's harness (not shipped) checks, on every PHP
build from 7.4 to 8.5:

| Check | Method |
|---|---|
| **Placement** | The home page of each of the six release branches, fetched from a real install without the plugin in three layouts (desktop, phone, tablet user agents), plus the ZCA Bootstrap 3.8.0 header with its PHP resolved, is run through the insertion function and the output inspected byte for byte. |
| **Zen Cart API** | Every `zen_*` function the plugin calls is looked up in all six installs. Both notifiers it observes are confirmed to fire from `responsive_classic` on all six. The header markup the patterns depend on is confirmed present in all six `tpl_header.php` files. The sanitizer behavior the custom anchor relies on is confirmed in all six `init_sanitize.php` files. |
| **Observer** | Real PHP output buffering, end to end: the normal sequence, a buffer somebody else left open, the footer notifier never firing, and the switched-off state. |
| **Installer** | Against a recording fake database: the SQL emitted, its order, and the columns every row carries. |
| **Security** | The Plugin Library's own scan questions, plus the escaping and validation paths specific to this plugin. |
| **PHP** | Lint plus the whole suite on 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5, with zero diagnostics at `E_ALL`. |

And the plugin was installed through Plugin Manager on all six branches in a
lab and the storefront fetched in all three layouts.

---

## What we rely on

### No header notifier exists — so the page is buffered

There is no `$zco_notifier->notify()` call anywhere inside `tpl_header.php` on
any supported release, and a zc_plugin cannot supply a template override. The
two notifiers that bracket the page body and exist on every release are:

| Notifier | Fires | Present |
|---|---|---|
| `NOTIFY_HTML_HEAD_END` | `html_header.php`, just before `</head>` | v1.5.8 → v3.0.0 |
| `NOTIFY_FOOTER_END` | `tpl_main_page.php`, just before `</body>` | v1.5.8 → v3.0.0 |

The observer writes the stylesheet link at the first and calls
`ob_start('trademark_ob_callback')`; at the second it calls `ob_end_flush()`,
and the callback inserts the mark. Everything from `</head>` to `</body>` passes
through the callback once.

Two things make this safe:

- **The observer closes only its own buffer.** It records `ob_get_level()`
  after opening, and at the footer closes the buffer only if that is still the
  innermost level. If something between the two notifiers opened a buffer and
  left it open, both are left for PHP to close at script end — where the
  callback still runs. (`NOTIFY_PAGE_BODY_BEGIN` would have been a tidier place
  to open, but it only exists from v2.1.)
- **Partial flushes are reassembled.** If anything calls `ob_flush()` between
  the notifiers, PHP hands the callback what it has so far without the FINAL
  flag. The callback holds that and prepends it when the final chunk arrives,
  so the patterns always see the whole page and never a header cut in half.

Zen Cart's own `init_gzip.php` opens `ob_gzhandler` in `application_top`, so
when compression is on the plugin's buffer nests inside it and hands the
compressor the transformed page. Nested buffers are exactly what PHP's output
control is for.

### The header markup is identical on all six branches

The `responsive_classic` `tpl_header.php` on the v1.5.8, 2.0, 2.1, 2.2, 2.3 and
master branches emits, byte for byte:

```
<div class="header Fixed"><a href="#menu" title="Menu"><i class="fa-solid fa-bars"></i></a></div>
<div id="navMain">
  <ul class="back">          (desktop)
  <ul>                       (phone: first item is Home; tablet: first item is class="hide")
```

The 2.0 → 2.3 files are md5-identical; 1.5.8 and 3.0.0 differ from them only in
places the patterns don't touch. The harness re-checks this against the lab's
copies, so a future core change that moves the markup fails the suite rather
than silently producing pages with no mark.

The three layouts are told apart from the markup, not from `$_SESSION`:

| Page has | Layout | Rule |
|---|---|---|
| `<ul class="back">` | desktop | mark the first `<li>` |
| the fixed bar, and the list's first item is `class="hide"` | tablet | mark the bar; mark the first *visible* `<li>` with an extra class hidden in portrait |
| the fixed bar, and the list opens with a visible item | phone | mark the bar only |

The tablet rule mirrors `responsive_tablet.css`, which hides `.header` under
`@media (orientation:landscape)` and lays `#navMain` over the bar as a fixed
strip under `(orientation:portrait)`. The plugin's stylesheet uses the same
orientation query, so the two can't drift apart unless the template changes
both.

One thing the DOM check found: the template's mmenu script rewrites the bar's
class from `header Fixed` to `header mm-slideout` as the page loads. The
server-side pattern sees the original; a CSS selector on `.Fixed` never applies
at run time. The stylesheet therefore has no rule for the bar at all — the
float alone places the mark after the hamburger link.

### ZCA Bootstrap's navbar opening tag hasn't changed

`<nav class="navbar fixed-top mx-3 navbar-expand-lg rounded-bottom" ...>` is
the same in every ZCA Bootstrap release the old instructions covered, 3.3.0
through 3.8.0. Inside a flex container the float is ignored and the mark is
simply the first item, which is where the old hand edit put it.

### `val_function` makes the custom anchor survive the admin sanitizer

An ordinary configuration value is run through `htmlspecialchars()` by
`AdminRequestSanitizer` before it is stored (`STRICT_SANITIZE_VALUES`), which
would turn `<div id="navMain">` into `&lt;div id=&quot;navMain&quot;&gt;` — and
that never matches a page. `admin/includes/init_includes/init_sanitize.php`
applies `NULL_ACTION` to `configuration_value` instead when the row carries a
`val_function`, and `admin/configuration.php` then validates the raw value with
`zen_validate_configuration_entry()`. Both checks are the same in v1.5.8 and
v3.0.0, and the harness confirms them on all six branches.

So the custom anchor row carries this `val_function`:

```json
{"error":"TEXT_TRADEMARK_ANCHOR_INVALID","id":"FILTER_VALIDATE_REGEXP","options":{"options":{"regexp":"/^[\\s\\S]{0,1000}$/"}}}
```

which accepts anything up to 1000 characters, including an empty value. The
value is only ever compared against the page; it is never printed.

**That covers one of two admin paths.** v2.2 added a second: the configuration
page's **Save All** form, which posts every setting in the group at once as
`configuration[cfg_<id>]`. `init_sanitize.php` on v2.2, v2.3 and v3.0 runs the
whole `configuration` array through `PRODUCT_NAME_DEEP_REGEX` *before* any
per-row rule, and that filter strips every `="` that is not `class="` or
`style="`. A raw `<div id="navMain">` typed there is stored as
`<div inavMain">` — confirmed by posting it to the v3.0.0 lab admin — and never
matches a page. The per-row edit form still exists on those releases and still
honors `val_function`, but the group form is the one a store owner sees first.

That is why the recommended anchor syntax is `id=NAME` / `class=NAME`. It has
no quote after its `=`, so the product-name filter leaves it alone, and it
reaches the storefront intact from either form on every release. The plugin
turns it into a tag-matching pattern (`trademark_anchor_pattern()`), which is
also more robust than a literal fragment: attribute order and whitespace in the
template no longer matter. The fragment form is still accepted for anything the
admin lets through.

Every other setting goes through the normal sanitizer, and that's fine: the
trademark text is decoded and then escaped exactly once on output
(`trademark_normalize_text()`), so a store name stored as `Stoke &amp; Trent`
— which is how Zen Cart stores `STORE_NAME` — prints as `Stoke & Trent` rather
than `Stoke &amp; Trent`. The same function makes markup in the text show
literally.

### Observer registration: `auto.*.php` + `zcObserver*`

`includes/init_includes/init_observers.php` scans each installed plugin's
`catalog/includes/classes/observers/` for `auto.*.php`, includes them, and
instantiates `'zcObserver' . base::camelize($name, true)`. Identical in v1.5.8
and v3.0.0-dev. The dispatch order — snake_case method, then `update` +
CamelCased event name, then generic `update()` — is the same too, so
`updateNotifyHtmlHeadEnd()` and `updateNotifyFooterEnd()` are found on every
release.

### Stylesheet linked from inside `zc_plugins/`

Zen Cart's shipped `zc_plugins/.htaccess` denies everything then explicitly
re-allows a list that includes `css`, so the stylesheet can be served from the
plugin's own directory on every release. The template's `css/trademark.css` is
checked first so a store can override it. The alternative,
`linkCatalogStylesheet()` from the `InteractsWithPlugins` trait, is v2.x-only.

### Language file: `lang.*.php` returning an array

v1.5.8 introduced the array-returning language format and every release since
reads it from a plugin's `admin/includes/languages/<lang>/extra_definitions/`.
v3.0.0 drops the legacy `define()` loader, so this is the only format that
spans the range. The plugin has two admin strings and no storefront strings at
all — the mark's text is the owner's setting or the store name.

### Installer: `executeInstallerSql()` only

`addConfigurationKey()`, `getOrCreateConfigGroupId()` and friends were added in
v2.0.1/v2.1.0 and don't exist on v1.5.8. The installer uses raw SQL through
`executeInstallerSql()` throughout, and `executeUpgrade()` takes an optional
argument because v1.5.8 calls it with none.

The INSERT names `val_function` explicitly. The column exists on every
supported release (v1.5.8's install SQL sets it on 73 rows).

### `plugin_control.name` isn't refreshed on half the range

`PluginManager::updatePluginControl()` refreshes `name` and `description` on
every scan on v2.2+ (`upsertMany()`), but on v1.5.8/v2.0/v2.1 it calls
Eloquent's `upsert($values, ['id'], ['infs'])`, which rewrites only `infs`. So
the *Mod Not Turned On* notice in the name would freeze on those releases. The
plugin's `admin/includes/functions/extra_functions/trademark_admin.php`, which
every release loads on every admin page, corrects the stored name on the Plugin
Manager page when it disagrees with `TRADEMARK_STATUS`, and the uninstaller puts
the plain name back. The same reasoning is why nothing state-dependent is in
`pluginDescription`.

### `zen_config()` is not used

It's `@since v3.0.0` and a fatal on everything earlier. `trademark_config()` is
its portable form: `defined($key) ? constant($key) : $default`.

### The PHP floor is 7.4

Zen Cart v1.5.8 runs on PHP 7.4 and 8.0 (not 8.1: it fatals in
`application_top` on 8.1's `IntlDateFormatter` change), so the plugin's source
uses nothing newer than 7.4: no `str_contains()`, no `match`, no nullsafe operator,
no named arguments, no union types. The harness greps for each. It also stays
clean on 8.4/8.5: no implicitly-nullable parameters, and the observer declares
its one property.

---

## What isn't relied on

- `NOTIFY_PAGE_BODY_BEGIN` (v2.1+) — would be a tidier place to open the buffer.
- `NOTIFY_HTML_HEAD_CSS_BEGIN` (v2.1+) — would put the link among the other
  stylesheets rather than after them.
- `InteractsWithPlugins::linkCatalogStylesheet()` (v2.x).
- Root-level `filenames.php` / `database_tables.php` (v2.2+). The plugin needs
  neither.
- Anything in `$_SESSION`, including `layoutType`. The layout is read from the
  markup.
