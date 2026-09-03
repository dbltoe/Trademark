<?php
/**
 * Trademark -- storefront functions.
 *
 * Everything the plugin does on the storefront is in this file, and all of it
 * is plain string work on the page Zen Cart has already rendered. No template
 * file is read, copied or overridden.
 *
 * HOW THE MARK GETS ONTO THE PAGE
 * -------------------------------
 * There is no notifier inside tpl_header.php on any supported release, and
 * a zc_plugin cannot supply a template override. What every release from
 * v1.5.8 to v3.0.0 does have is NOTIFY_HTML_HEAD_END (fired just before
 * </head>) and NOTIFY_FOOTER_END (fired just before </body>). The observer
 * opens an output buffer at the first and closes it at the second, and the
 * buffer's callback -- trademark_ob_callback() below -- inserts the mark into
 * the header markup on its way out.
 *
 * Which markup is recognized:
 *
 *   responsive_classic   `<div class="header Fixed">` -- the fixed bar with the
 *   (and its clones)     hamburger on phones and tablets -- and the first
 *                        visible entry of `<div id="navMain">`. Both are
 *                        byte-identical in the v1.5.8, v2.0, v2.1, v2.2, v2.3
 *                        and v3.0 templates.
 *   ZCA Bootstrap        the opening `<nav class="navbar ...">` tag, unchanged
 *   (and its clones)     from Bootstrap 3.3.0 through 3.8.0.
 *   anything else        a fragment the store owner supplies in the admin
 *                        (Custom Anchor), or optionally the top of <body>.
 *
 * If the page already carries a `class="tradeMark"` element -- a store still
 * running the old file-based version of this mod -- nothing is added, so the
 * two never double up.
 *
 * PHP 7.4 is the floor (Zen Cart v1.5.8), so nothing newer than 7.4 syntax is
 * used here: no str_contains(), no match, no nullsafe operator.
 *
 * @package  Trademark
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

/* ------------------------------------------------------------------ *
 * The markup this plugin looks for.
 *
 * define() rather than const so the file can be included more than once
 * (the test harness does) without a redeclaration error.
 * ------------------------------------------------------------------ */

/**
 * responsive_classic, phone and tablet layouts: the fixed bar whose first
 * child is the hamburger link. The mark goes right after that link, so it
 * sits beside the menu icon.
 */
if (!defined('TRADEMARK_PATTERN_MOBILE_BAR')) {
    define('TRADEMARK_PATTERN_MOBILE_BAR',
        '~<div\b[^>]*\bclass="header Fixed"[^>]*>\s*<a\b[^>]*href="#menu"[^>]*>.*?</a>~s');
}

/**
 * responsive_classic, every layout: the first VISIBLE entry of the main
 * navigation list. The tablet layout opens its list with a `class="hide"`
 * item (the Back to Top link), and a mark placed inside that would be hidden
 * with it, so any number of hidden entries are stepped over first.
 *
 * The mark lands inside the <li>, ahead of its link, exactly where the
 * file-based version of this mod put it.
 */
if (!defined('TRADEMARK_PATTERN_NAV_MAIN')) {
    define('TRADEMARK_PATTERN_NAV_MAIN',
        '~<div\b[^>]*\bid="navMain"[^>]*>\s*<ul(?:\s[^>]*)?>\s*'
        . '(?:<li\b[^>]*\bclass="[^"]*\bhide\b[^"]*"[^>]*>.*?</li>\s*)*'
        . '<li(?:\s[^>]*)?>~s');
}

/**
 * ZCA Bootstrap: the header navbar's opening tag. The mark becomes the
 * navbar's first flex item, ahead of the toggler and the menu.
 */
if (!defined('TRADEMARK_PATTERN_NAVBAR')) {
    define('TRADEMARK_PATTERN_NAVBAR',
        '~<nav\b[^>]*\bclass="[^"]*\bnavbar\b[^"]*"[^>]*>~');
}

/**
 * Last resort, only when the owner has switched it on: the top of the body.
 */
if (!defined('TRADEMARK_PATTERN_BODY')) {
    define('TRADEMARK_PATTERN_BODY', '~<body\b[^>]*>~');
}

/* ------------------------------------------------------------------ *
 * Settings
 * ------------------------------------------------------------------ */

