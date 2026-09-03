<?php
/**
 * Trademark -- plugin manifest.
 *
 * @package  Trademark
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

// Only ever read by Plugin Manager, inside the admin, where this is defined.
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

/**
 * Links shown in the Plugin Manager, alongside Install / Uninstall / Disable.
 *
 * Zen Cart stores `pluginDescription` in `plugin_control.description` (a TEXT
 * column) and echoes it into the Plugin Manager's info box as raw HTML,
 * without escaping, on every release from v1.5.8 to v3.0.0. That box is also
 * where the action buttons live, so markup placed here appears exactly once,
 * right where the store owner is already looking, with no core file changed.
 *
 * The Read Me link is built from DIR_WS_CATALOG rather than hard-coded, so it
 * works whatever the store lives at and whatever the admin directory has been
 * renamed to. Zen Cart's shipped `zc_plugins/.htaccess` denies everything then
 * explicitly re-allows `.html`, so readme.html is reachable by design.
 *
 * On v2.2 and later the description is refreshed from this file on every
 * Plugin Manager scan. On v1.5.8, v2.0 and v2.1 it is not: those releases
 * write plugin_control.description only on the INSERT that first creates the
 * row, so whatever is here the first time a store scans the plugin is what
 * that store shows for good. Nothing state-dependent belongs in it.
 */
$tmPluginDir = 'zc_plugins/Trademark/v2.0.0/';
$tmReadmeUrl = (defined('DIR_WS_CATALOG') ? DIR_WS_CATALOG : '/') . $tmPluginDir . 'readme.html';
$tmGithubUrl = 'https://github.com/dbltoe/Trademark';

/**
 * The Zen Cart forum's support thread for this plugin: "Trademark in the
 * Navigation Bar", opened by dbltoe on 9 Oct 2023.
 *
 * It is set BEFORE THE FIRST RELEASE, not after, on purpose. On v1.5.8, v2.0
 * and v2.1 the description is captured on the first scan and never refreshed,
 * so a link added later would reach nobody on half the supported range short
 * of an uninstall and re-install. An empty string renders nothing at all.
 *
 * The forum now runs on XenForo, whose thread address is /threads/<id>; that
 * page opens on the first post, which is the authoritative description of
 * the plugin. The older /showthread.php?t=<id> form no longer resolves to
 * the same thread, so don't use it.
 */
$tmForumUrl = 'https://www.zen-cart.com/threads/205827/';

/**
 * "Installed, but switched off."
 *
 * TRADEMARK_STATUS is a configuration constant, defined during application_top
 * -- which runs before plugin_manager.php re-reads this file. So:
 *
 *   - constant undefined  -> not installed. Say nothing.
 *   - defined and 'true'  -> live. Say nothing.
 *   - defined, not 'true' -> installed and dormant. Say so, in the NAME.
 *
 * The name and not the description, because plugin_control.description is
 * frozen at first insert on v1.5.8/v2.0/v2.1 and a notice there would never
 * clear. The name is kept honest on every release by the plugin's own admin
 * extra_functions file. It is plain text by necessity: plugin_control.name is
 * varchar(64) and the plugin list echoes it unescaped, so markup could be
 * truncated mid-tag.
 */
$tmIsOff = defined('TRADEMARK_STATUS') && TRADEMARK_STATUS !== 'true';

$tmName = 'Trademark';
if ($tmIsOff) {
    $tmName .= ' - Mod Not Turned On';
}

$tmButtonGap = '6px';

/**
 * Styled as buttons matching Plugin Manager's own controls, which use exactly
 * `class="btn btn-primary" role="button"`. Spacing is inline so an admin
 * theme's own .btn margin cannot change it: before = between = after.
 */
$tmLinks =
    '<div style="margin:10px 0 0;padding:0 0 0 ' . $tmButtonGap . '">'
    . '<a href="' . $tmReadmeUrl . '" target="_blank" rel="noopener noreferrer"'
    . ' class="btn btn-primary" role="button"'
    . ' style="margin:0 ' . $tmButtonGap . ' 0 0">Read Me</a>'
    . '<a href="' . $tmGithubUrl . '" target="_blank" rel="noopener noreferrer"'
    . ' class="btn btn-primary" role="button"'
    . ' style="margin:0 ' . $tmButtonGap . ' 0 0">GitHub</a>'
    . '</div>';

$tmForumLink = '';
if ($tmForumUrl !== '') {
    $tmForumLink =
        '<div style="margin:8px 0 0;padding:0 0 0 ' . $tmButtonGap . '">'
        . '<a href="' . $tmForumUrl . '" target="_blank" rel="noopener noreferrer">'
        . 'Forum Support Thread</a>'
        . '</div>';
}

return [
    'pluginVersion' => 'v2.0.0',
    'pluginName' => $tmName,
    'pluginDescription' =>
        'Shows your trademark -- your store name unless you say otherwise -- in the '
        . 'navigation bar of every storefront page, beside the menu icon on phones and '
        . 'tablets. Works with responsive_classic and ZCA Bootstrap and their clones out '
        . 'of the box, and with any other template through a custom anchor. No template '
        . 'file is changed: install, and it is there.'
        . $tmLinks
        . $tmForumLink,
    // Shown as the Author in Plugin Manager, and stored in
    // plugin_control.author / plugin_control_versions.author (varchar(64)).
    'pluginAuthor' => 'My Zen Cart Host (dbltoe)',
    // ID from the Zen Cart Plugins Library: the "Trademark" listing at
    // https://www.zen-cart.com/downloads.php?do=file&id=2378
    //
    // It is written to plugin_control.zc_contrib_id and is the only thing
    // that makes "a new version is available" work in Plugin Manager. On
    // v1.5.8/v2.0/v2.1 the column is written only by the INSERT that first
    // creates the row, so it has to be right the first time a store installs.
    'pluginId' => 2378,
    'zcVersions' => ['v158', 'v200', 'v210', 'v220', 'v230', 'v300'],
    'changelog' => 'changelog.txt',
    'github_repo' => $tmGithubUrl,
    'pluginGroups' => [],
];
