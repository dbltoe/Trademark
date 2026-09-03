# Installing Trademark

Applies to Zen Cart v1.5.8 and later, including v2.x and the v3.0.0 development
branch. PHP 7.4 through 8.5.

---

## 1. Copy the files

This repository contains one directory that belongs in your store:

```
zc_plugins/Trademark/
```

Upload it so it lands at:

```
<your store root>/zc_plugins/Trademark/v2.0.0/
```

That directory should contain `manifest.php`, `readme.html`, `changelog.txt`,
and the `Installer/`, `admin/` and `catalog/` sub-directories.

Nothing goes anywhere else. The plugin doesn't drop files into your `admin/`,
`includes/` or template directories, which is what makes it safe to remove
later.

> **If your store is itself a git checkout of Zen Cart:** `zc_plugins/.gitignore`
> uses a deny-all rule with an explicit allowlist, so add `!Trademark/` and
> `!Trademark/**` to it or git won't see the plugin. This has no effect on the
> plugin working.

## 2. Install it

1. Log in to your Zen Cart admin.
2. Go to **Modules → Plugin Manager**.
3. Find **Trademark** and click **Install**.

> Selecting the plugin shows an info panel on the right. Alongside the
> description and the **Install / Uninstall / Disable** buttons you'll find
> **Read Me** and **GitHub** buttons. Read Me opens the full documentation, and
> both work whether or not the plugin is installed.

The installer creates a **Trademark** configuration group and adds it to the
Configuration menu. If the menu entry doesn't appear straight away, log out of
the admin and back in — Zen Cart caches the menu structure per session.

## 3. Look at your storefront

That's it. The mark is on every page, showing your store name in white at the
same size as the navigation links. The master switch is **on** from the start,
because with every other setting at its default the plugin does exactly what
it's for.

If nothing appears, your store name is probably empty: check **Configuration →
My Store → Store Name**, or enter the text you want under **Configuration →
Trademark → Trademark Text**.

Every setting is documented in [CONFIGURATION.md](CONFIGURATION.md).

---

## Upgrading from the file-based mod (1.1.0 and earlier)

Versions 1.0.x and 1.1.0 weren't a plugin. They replaced a file in your
template and, depending on the version, added a language file and a CSS rule.
The plugin needs none of that, and the old header edit actually stops the
plugin working: it sees the mark your template already carries and adds
nothing. So the old pieces have to come out. This is the complete list.

### What the old versions put on your store

| File | Which versions | What to do |
|---|---|---|
| `includes/templates/YOUR_TEMPLATE/common/tpl_header.php` | All. responsive_classic: the whole file was replaced. ZCA Bootstrap: one line was added by hand after the `<nav>` tag. | Restore or edit (step 2) |
| `includes/languages/english/extra_definitions/lang.trademark.php` | 1.0.x on Zen Cart 1.5.8 and 2.0, both templates. 1.1.0 stopped shipping it but never said to delete it. | Delete (step 3, **after** step 2) |
| `includes/languages/english/extra_definitions/trademark_definitions.php` | 1.0.x on Zen Cart 1.5.7, both templates. | Delete (step 3, **after** step 2) |
| `includes/templates/YOUR_TEMPLATE/common/tpl_header.php.bak` | One 1.0.x package for 1.5.8 carried this by mistake. | Delete if present |
| A `.tradeMark { ... }` rule in `css/stylesheet_zcustom.css` (responsive_classic) or `css/site_specific_styles.php` (ZCA Bootstrap) | All, added by hand from the readme. | Your choice (step 4) |

The old mod made no database changes, added nothing to the admin, and changed
no permissions, so there's nothing to reverse there. If you run a language
other than English, check that language's `extra_definitions` folder for the
same two file names.

### The steps, in this order

1. **Install the plugin first**, from Plugin Manager. Your storefront doesn't
   change yet, because the plugin steps aside while the old mark is in the
   page. There's never a moment with no mark showing.
2. **Put the header back.**
   - *responsive_classic, shipped file used unchanged:* replace
     `includes/templates/YOUR_TEMPLATE/common/tpl_header.php` with the stock
     copy for your Zen Cart version
     (`includes/templates/responsive_classic/common/tpl_header.php` from the
     Zen Cart download). If your clone only ever changed this file for the
     trademark, you can delete the clone's copy instead; Zen Cart then uses
     the stock one.
   - *responsive_classic, changes merged into a customized file:* three edits
     to undo. In the two `echo '<div class="header Fixed">...` lines, remove
     `<p class="tradeMark">' . STORE_NAME . '</p>`. In the navigation list,
     change

     ```php
     <li><?php echo '<p class="tradeMark">' . HEADER_TITLE_TRADEMARK . '</p><a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?>
     ```

     back to

     ```php
     <li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?>
     ```

     wherever it occurs (once or twice depending on the version).
   - *ZCA Bootstrap:* in your cloned template's `tpl_header.php`, delete the
     one line `<p class="tradeMark"><?php echo STORE_NAME; ?></p>` that follows
     the `<nav class="navbar ...>` tag.

   Reload the storefront. The plugin's mark is now the one you see, in the
   same spot.