/**
 * A configuration value, or a default when the key is not defined.
 *
 * Deliberately not zen_config(): that helper is v3.0.0 only and a fatal
 * "undefined function" on everything earlier. This is its own fallback form.
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function trademark_config($key, $default = '')
{
    return defined($key) ? (string)constant($key) : $default;
}

/**
 * @return bool
 */
function trademark_is_enabled()
{
    return trademark_config('TRADEMARK_STATUS', 'false') === 'true';
}

/**
 * The character set Zen Cart is running in.
 *
 * @return string
 */
function trademark_charset()
{
    return defined('CHARSET') ? CHARSET : 'utf-8';
}

/**
 * Make a stored text value safe to print, exactly once.
 *
 * Zen Cart's admin stores an ordinary configuration value with its <, >, &
 * and " already converted to entities (the request sanitizer does it), and
 * templates echo STORE_NAME raw on the strength of that. Escaping such a
 * value again would print "&amp;" where the owner typed "&". So it is
 * decoded first and then escaped once, which gives the right answer whether
 * the stored value was escaped, raw, or a mixture.
 *
 * @param string $value
 * @return string
 */
function trademark_normalize_text($value)
{
    $decoded = html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, trademark_charset());

    return htmlspecialchars($decoded, ENT_QUOTES | ENT_HTML5, trademark_charset());
}

/**
 * The text of the mark: the owner's setting, or the store name.
 *
 * @return string HTML-safe
 */
function trademark_text()
{
    $text = trim(trademark_config('TRADEMARK_TEXT', ''));
    if ($text === '') {
        $text = trim(trademark_config('STORE_NAME', ''));
    }

    return trademark_normalize_text($text);
}

/**
 * The optional symbol after the text, as a superscript.
 *
 * Written as entities so the result is correct whatever charset the page is
 * served in.
 *
 * @return string
 */
function trademark_symbol_html()
{
    switch (trademark_config('TRADEMARK_SYMBOL', 'None')) {
        case 'TM':
            return '<sup class="tradeMarkSymbol">&trade;</sup>';
        case 'R':
            return '<sup class="tradeMarkSymbol">&reg;</sup>';
        case 'SM':
            return '<sup class="tradeMarkSymbol">&#8480;</sup>';
    }

    return '';
}

/**
 * The element inserted into the page.
 *
 * `tradeMark` with a capital M is deliberate and is kept from the file-based
 * version of this mod, so a store's existing CSS for it carries on working.
 * The capital also keeps it clear of any `trademark` class a template might
 * define for its own purposes.
 *
 * @return string '' when there is no text to show
 */
function trademark_mark_html()
{
    $text = trademark_text();
    if ($text === '') {
        return '';
    }

    return '<p class="tradeMark">' . $text . trademark_symbol_html() . '</p>';
}

/**
 * Placement settings, read once per request.
 *
 * The custom anchor is used as stored. Its configuration row carries a
 * val_function, and Zen Cart's admin sanitizer leaves a value alone when one
 * is present (NULL_ACTION), so what the owner typed -- angle brackets, quotes
 * and all -- is what is compared against the page.
 *
 * @return array
 */
function trademark_options()
{
    return [
        'nav' => trademark_config('TRADEMARK_SHOW_NAV', 'true') === 'true',
        'mobile_bar' => trademark_config('TRADEMARK_SHOW_MOBILE_BAR', 'true') === 'true',
        'custom_anchor' => trademark_config('TRADEMARK_CUSTOM_ANCHOR', ''),
        'custom_position' => trademark_config('TRADEMARK_CUSTOM_POSITION', 'After'),
        'fallback' => trademark_config('TRADEMARK_FALLBACK', 'false') === 'true',
    ];
}

/* ------------------------------------------------------------------ *
 * Insertion
 * ------------------------------------------------------------------ */

/**
 * Insert $mark immediately after the first match of $pattern.
 *
 * A callback rather than a replacement string, because the mark is built
 * from the store name and a name containing "$" or "\" would otherwise be
 * read as a back-reference.
 *
 * @param string $html
 * @param string $pattern
 * @param string $mark
 * @param int    $count  set to 1 if an insertion was made, else 0
 * @return string
 */
