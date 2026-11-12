# Trademark

Version 2.0.2 · Zen Cart 1.5.8 through 3.0.0 · PHP 7.4 through 8.5 · GPL v3 · by My Zen Cart Host (dbltoe)

Shows your trademark — your store name unless you say otherwise — in the navigation bar of every storefront page. No template file is changed.

## What it does

It puts one short line of text at the top of every storefront page, in the navigation bar, where a visitor can't miss it:

```
  Desktop, responsive_classic:

  Stoke & Trent Kayaks   Home | Log In                    [ search ]

  Phone, responsive_classic:

  [☰] Stoke & Trent Kayaks                    [home] [top] [login]

  ZCA Bootstrap, any device:

  Stoke & Trent Kayaks   [☰]  Home  Log In  ...
```

The text is your store name, taken from *Configuration → My Store → Store Name*. If the mark you have to show is something other than your store name, there's a setting for that. There's also an optional ™, ® or ℠ after it, and you can pick the color, size and weight.

Install it and it's there. Nothing to configure unless you want to.

## Why it exists

A client's lawyers required the company's trademark to be plainly shown on every page of the site. Many of our stores don't use the logo wrapper — the logo is moved into the home page to make room for the products — so there wasn't a reliable spot that appeared everywhere. The navigation bar is on every page, on every device, so that's where the mark goes: ahead of *Home* on a desktop, right beside the menu icon on a phone.

The lawyers also said the ™ or ® symbol isn't needed. The company name alone does the job, whether or not it's a registered mark. So the default is the bare name, and the symbol is opt-in.

Earlier versions of this mod did the job by replacing `tpl_header.php` in your template, one copy per Zen Cart version, plus hand edits for ZCA Bootstrap. This version is a proper zc_plugin: it installs from Plugin Manager, touches no template file, and runs unchanged on Zen Cart v1.5.8 through v3.0.0.

## Installing

1. Upload the `zc_plugins/Trademark/` directory from this package so it lands at `<your store>/zc_plugins/Trademark/v2.0.2/`. Nothing goes anywhere else.
2. In your admin, go to **Modules → Plugin Manager**, select **Trademark**, and click **Install**.
3. Reload your storefront.

That's it. The mark is on every page, showing your store name, in white, at the same size as the navigation links.

Once it's installed, a superuser can open this guide from the **ReadMe** button in Plugin Manager.

> Selecting the plugin in Plugin Manager shows an info panel on the right. Next to **Install / Uninstall / Disable** you'll find **Forum Thread** and **GitHub** buttons, which work before installing, plus **Settings** once the plugin is installed and, for superusers, **ReadMe**. Settings opens the plugin's Configuration page; ReadMe opens this guide inside the admin, in its own window.

If your store's name is empty in *Configuration → My Store*, nothing appears until you either set it or enter something in **Trademark Text**. A store with no name has nothing to show.

## Settings

All under **Configuration → Trademark**. Twelve settings, and the defaults are right for most stores.

### What it says

| Setting | Default | What it does |
| --- | --- | --- |
| Enable Trademark? | `true` | Master switch. `false` hides the mark on every page without uninstalling. While it's off, Plugin Manager lists the plugin as *Trademark - Mod Not Turned On* so a switched-off mark can't be forgotten. |
| Trademark Text: | *empty* | Empty means your store name. Enter something here only if the mark you must display is different from the store name. |
| Append A Symbol? | `None` | `TM`, `R` or `SM` adds ™, ® or ℠ after the text as a small superscript. Use `R` only for a mark that's actually registered. |

### Where it goes

