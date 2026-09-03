# Customizing Trademark

---

## The mark

What the plugin inserts is one element:

```html
<p class="tradeMark">Stoke &amp; Trent Kayaks</p>
```

with `<sup class="tradeMarkSymbol">` inside it when a symbol is on, and the
extra class `tradeMarkTablet` on the second copy a tablet page gets (see
[CONFIGURATION.md](CONFIGURATION.md#how-the-responsive_classic-layouts-are-handled)).

The capital **M** in `tradeMark` is deliberate. It's the class the file-based
version of this mod used, so a store's existing CSS for it keeps working, and it
stays clear of any `trademark` class a template might define for its own
purposes.

## Styling

Color, size and weight come from the admin settings and are written into the
page as one inline rule right after the stylesheet link:

```html
<link rel="stylesheet" href="/zc_plugins/Trademark/v2.0.0/catalog/includes/templates/template_default/css/trademark.css">
<style>.tradeMark{color:#FFFFFF;font-size:1em;font-weight:normal}</style>
```

Everything else is in the stylesheet:

```
zc_plugins/Trademark/v2.0.0/catalog/includes/templates/template_default/css/trademark.css
```

It's short. The mark floats left with no margin or padding (which is what puts
it ahead of *Home* in responsive_classic, whose navigation items are inline, and
is ignored inside ZCA Bootstrap's flex navbar, where the mark is simply the first
item); the symbol is a small superscript with zero line-height so it doesn't
make the bar taller; and the tablet copy is hidden in portrait.

Three ways to change it, in increasing order of commitment:

### 1. Use the settings

Color, size and weight cover most needs.

### 2. Add rules to your template's own CSS

Zen Cart loads every `style*.css` in your template's `css/` folder, but the
plugin's link goes into `<head>` *after* them (it's written at
`NOTIFY_HTML_HEAD_END`), so a plain `.tradeMark { ... }` rule of yours would
lose to the plugin's. Raise the specificity and yours wins:

```css
#navMain .tradeMark { letter-spacing: .04em; }
nav.navbar .tradeMark { margin-right: 2rem; }
```

The three inline settings have the same specificity as the stylesheet, so the
same applies to them.

### 3. Take over the stylesheet

Copy `trademark.css` into `includes/templates/YOUR_TEMPLATE/css/`. The plugin
checks there first and links that copy instead of its own, so the rules are
yours to change. Then, if you'd rather set color and size in CSS too, switch
**Load The Plugin Stylesheet?** off: nothing of the plugin's is linked and your
template's CSS is all there is.

That last option is also the right one for a store coming from the file-based
mod with `.tradeMark` already styled in `stylesheet_zcustom.css` or
`site_specific_styles.php`.

---

## The custom anchor

For a template the plugin doesn't know, or to put the mark somewhere else in one
it does.

1. Open your storefront and view the page source.
2. Find the element you want the mark in and note its `id` or a class it has.
3. Enter it into **Configuration → Trademark → Custom Anchor (Advanced)** as
   `id=NAME` or `class=NAME`.

For example, to put the mark at the start of the main navigation in a template
that kept Zen Cart's ids:

```
id=navMain
```

or, for a Bootstrap-style header:

```
class=navbar
```

`id=` matches the first element on the page with exactly that id. `class=`
matches the first element whose class list contains that word as a whole word,
so `class=navbar` finds `<nav class="navbar fixed-top ...">` but not
`class="navbar-nav"`. The mark goes inside the element as its first item, or
just ahead of the element with **Custom Anchor Position** set to `Before`.

### The fragment form

An exact fragment of the page's HTML also works — `<!--bof-navigation display-->`,
say — and the mark goes immediately after (or before) the first place it occurs.
But on Zen Cart 2.2 and later the settings page saves the whole group through
one form, and the admin's input filter strips `="` from anything typed into it
(it's the same filter that protects product names), so a fragment with a quoted
attribute such as `<div id="navMain">` can't be entered there. The `id=` and
`class=` forms have nothing to strip and work on every release, which is why
they're the ones to use.

Things worth knowing:

- **Only the first match** on a page is used.
- **Pick an element that's on every page.** The home page's banner isn't on the
  product pages. Header and navigation markup usually is.
- **While the anchor is set, the two "Show In ..." settings are ignored.** You've
  said where the mark goes.
- **A page that doesn't contain the anchor gets no mark**, unless **Show At The
  Top Of The Page If Nothing Matches?** is on.
- **It's only ever compared against the page.** It's never printed. Up to 1000
  characters.

You'll probably want to look at styling afterwards: the shipped stylesheet
floats the mark left, which suits a navigation bar and may not suit wherever
you've put it. A rule in your template's CSS such as

```css
#yourHeader .tradeMark { float: none; display: inline-block; margin: 0 1em; }
```

is usually all it takes.

### A clone that reworked the header

The plugin recognizes responsive_classic's header by `<div class="header Fixed">`
followed by the `#menu` link, and `<div id="navMain">` followed by a `<ul>`; and
ZCA Bootstrap's by `<nav class="... navbar ...">`. A clone that kept those works
as-is. One that renamed or restructured them needs the custom anchor.

---

## Other templates

Any template works through the custom anchor. Two that come up:

- **Templates with their own top bar** (many commercial themes): anchor on the
  bar's opening tag and style the mark to match its links.
- **Templates with no navigation bar at all**: anchor on the logo wrapper or the
  header wrapper, and give the mark a block of its own.

If you'd like the plugin to recognize a particular template out of the box, open
an issue on GitHub with a copy of its rendered header markup.
