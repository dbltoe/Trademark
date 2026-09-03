# Configuration reference

Everything lives under **Admin → Configuration → Trademark**. There are twelve
settings, and the defaults are right for most stores.

Setting titles are Title Case and end in a colon, or a question mark where the
setting is a yes/no question.

---

## What it says

| Setting | Default | What it does |
|---|---|---|
| **Enable Trademark?** | `true` | Master switch. `false` hides the mark on every page without uninstalling. While it's off, Plugin Manager lists the plugin as *Trademark - Mod Not Turned On*. **On from the start**, deliberately: with every other setting at its default the plugin shows your store name, which is exactly what it's for. |
| **Trademark Text:** | *(empty)* | Empty means your store name, from **Configuration → My Store → Store Name**. Enter something only if the mark you must display differs from the store name. Plain text; any markup is shown literally. |
| **Append A Symbol?** | `None` | `TM`, `R` or `SM` adds ™, ® or ℠ after the text as a small superscript. `None` is usually right — the lawyers say the company name itself is enough. Use `R` only for a mark that's actually registered. |

## Where it goes

| Setting | Default | What it does |
|---|---|---|
| **Show In The Main Navigation Bar?** | `true` | responsive_classic: ahead of *Home* in the top navigation, on desktops and on tablets held in landscape. ZCA Bootstrap: first item in the navbar, on every device. |
| **Show In The Mobile Header Bar?** | `true` | responsive_classic only: beside the menu icon in the fixed bar that phones, and tablets in portrait, get across the top. Set to `false` and phones show no mark at all — there's nowhere else on a phone to put it. Does nothing for ZCA Bootstrap, which has no separate bar. |
| **Custom Anchor (Advanced):** | *(empty)* | Name the element the mark should go in: `id=navMain` or `class=navbar`. An exact HTML fragment also works where the admin lets you type it (see below). **While set, the two settings above are ignored.** See [CUSTOMIZING.md](CUSTOMIZING.md#the-custom-anchor). |
| **Custom Anchor Position:** | `After` | With an id or class anchor, `After` puts the mark inside the element as its first item and `Before` puts it just ahead of the element. With a fragment, immediately after or before the fragment. |
| **Show At The Top Of The Page If Nothing Matches?** | `false` | If a page has none of the places above, `true` puts the mark at the very top of the page body so it's still shown. Off by default because a mark in an unplanned spot usually looks wrong — but the choice is yours if the mark must appear no matter what. |

### How the responsive_classic layouts are handled

| Layout | What happens |
|---|---|
| Desktop | The mark goes inside the first entry of the top navigation, ahead of *Home*. |
| Phone | The mark goes in the fixed bar after the menu icon. The icon strip the template lays over that bar is left alone — a mark in it would sit on top of the other one. |
| Tablet | Both. In portrait the fixed bar shows and its mark is the one you see; in landscape the template hides the bar and the mark ahead of *Home* is the one you see. The second copy carries an extra class, `tradeMarkTablet`, that the plugin's stylesheet hides in portrait using the same orientation query the template uses. |

The plugin tells the layouts apart from the markup itself, so this needs no
setting.

## How it looks

| Setting | Default | What it does |
|---|---|---|
| **Load The Plugin Stylesheet?** | `true` | Links the plugin's stylesheet and applies the three settings below. Set to `false` if your template already styles `.tradeMark` in its own CSS — for example from the file-based version of this mod — so the plugin doesn't override it. When `false`, nothing of the plugin's reaches `<head>`. |
| **Text Color:** | `#FFFFFF` | Any CSS color: `#FFFFFF`, `gold`, `rgb(255,255,255)`, `var(--nav-fg)`. White suits the dark bars in responsive_classic and ZCA Bootstrap. A value that doesn't look like a color is ignored and white is used — this is the one setting that reaches the page as code rather than text, so it's checked. |
| **Font Size:** | `1em` | Any CSS size: `1em`, `1.2rem`, `16px`, `120%`. `1em` matches the navigation links around it. Anything else falls back to `1em`. |
| **Font Weight:** | `normal` | Or `bold`. |

### Contrast

White on responsive_classic's dark gray bar passes comfortably. If your
template's bar is a lighter color, pick a text color that meets
[accessibility contrast standards](https://docs.zen-cart.com/user/accessibility/accessibility/#what-is-accessibility).
A trademark nobody can read isn't plainly shown.

---

## Where the values live

All twelve are ordinary rows in Zen Cart's `configuration` table, in a group
titled **Trademark**, and become constants named `TRADEMARK_*` during
`application_top`. Nothing is cached anywhere else, so a change on the admin
page is live on the next storefront request.

The custom anchor row carries a `val_function`, which has a side effect the
plugin relies on: on the single-setting edit form, Zen Cart's admin sanitizer
leaves such a value alone instead of HTML-escaping it. The group **Save All**
form that Zen Cart 2.2 added is filtered differently and strips `="` from what
you type, which is why the recommended anchor syntax is `id=NAME` /
`class=NAME`: nothing in it to strip, on any release. See
[COMPATIBILITY.md](COMPATIBILITY.md) for the details.