function trademark_insert_after_match($html, $pattern, $mark, &$count)
{
    $count = 0;
    $result = preg_replace_callback($pattern, static function ($m) use ($mark, &$count) {
        $count++;
        return $m[0] . $mark;
    }, $html, 1);

    // A regex failure (a page too deep for the backtrack limit, say) returns
    // null. The page is worth more than the mark, so hand it back untouched.
    if ($result === null) {
        $count = 0;
        return $html;
    }

    return $result;
}

/**
 * The regex for a custom anchor written as `id=NAME` or `class=NAME`.
 *
 * This is the recommended form of the custom anchor, and the reason is the
 * admin, not the storefront. Zen Cart v2.2 and later save a configuration
 * group through one form, and the request sanitizer runs every value in it
 * through the "product name" filter, which strips any `="` that is not
 * `class="` or `style="`. A raw fragment such as `<div id="navMain">` comes
 * out the other side as `<div inavMain">` and never matches a page. `id=navMain`
 * carries no quote after its `=`, so every release stores it as typed.
 *
 * `id=NAME` matches the first opening tag whose id attribute is exactly NAME.
 * `class=NAME` matches the first opening tag whose class attribute contains
 * NAME as whole words -- `class=navbar` finds
 * `<nav class="navbar fixed-top ...">`. Either way the match is the whole
 * opening tag, so "After" puts the mark inside the element as its first
 * child and "Before" puts it just ahead of the element.
 *
 * @param string $anchor the setting, already trimmed
 * @return string a pattern, or '' when the setting is not in this form
 */
function trademark_anchor_pattern($anchor)
{
    if (strpos($anchor, '<') !== false || preg_match('~^(id|class)\s*=\s*(.+)$~i', $anchor, $m) !== 1) {
        return '';
    }

    $value = trim($m[2], " \t\"'");
    if ($value === '') {
        return '';
    }

    $quoted = preg_quote($value, '~');
    if (strtolower($m[1]) === 'id') {
        return '~<[a-z][a-z0-9-]*\b[^>]*\bid\s*=\s*["\']' . $quoted . '["\'][^>]*>~i';
    }

    return '~<[a-z][a-z0-9-]*\b[^>]*\bclass\s*=\s*["\'][^"\']*(?<![\w-])' . $quoted . '(?![\w-])[^"\']*["\'][^>]*>~i';
}

/**
 * Put the mark into a rendered page.
 *
 * Pure: takes a page and settings, returns the page and where the mark was
 * placed. Nothing here reads Zen Cart state, which is what makes it testable
 * against captured storefront output without a Zen Cart install.
 *
 * Rules, in order:
 *
 *   0. A page that already contains a `class="tradeMark"` element is left
 *      alone. That is the file-based mod still installed in the template;
 *      adding a second mark would be worse than adding none.
 *   1. A custom anchor, when set, is the ONLY placement tried (fallback
 *      aside). The owner has said where the mark goes. It is either
 *      `id=NAME` / `class=NAME` (see trademark_anchor_pattern) or an exact
 *      fragment of the page's HTML.
 *   2. Otherwise the mobile bar and the main navigation are tried, each
 *      under its own switch. On a phone page only the bar is marked; on a
 *      tablet page both are, with the navigation copy carrying an extra
 *      class that the stylesheet hides in portrait; on a desktop page only
 *      the navigation is. Bootstrap's navbar counts as the main navigation.
 *   3. If nothing has been placed and the fallback is on, the mark goes in
 *      at the top of <body>.
 *
 * @param string $html    the rendered page
 * @param string $mark    from trademark_mark_html()
 * @param array  $options from trademark_options()
 * @return array ['html' => string, 'placed' => string[]]
 */