| Setting | Default | What it does |
| --- | --- | --- |
| Show In The Main Navigation Bar? | `true` | responsive_classic: ahead of *Home* in the top navigation, on desktops and on tablets held in landscape. ZCA Bootstrap: first item in the navbar, on every device. |
| Show In The Mobile Header Bar? | `true` | responsive_classic only: beside the menu icon in the fixed bar that phones, and tablets in portrait, get across the top. Set to `false` and phones show no mark at all — there's nowhere else on a phone to put it. |
| Custom Anchor (Advanced): | *empty* | For any other template, or to choose a different spot: `id=navMain` or `class=navbar`. See Any other template: the custom anchor. When set, the two settings above are ignored. |
| Custom Anchor Position: | `After` | `After` puts the mark inside the anchored element as its first item; `Before` puts it just ahead of the element. |
| Show At The Top Of The Page If Nothing Matches? | `false` | If a page has none of the places above, `true` puts the mark at the very top of the page body so it's still shown. Off by default because an unplanned spot usually looks wrong. |

### How it looks

| Setting | Default | What it does |
| --- | --- | --- |
| Load The Plugin Stylesheet? | `true` | Links the plugin's small stylesheet and applies the three settings below. Set to `false` if your template already styles `.tradeMark` in its own CSS — for example from the file-based version of this mod. |
| Text Color: | `#FFFFFF` | Any CSS color. White suits the dark bars in responsive_classic and ZCA Bootstrap. Something that doesn't look like a color is ignored and white is used. |
| Font Size: | `1em` | Any CSS size. `1em` matches the links around it. |
| Font Weight: | `normal` | Or `bold`. |