3. **Now delete the language file**, if you have one: `lang.trademark.php` or
   `trademark_definitions.php` under
   `includes/languages/english/extra_definitions/`. Only after step 2. That
   file defines `HEADER_TITLE_TRADEMARK`, and while the old header is still in
   place, removing the definition is what turns the phone and tablet layouts
   into a blank page on PHP 8.
4. **Decide about the CSS.** Either delete the old `.tradeMark` rule and use
   the plugin's **Text Color**, **Font Size** and **Font Weight** settings, or
   keep it and set **Load The Plugin Stylesheet?** to `false` so the plugin
   doesn't override it. Don't keep both: the plugin's inline rule is later in
   `<head>` and would win.
5. Delete `tpl_header.php.bak` if it's there.

> **If your phone or tablet layout is already a blank page**, you're on 1.1.0
> (or deleted the language file early). That's the `HEADER_TITLE_TRADEMARK`
> fatal. Do step 2 now and it's fixed; the plugin can be installed before or
> after.

---

## Upgrading the plugin

1. Upload the new version directory alongside the old one, so that
   `zc_plugins/Trademark/` holds both. **Don't delete the old one yet** —
   Plugin Manager needs both to offer the upgrade.
2. **Modules → Plugin Manager → Upgrade.**
3. Once it reports success, the old version directory can be deleted.

**Your settings survive.** Configuration records are written with
`INSERT IGNORE` followed by an update of the metadata only — labels, help text,
ordering and input type are refreshed to the current version, but
`configuration_value` is never touched.

Uninstall + Install produces the same result if you prefer it.

---

## Uninstalling

**Modules → Plugin Manager → Trademark → Uninstall.** This removes the
configuration group, all of its settings, and the admin menu entry. No template
file was ever changed, so there's nothing else to undo.

To remove the code as well, delete the `zc_plugins/Trademark/` directory
afterwards.

To hide the mark without uninstalling, set **Enable Trademark?** to `false`.
While it's off, Plugin Manager lists the plugin as *Trademark - Mod Not Turned
On* so it can't be forgotten.

---

## Troubleshooting

### The mark doesn't appear

- Is **Enable Trademark?** set to `true`?
- Is your store name set, or **Trademark Text** filled in? An empty name shows
  nothing.
- View the page source and search for `class="tradeMark"`. If it's there but
  invisible, it's a styling matter — most often white text on a white bar. Set
  **Text Color**.
- If it isn't in the source, the plugin didn't recognize your header. On a
  template that isn't responsive_classic or ZCA Bootstrap, or a clone that
  reworked the header, set a custom anchor — see
  [CUSTOMIZING.md](CUSTOMIZING.md).

### It appears twice

Your template still carries the file-based mod's mark somewhere the plugin
doesn't check for it, or something else on the page has the class. Search your
template's files for `tradeMark`. The plugin itself never adds a mark to a page
that already has one.

### It appears on a phone but not on a tablet in landscape (or the other way round)

On a tablet, responsive_classic shows the fixed bar in portrait and an ordinary
navigation row in landscape, and the plugin puts a copy in each. If one is
missing, check that both **Show In The Main Navigation Bar?** and **Show In The
Mobile Header Bar?** are `true`.

### The stylesheet 404s

View source and check this URL loads:

```
zc_plugins/Trademark/v2.0.0/catalog/includes/templates/template_default/css/trademark.css
```

Zen Cart's shipped `zc_plugins/.htaccess` denies everything then explicitly
re-allows `.css`, `.js` and `.html`, so on a normal Apache host this works. If
your server ignores `.htaccess` — nginx, LiteSpeed without compatibility mode,
or Apache with `AllowOverride None` plus its own deny rule — you may need a
rule permitting those types under `zc_plugins/`. The same applies to the Read
Me link in Plugin Manager.

### After upgrading, the old styling is still showing

Browsers cache the stylesheet for up to an hour. A hard reload (Ctrl+F5) clears
it. Each plugin version lives in its own directory, so a new version is always a
new URL.

### The admin menu entry is missing after installing

Log out and back in. If it's still missing, uninstall and re-install — that
rewrites the `admin_pages` record.