function trademark_inject($html, $mark, array $options)
{
    $result = ['html' => $html, 'placed' => []];

    if ($mark === '' || preg_match('~class="tradeMark[\s"]~', $html) === 1) {
        return $result;
    }

    $custom = isset($options['custom_anchor']) ? trim((string)$options['custom_anchor']) : '';

    if ($custom !== '') {
        /* Either `id=NAME` / `class=NAME` (matched as a whole opening tag) or
         * an exact fragment of the page. Both yield the same two numbers: where
         * the anchor starts and how long it is. */
        $pos = false;
        $length = 0;
        $pattern = trademark_anchor_pattern($custom);
        if ($pattern !== '') {
            if (preg_match($pattern, $html, $m, PREG_OFFSET_CAPTURE) === 1) {
                $pos = $m[0][1];
                $length = strlen($m[0][0]);
            }
        } else {
            $pos = strpos($html, $custom);
            $length = strlen($custom);
        }

        if ($pos !== false) {
            $before = isset($options['custom_position']) && $options['custom_position'] === 'Before';
            $at = $before ? $pos : $pos + $length;
            $result['html'] = substr($html, 0, $at) . $mark . substr($html, $at);
            $result['placed'][] = 'custom';
        }
    } else {
        $count = 0;

        /* A page carrying the fixed bar is a phone or tablet page. That
         * decides what happens to the navigation list below: see the
         * callback in the nav block. */
        $hasBar = preg_match(TRADEMARK_PATTERN_MOBILE_BAR, $html) === 1;

        if ($hasBar && !empty($options['mobile_bar'])) {
            $result['html'] = trademark_insert_after_match($result['html'], TRADEMARK_PATTERN_MOBILE_BAR, $mark, $count);
            if ($count > 0) {
                $result['placed'][] = 'mobile_bar';
            }
        }

        if (!empty($options['nav'])) {
            /* responsive_classic's three layouts stack differently at the
             * top of the page, and the navigation copy of the mark has to
             * follow suit:
             *
             *   desktop   the list is an ordinary bar. Mark it.
             *   phone     the list is a fixed strip laid OVER the bar, in
             *             both orientations, so a mark in it would sit on
             *             top of the one beside the hamburger. Do not mark
             *             it: the bar copy is the phone's mark.
             *   tablet    the same overlap in portrait -- but in landscape
             *             the template hides the bar and turns the list
             *             into an ordinary one, so the list copy is the
             *             only mark there is. Mark it, with an extra class
             *             the stylesheet hides in portrait. A tablet list is
             *             told from a phone list by the hidden Back-to-Top
             *             item it opens with.
             */
            $placed = '';
            $result['html'] = preg_replace_callback(TRADEMARK_PATTERN_NAV_MAIN,
                static function ($m) use ($mark, $hasBar, &$placed) {
                    if (!$hasBar) {
                        $placed = 'nav';
                        return $m[0] . $mark;
                    }
                    $isTablet = preg_match('~<li\b[^>]*\bclass="[^"]*\bhide\b~', $m[0]) === 1;
                    if (!$isTablet) {
                        return $m[0];
                    }
                    $placed = 'nav_tablet';
                    return $m[0] . str_replace('<p class="tradeMark">', '<p class="tradeMark tradeMarkTablet">', $mark);
                }, $result['html'], 1);
            if ($result['html'] === null) {
                $result['html'] = $html;
                $placed = '';
            }

            if ($placed !== '') {
                $result['placed'][] = $placed;
            } elseif (!$hasBar) {
                $result['html'] = trademark_insert_after_match($result['html'], TRADEMARK_PATTERN_NAVBAR, $mark, $count);
                if ($count > 0) {
                    $result['placed'][] = 'navbar';
                }
            }
        }
    }

    if ($result['placed'] === [] && !empty($options['fallback'])) {
        $wrapped = '<div class="tradeMarkFallback">' . $mark . '</div>';
        $result['html'] = trademark_insert_after_match($result['html'], TRADEMARK_PATTERN_BODY, $wrapped, $count);
        if ($count > 0) {
            $result['placed'][] = 'body';
        }
    }

    return $result;
}

/**
 * Output-buffer callback: the page passes through here on its way out.
 *
 * Registered by the observer with ob_start() at NOTIFY_HTML_HEAD_END. PHP
 * calls it when the buffer is flushed -- normally by the observer at
 * NOTIFY_FOOTER_END, or at script end if that notifier never fires (a custom
 * template that dropped it, say). Either way the mark is inserted.
 *
 * Partial flushes are folded back together. If something between the two
 * notifiers calls ob_flush(), PHP hands over what it has so far without the
 * FINAL flag; that is held here and prepended when the final chunk arrives,
 * so the patterns always see the whole page and never a header cut in half.
 *
 * @param string $buffer
 * @param int    $phase  PHP_OUTPUT_HANDLER_* flags
 * @return string
 */