> **Check the contrast.** White on responsive_classic's dark gray bar is fine. If your template's bar is a lighter color, pick a text color that meets accessibility contrast standards (https://docs.zen-cart.com/user/accessibility/accessibility/#what-is-accessibility); a trademark nobody can read isn't plainly shown.

## Where the mark goes

The plugin looks at each page as it goes out and inserts the mark next to markup it recognizes. Out of the box it recognizes two templates, and clones of either.

### responsive_classic

Zen Cart's own template lays its header out three ways, and the plugin follows each:

| Device | Where |
| --- | --- |
| Desktop | Inside the first entry of the top navigation, ahead of the *Home* link. It floats left, so it reads *Stoke & Trent Kayaks  Home \| Log In*. |
| Phone | In the fixed bar at the top, right after the menu icon. The icon strip that the template lays over that bar is left alone, because a mark in it would sit on top of the one beside the icon. |
| Tablet | Both. In portrait the template shows the fixed bar and the mark beside the icon is the one you see. In landscape the template hides that bar and turns the strip into an ordinary navigation row, and the mark ahead of *Home* is the one you see. The plugin adds the second copy with an extra class that its stylesheet hides in portrait, using the same orientation query the template itself uses. |

This is the same markup in the v1.5.8, v2.0, v2.1, v2.2, v2.3 and v3.0 templates, checked against each rather than assumed.

### ZCA Bootstrap

The mark becomes the first item inside the header `<nav class="navbar ...">`, ahead of the menu toggler and the menu itself. On a desktop that reads *Stoke & Trent Kayaks  Home  Log In ...*; on a phone it's the name at the left and the toggler at the right. That's the same spot the old hand-edit instructions put it. Bootstrap versions 3.3.0 through 3.8.0 all open the navbar the same way.

### A clone of either

Works the same, as long as the clone kept the header markup. If you've reworked the header, use the custom anchor, described next.

## Any other template: the custom anchor

For a template the plugin doesn't know, or to put the mark somewhere else in one it does, name the element you want it in. Open your storefront, view the page source, find the element, and put its id or class into **Custom Anchor (Advanced)**:

```
id=navMain
class=navbar
```

`id=` matches the first element on the page with exactly that id. `class=` matches the first element whose class list contains that word (so `class=navbar` finds `<nav class="navbar fixed-top ...">`). The mark goes inside that element as its first item, or just ahead of the element if you set **Custom Anchor Position** to `Before`.

An exact fragment of the page's HTML, such as `<!--bof-navigation display-->`, also works: the mark goes immediately after (or before) the first place it occurs. But on Zen Cart 2.2 and later the settings page saves the whole group through one form, and Zen Cart's input filter strips `="` from whatever you type in it, so a fragment with a quoted attribute can't be entered there. The `id=` and `class=` forms have no quote to strip and work on every release, which is why they're the ones to use.

A few things worth knowing:

- Only the first match on a page is used.
- Choose an element that's on every page. The home page's banner isn't on the product pages; the header and navigation usually are.
- While the anchor is set, the two "Show In ..." settings are ignored: you've said where the mark goes.
- If a page doesn't contain the anchor, that page gets no mark — unless **Show At The Top Of The Page If Nothing Matches?** is on.
- The anchor is only ever compared against the page. It's never printed.

You'll probably want to look at Styling afterwards: the shipped stylesheet floats the mark left, which suits a navigation bar and may not suit wherever you've put it.

## Styling

The mark is:

```
<p class="tradeMark">Stoke &amp; Trent Kayaks</p>
```

with `<sup class="tradeMarkSymbol">` inside it when a symbol is on. The capital **M** in `tradeMark` is deliberate: it's the class the file-based version of this mod used, so a store's existing CSS for it keeps working, and it stays clear of any `trademark` class a template might define for its own purposes.

Color, size and weight come from the admin settings, written into the page as one inline rule. Everything else — the float, zero margin and padding, the tablet orientation rule — is in the plugin's stylesheet:

```
zc_plugins/Trademark/v2.0.2/catalog/includes/templates/template_default/css/trademark.css
```

Three ways to change it, in increasing order of commitment:

1. **Use the settings.** Color, size and weight cover most needs.
2. **Add rules to your template's own CSS.** Zen Cart loads every `style*.css` in your template's `css/` folder, but the plugin's link goes into `<head>` after them, so a plain rule of yours would lose. Either raise the specificity (`#navMain .tradeMark { ... }`) or use the next option.
3. **Take over the stylesheet.** Copy `trademark.css` into `includes/templates/YOUR_TEMPLATE/css/`. The plugin links that copy instead of its own. Then, if you'd rather set color and size in CSS too, switch **Load The Plugin Stylesheet?** off; nothing of the plugin's is linked and your template's CSS is all there is.

## How it works

Zen Cart doesn't fire a notifier inside `tpl_header.php` on any supported release, and a zc_plugin can't supply a template override. What every release does have is a notifier at the end of `<head>` and another at the end of `<body>`. The plugin opens an output buffer at the first and closes it at the second, and on the way out it looks for the header markup it recognizes and inserts the mark beside it. The page is otherwise byte for byte what your template produced.

That's why nothing in your template is edited, copied or overridden, why it works with a clone of either template as long as the header markup survived, and why uninstalling leaves nothing behind.

Two safeguards are worth knowing about. If a page already contains a `class="tradeMark"` element — a template still carrying the file-based mod's edits — the plugin adds nothing, so the two can never double up. And the buffer is closed only if it's the plugin's own; if something else opened one and left it open, the plugin leaves both for PHP to close at the end of the request, and the mark is still inserted then.

## Upgrading from the file-based mod (1.1.0 and earlier)

Versions 1.0.x and 1.1.0 of this mod weren't a plugin. They replaced a file in your template and, depending on the version, added a language file and a CSS rule. The plugin needs none of that, and the old header edit actually stops the plugin working: it sees the mark your template already carries and adds nothing. So the old pieces have to come out. This section is the complete list.

### What the old versions put on your store

| File | Which versions | What to do |
| --- | --- | --- |
| `includes/templates/YOUR_TEMPLATE/common/tpl_header.php` | All. responsive_classic: the whole file was replaced. ZCA Bootstrap: one line was added by hand after the `<nav>` tag. | Restore or edit — step 2 below. |
| `includes/languages/english/extra_definitions/lang.trademark.php` | 1.0.x on Zen Cart 1.5.8 and 2.0, both templates. (1.1.0 stopped shipping it but never said to delete it.) | Delete — step 3 below, **after** step 2. |
| `includes/languages/english/extra_definitions/trademark_definitions.php` | 1.0.x on Zen Cart 1.5.7, both templates. | Delete — step 3 below, **after** step 2. |
| `includes/templates/YOUR_TEMPLATE/common/tpl_header.php.bak` | One 1.0.x package for 1.5.8 carried this by mistake. | Delete if present. |
| A `.tradeMark { ... }` rule in `includes/templates/YOUR_TEMPLATE/css/stylesheet_zcustom.css` (responsive_classic) or `css/site_specific_styles.php` (ZCA Bootstrap) | All, added by hand from the old instructions. | Your choice — step 4 below. |

The old mod made no database changes, added nothing to the admin, and changed no permissions, so there's nothing to reverse there. If you have a language other than English, look for the same two language file names under that language's `extra_definitions` folder as well.

### The steps, in this order

1. **Install the plugin first**, from Plugin Manager. Your storefront doesn't change yet, because the plugin steps aside while the old mark is in the page. This means there's never a moment with no mark showing.
2. **Put the header back.** How depends on your template and on how the old mod went in; the three cases are under Putting the header back, just below this list. Then reload the storefront. The plugin's mark is now the one you see, in the same spot.
3. **Now delete the language file**, if you have one: `lang.trademark.php` or `trademark_definitions.php` under `includes/languages/english/extra_definitions/`. Do this only after step 2. That file defines `HEADER_TITLE_TRADEMARK`, and while the old header is still in place, removing the definition is what turns the phone and tablet layouts into a blank page on PHP 8.
4. **Decide about the CSS.** The old instructions had you add a `.tradeMark` rule to `stylesheet_zcustom.css` or `site_specific_styles.php`. Either delete it and use the plugin's **Text Color**, **Font Size** and **Font Weight** settings, or keep it and set **Load The Plugin Stylesheet?** to `false` so the plugin doesn't override it. Don't keep both: the plugin's inline rule is later in `<head>` and would win.
5. Delete `tpl_header.php.bak` if it's there. Zen Cart ignores it, but it's clutter.

> **If your phone or tablet layout is already a blank page**, you're on 1.1.0 (or deleted the language file early). That's the `HEADER_TITLE_TRADEMARK` fatal. Do step 2 now and it's fixed; the plugin can be installed before or after.

### Putting the header back

This is step 2. Use whichever of the three cases is yours.

**responsive_classic, if you used the shipped file unchanged.** Replace `includes/templates/YOUR_TEMPLATE/common/tpl_header.php` with the stock copy for your Zen Cart version, which is `includes/templates/responsive_classic/common/tpl_header.php` from the Zen Cart download. If your template is a clone that only ever changed this file for the trademark, you can delete the clone's copy instead; Zen Cart then uses the stock one.

**responsive_classic, if you merged the changes into a customized file.** There are three edits to undo. In the two `echo '<div class="header Fixed">...` lines, remove `<p class="tradeMark">' . STORE_NAME . '</p>`. In the navigation list, change

```
<li><?php echo '<p class="tradeMark">' . HEADER_TITLE_TRADEMARK . '</p><a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?>
```

back to

```
<li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?>
```

wherever it occurs (once or twice depending on the version).

**ZCA Bootstrap.** Open `tpl_header.php` in your cloned template and delete the one line `<p class="tradeMark"><?php echo STORE_NAME; ?></p>` that follows the `<nav class="navbar ...>` tag.

Removing the plugin itself later is a separate, simpler matter: see Uninstalling.

## Upgrading the plugin

1. Upload the new version directory alongside the old one, so that `zc_plugins/Trademark/` holds both. Don't delete the old one yet; Plugin Manager needs both to offer the upgrade.
2. **Modules → Plugin Manager → Upgrade.**
3. Once it reports success, delete the old version directory.

Your settings survive. The installer refreshes labels and help text to the current version but never touches a value you've set.

**Version 2.0.2** moves the plugin to the GNU General Public License, version 3, and replaces readme.html with this guide: the ReadMe button in Plugin Manager now opens it inside the admin, for superusers, in its own window. Nothing changes on the storefront or in the settings.

**Version 2.0.1** adds a Settings button to the plugin's Plugin Manager panel, shown once the plugin is installed, and puts the panel's buttons on one row: ReadMe, Settings, Forum Thread, GitHub. Nothing changes on the storefront or in the settings.

## Uninstalling

**Modules → Plugin Manager → Trademark → Uninstall.** This removes the configuration group and its settings and the admin menu entry. No template file was ever changed, so there's nothing else to undo. Delete `zc_plugins/Trademark/` afterwards if you want the code gone too.

To hide the mark without uninstalling, set **Enable Trademark?** to `false`.

## Troubleshooting

### The mark doesn't appear

- Is **Enable Trademark?** `true`?
- Is your store name set (*Configuration → My Store → Store Name*), or **Trademark Text** filled in? An empty name shows nothing.
- View the page source and look for `class="tradeMark"`. If it's there but invisible, it's a styling matter — most often white text on a white bar. Set **Text Color**.
- If it isn't in the source, the plugin didn't recognize your header. On a template that isn't responsive_classic or ZCA Bootstrap, or a clone that reworked the header, set a custom anchor.
- Is the page one your template renders without `html_header.php` and `tpl_main_page.php`? The plugin needs both notifiers. Every stock page has them.

### It appears twice

Your template still carries the file-based mod's mark somewhere the plugin doesn't check for it, or you've styled a second element with the class. Search your template's files for `tradeMark`. The plugin itself never adds a mark to a page that already has one.

### It appears on a phone but not on a tablet in landscape (or the other way round)

On a tablet, responsive_classic shows the fixed bar in portrait and an ordinary navigation row in landscape, and the plugin puts a copy in each. If one is missing, check that both **Show In The Main Navigation Bar?** and **Show In The Mobile Header Bar?** are `true`.

### The stylesheet 404s

Zen Cart's shipped `zc_plugins/.htaccess` denies everything then explicitly re-allows web assets such as `.css` and `.js`, so on a normal Apache host the stylesheet loads. If your server ignores `.htaccess` — nginx, or Apache with `AllowOverride None` plus its own deny rule — you may need a rule permitting those types under `zc_plugins/`.

### After upgrading, the old styling is still showing

Browsers cache the stylesheet for up to an hour. A hard reload (Ctrl+F5) clears it. Each plugin version lives in its own directory, so a new version is always a new URL.

## Compatibility

| Item | Details |
| --- | --- |
| Zen Cart | v1.5.8 through v3.0.0-dev, one codebase, verified against all six release branches |
| PHP | 7.4 through 8.5 |
| Templates | responsive_classic and ZCA Bootstrap 3.3.0–3.8.0 out of the box, and clones of either; anything else through the custom anchor |
| Database | Twelve rows in `configuration` and one group. No tables of its own. |
| Assets | One stylesheet, served from the plugin directory. No JavaScript, no CDN, no external services. |

How one codebase spans those releases, and what it relies on, is in `docs/COMPATIBILITY.md` in the download. The `docs/` folder also has `INSTALL.md`, `CONFIGURATION.md` and `CUSTOMIZING.md`.

## Support

Source, issues and releases are on GitHub (https://github.com/dbltoe/Trademark). Questions and problem reports go to the plugin's thread on the Zen Cart forum, Trademark in the Navigation Bar (https://www.zen-cart.com/threads/205827/), which is also linked from the plugin's Plugin Manager panel.

As always, no warranty or guarantee is applied or implied. Always make backups before adding or editing any mod.

## Changelog

See `CHANGELOG.md`. Version 2.0.2 moves the plugin to the GNU General Public License, version 3, and replaces readme.html with this guide, opened from the ReadMe button in Plugin Manager.

## License

Trademark is free software under the GNU General Public License, version 3 (https://www.gnu.org/licenses/gpl-3.0.html); the full text is in `LICENSE`. Copyright © 2026 My Zen Cart Host (dbltoe).
