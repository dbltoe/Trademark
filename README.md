# Trademark 2.0.1

An encapsulated Zen Cart plugin that shows your trademark — your store name
unless you say otherwise — in the navigation bar of every storefront page,
beside the menu icon on phones and tablets.

**Nothing in your template is changed.** Install it from Plugin Manager and the
mark is there; uninstall it and everything is back the way it was.

**Runs on Zen Cart v1.5.8 through v3.0.0 and PHP 7.4 through 8.5 from a single
codebase**, verified against all six release branches rather than assumed — see
[docs/COMPATIBILITY.md](docs/COMPATIBILITY.md).

---

## The story behind it

A client's lawyers required the company's trademark to be plainly shown on
every page of their Zen Cart site. Most of our stores don't use the logo
wrapper — the logo moves into the home page to give the products more room — so
there wasn't a spot that reliably appeared everywhere. The navigation bar is on
every page on every device, so that's where the mark goes: ahead of *Home* on a
desktop, right beside the hamburger on a phone.

The lawyers also said the ™ or ® symbol isn't needed. The company name alone
does the job, whether or not it's a registered mark. So the default is the bare
name and the symbol is opt-in.

## What it does

```
Desktop, responsive_classic:

  Stoke & Trent Kayaks   Home | Log In                    [ search ]

Phone, responsive_classic:

  [≡] Stoke & Trent Kayaks                    [home] [top] [login]

ZCA Bootstrap, any device:

  Stoke & Trent Kayaks   [≡]  Home  Log In  ...
```

- Works with **responsive_classic** and **ZCA Bootstrap** (3.3.0 through 3.8.0)
  and clones of either, with nothing to configure.
- Works with **any other template** through a custom anchor: name an element by
  id or class (`id=navMain`, `class=navbar`) and the mark goes inside it.
- Text (store name by default), optional ™ / ® / ℠, color, size and weight are
  admin settings. Twelve settings in all; the defaults are right for most
  stores.
- Coexists with the old file-based version of this mod: a page that already
  carries a `.tradeMark` element is left alone, so you can install first and
  remove the old edits later.
- One stylesheet, no JavaScript, no CDN, no tables of its own.

## Requirements

| | |
|---|---|
| Zen Cart | v1.5.8 or later, including v3.0.0-dev |
| PHP | 7.4 – 8.5 |
| Templates | responsive_classic, ZCA Bootstrap 3.3.0–3.8.0, clones of either; anything else via the custom anchor |

## Installation

Copy `zc_plugins/Trademark/` into the `zc_plugins/` directory of your store,
then install from **Admin → Modules → Plugin Manager**. Full instructions are
in [docs/INSTALL.md](docs/INSTALL.md) and in the `readme.html` shipped with the
plugin, which Plugin Manager links from its info panel.

**Already running 1.1.0 or earlier?** Those versions replaced `tpl_header.php`
and, in some packages, added a language file. Install the plugin first, then
follow *Upgrading from the file-based mod* in INSTALL.md: it lists every file
the old versions placed and the order to remove them in. The order matters.
Taking the language file away before the header goes back is the PHP 8 blank
page some 1.1.0 stores have already hit.

## Documentation

- [docs/INSTALL.md](docs/INSTALL.md) — installing, upgrading, uninstalling, troubleshooting
- [docs/CONFIGURATION.md](docs/CONFIGURATION.md) — every setting
- [docs/CUSTOMIZING.md](docs/CUSTOMIZING.md) — styling, the custom anchor, other templates
- [docs/COMPATIBILITY.md](docs/COMPATIBILITY.md) — how one codebase spans v1.5.8 → v3.0.0, and what it relies on
- [CHANGELOG.md](CHANGELOG.md)

## Support

Questions and bug reports go in the plugin's thread on the Zen Cart forum:
[Trademark in the Navigation Bar](https://www.zen-cart.com/threads/205827/).
Plugin Manager links to it from the plugin's info panel.

## Legal stuff

GNU General Public License v2.0, the same license Zen Cart itself is
distributed under. As always, no warranty or guarantee is applied or implied.

**ALWAYS MAKE BACKUPS BEFORE ADDING/EDITING ANY MOD.**