function trademark_ob_callback($buffer, $phase = 0)
{
    static $held = '';

    if (($phase & PHP_OUTPUT_HANDLER_FINAL) === 0 && ($phase & PHP_OUTPUT_HANDLER_END) === 0) {
        $held .= $buffer;
        return '';
    }

    $page = $held . $buffer;
    $held = '';

    $result = trademark_inject($page, trademark_mark_html(), trademark_options());

    return $result['html'];
}

/* ------------------------------------------------------------------ *
 * The <head> additions
 * ------------------------------------------------------------------ */

/**
 * A web path for a catalog-relative file path.
 *
 * @param string $relative e.g. includes/templates/responsive_classic/css/trademark.css
 * @return string
 */
function trademark_catalog_path($relative)
{
    $base = defined('DIR_WS_CATALOG') ? DIR_WS_CATALOG : '/';

    return $base . ltrim($relative, '/');
}

/**
 * Where the stylesheet is: the active template's own copy if it has one,
 * otherwise the copy shipped inside this plugin.
 *
 * The plugin path is worked out from this file's location, so the version
 * directory can change without anything here being edited. Zen Cart's shipped
 * zc_plugins/.htaccess denies everything then re-allows .css, which is what
 * lets a browser fetch it from there.
 *
 * @return string '' if neither file exists
 */
function trademark_stylesheet_href()
{
    $filename = 'trademark.css';
    $catalog = defined('DIR_FS_CATALOG') ? DIR_FS_CATALOG : '';

    if (defined('DIR_WS_TEMPLATE')) {
        $templatePath = DIR_WS_TEMPLATE . 'css/' . $filename;
        if ($catalog !== '' && is_file($catalog . $templatePath)) {
            return trademark_catalog_path($templatePath);
        }
    }

    $pluginRoot = dirname(__DIR__, 4);
    $pluginRelative = 'zc_plugins/' . basename(dirname($pluginRoot)) . '/' . basename($pluginRoot)
        . '/catalog/includes/templates/template_default/css/' . $filename;

    if ($catalog !== '' && is_file($catalog . $pluginRelative)) {
        return trademark_catalog_path($pluginRelative);
    }

    return '';
}

/**
 * The text color, checked to be a CSS color before it goes inside <style>.
 *
 * Anything that does not look like a color falls back to the default rather
 * than being printed: this is the one setting that reaches the page as code
 * rather than as text.
 *
 * @return string
 */
function trademark_css_color()
{
    $value = trim(trademark_config('TRADEMARK_COLOR', '#FFFFFF'));
    if (preg_match('~^(#[0-9a-f]{3,8}|[a-z]{3,30}|(rgb|hsl)a?\([0-9%.,\s/]+\)|var\(--[a-z0-9_-]+\))$~i', $value) === 1) {
        return $value;
    }

    return '#FFFFFF';
}

/**
 * @return string
 */
function trademark_css_font_size()
{
    $value = trim(trademark_config('TRADEMARK_FONT_SIZE', '1em'));
    if (preg_match('~^(\d*\.?\d+(px|em|rem|%|pt|vw|vh)|inherit|smaller|larger|(xx?-)?(small|large)|medium)$~i', $value) === 1) {
        return $value;
    }

    return '1em';
}

/**
 * @return string
 */
function trademark_css_font_weight()
{
    $value = strtolower(trim(trademark_config('TRADEMARK_FONT_WEIGHT', 'normal')));
    if (preg_match('~^(normal|bold|bolder|lighter|[1-9]00)$~', $value) === 1) {
        return $value;
    }

    return 'normal';
}

/**
 * What goes into <head>: the stylesheet link and the owner's three appearance
 * settings as one inline rule.
 *
 * Both are skipped when "Load The Plugin Stylesheet?" is off, for a store that
 * already styles .tradeMark in its own template CSS.
 *
 * @return string
 */
function trademark_render_head()
{
    static $written = false;

    if ($written || trademark_config('TRADEMARK_LOAD_CSS', 'true') !== 'true') {
        return '';
    }
    $written = true;

    $html = '';

    $href = trademark_stylesheet_href();
    if ($href !== '') {
        $html .= '<link rel="stylesheet" href="'
            . htmlspecialchars($href, ENT_QUOTES, trademark_charset()) . '">' . "\n";
    }

    $html .= '<style>.tradeMark{color:' . trademark_css_color()
        . ';font-size:' . trademark_css_font_size()
        . ';font-weight:' . trademark_css_font_weight() . '}</style>' . "\n";

    return $html;
}
