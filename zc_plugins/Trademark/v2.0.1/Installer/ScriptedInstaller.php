<?php
/**
 * Trademark -- Plugin Manager installer.
 *
 * Deliberately limited to the API that exists in every supported Zen Cart
 * release (v1.5.8 -> v3.0.0):
 *
 *   - only `executeInstallerSql()` is used for database work. The convenience
 *     helpers (`addConfigurationKey()`, `getOrCreateConfigGroupId()`, ...) were
 *     added in ZC v2.0.1/v2.1.0 and do not exist on v1.5.8.
 *   - `executeUpgrade()` is declared with an optional argument, because ZC
 *     v1.5.8 calls it with none and ZC v2.x/v3.x calls it with $oldVersion.
 *
 * Every step is idempotent, so an upgrade is simply a re-run of the install.
 *
 * @package  Trademark
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    /**
     * Title of the configuration group created for this plugin.
     */
    const CONFIG_GROUP_TITLE = 'Trademark';

    /**
     * admin_pages.page_key values this plugin owns.
     */
    const ADMIN_PAGE_KEYS = ['configTrademark'];

    /**
     * plugin_control.unique_key for this plugin -- its zc_plugins directory.
     */
    const PLUGIN_KEY = 'Trademark';
    const PLUGIN_BASE_NAME = 'Trademark';
    const PLUGIN_OFF_SUFFIX = ' - Mod Not Turned On';

    protected function executeInstall()
    {
        $configGroupId = $this->tmGetOrCreateConfigGroup();
        if ($configGroupId === 0) {
            return false;
        }

        $this->tmAddConfigurationKeys($configGroupId);
        $this->tmRegisterAdminPages($configGroupId);
        $this->tmSyncPluginControlName(false);
        $this->tmRefreshPluginDescription();

        // No quotes in the message: zen_record_admin_activity() escapes them
        // on the way in and the log then shows them backslashed.
        $this->tmLog('Trademark: installed/upgraded. The mark is shown on the storefront while Enable Trademark? is set to true.', 'warning');

        return true;
    }

    /**
     * ZC v1.5.8 calls this with no argument; ZC v2.x/v3.x passes $oldVersion.
     */
    protected function executeUpgrade($oldVersion = null)
    {
        // The install routine is idempotent, so re-running it brings any older
        // installation up to the current settings without touching values the
        // store owner has already changed.
        return $this->executeInstall();
    }

    protected function executeUninstall()
    {
        zen_deregister_admin_pages(self::ADMIN_PAGE_KEYS);

        $groupId = $this->tmGetConfigGroupId();
        if ($groupId > 0) {
            $this->executeInstallerSql(
                "DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = " . $groupId
            );
            $this->executeInstallerSql(
                "DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = " . $groupId
            );
        }

        // Put the plugin's name back before the row stops being ours to fix.
        //
        // On v1.5.8/v2.0/v2.1 the Plugin Manager scan never rewrites
        // plugin_control.name for an existing row, and the admin function that
        // keeps it honest stops loading the moment the plugin is uninstalled.
        // Skipping this would leave "Trademark - Mod Not Turned On" sitting in
        // the Not Installed list permanently.
        $this->tmSyncPluginControlName(true);
        $this->tmRefreshPluginDescription();

        $this->tmLog('Trademark: uninstalled. Settings removed; no template file was ever changed, so there is nothing else to undo.', 'warning');

        return true;
    }

    /**
     * Save the manifest's description again, now that the Settings page has
     * been registered (install, upgrade) or removed (uninstall).
     *
     * Needed on v1.5.8, v2.0 and v2.1, which store plugin_control.description
     * only when Plugin Manager first sees the plugin, before Install, so the
     * Settings button (built in manifest.php from the registered admin page)
     * would never appear there. v2.2 and later rewrite the description from
     * the manifest on every Plugin Manager visit, so this is a no-op there in
     * effect. Core's own install/uninstall step changes only status and
     * version, so this description stays.
     *
     * @return bool
     */
    protected function tmRefreshPluginDescription()
    {
        if (!defined('TABLE_PLUGIN_CONTROL')) {
            return true;
        }
        $manifest = require dirname(__DIR__) . '/manifest.php';
        if (!is_array($manifest) || !isset($manifest['pluginDescription'])) {
            return true;
        }

        return $this->executeInstallerSql(
            "UPDATE " . TABLE_PLUGIN_CONTROL . "
                SET description = '" . $this->dbConn->prepare_input($manifest['pluginDescription']) . "'
              WHERE unique_key = '" . $this->dbConn->prepare_input(self::PLUGIN_KEY) . "'
              LIMIT 1"
        );
    }

    /**
     * Write a line to the admin activity log, if we can.
     *
     * Guarded because the installer runs in an unusual context -- Plugin
     * Manager, mid-transaction, sometimes before this plugin's own files are
     * loaded -- and a missing logger must never be the thing that fails an
     * install or, worse, an uninstall.
     *
     * @param string $message
     * @param string $severity
     * @return void
     */
    protected function tmLog($message, $severity = 'info')
    {
        if (function_exists('zen_record_admin_activity')) {
            zen_record_admin_activity($message, $severity);
        }
    }

    /**
     * Write the plugin_control name the current state calls for.
     *
     * @param bool $clean True to force the plain name, used on uninstall.
     * @return bool
     */
    protected function tmSyncPluginControlName($clean = false)
    {
        if (!defined('TABLE_PLUGIN_CONTROL')) {
            return true;
        }

        $name = self::PLUGIN_BASE_NAME;
        if (!$clean) {
            // Read straight from the table: on install the constant either does
            // not exist yet or still holds the value from before this run.
            $status = $this->tmGetConfigValue('TRADEMARK_STATUS', 'true');
            if ($status !== 'true') {
                $name .= self::PLUGIN_OFF_SUFFIX;
            }
        }

        return $this->executeInstallerSql(
            "UPDATE " . TABLE_PLUGIN_CONTROL . "
                SET name = '" . $this->dbConn->prepare_input($name) . "'
              WHERE unique_key = '" . $this->dbConn->prepare_input(self::PLUGIN_KEY) . "'
              LIMIT 1"
        );
    }

    /* ----------------------------------------------------------------- *
     * Helpers
     * ----------------------------------------------------------------- */

    protected function tmGetConfigGroupId()
    {
        $sql =
            "SELECT configuration_group_id
               FROM " . TABLE_CONFIGURATION_GROUP . "
              WHERE configuration_group_title = '" . $this->dbConn->prepare_input(self::CONFIG_GROUP_TITLE) . "'
              LIMIT 1";
        $check = $this->dbConn->Execute($sql);

        return ($check->EOF) ? 0 : (int)$check->fields['configuration_group_id'];
    }

    protected function tmGetOrCreateConfigGroup()
    {
        $groupId = $this->tmGetConfigGroupId();
        if ($groupId > 0) {
            return $groupId;
        }

        $created = $this->executeInstallerSql(
            "INSERT INTO " . TABLE_CONFIGURATION_GROUP . "
                (configuration_group_title, configuration_group_description, sort_order, visible)
             VALUES
                ('" . $this->dbConn->prepare_input(self::CONFIG_GROUP_TITLE) . "',
                 'Shows your trademark (your store name unless you say otherwise) in the navigation bar on every storefront page, without changing any template file.',
                 0, 1)"
        );
        if ($created === false) {
            return 0;
        }

        $groupId = $this->tmGetConfigGroupId();
        if ($groupId > 0) {
            // Zen Cart convention: a configuration group's sort_order matches its id.
            $this->executeInstallerSql(
                "UPDATE " . TABLE_CONFIGURATION_GROUP . "
                    SET sort_order = $groupId
                  WHERE configuration_group_id = $groupId
                  LIMIT 1"
            );
        }

        return $groupId;
    }

    protected function tmGetConfigValue($key, $default = '')
    {
        $sql =
            "SELECT configuration_value
               FROM " . TABLE_CONFIGURATION . "
              WHERE configuration_key = '" . $this->dbConn->prepare_input($key) . "'
              LIMIT 1";
        $check = $this->dbConn->Execute($sql);

        return ($check->EOF) ? $default : $check->fields['configuration_value'];
    }

    protected function tmRegisterAdminPages($configGroupId)
    {
        // zen_register_admin_page() performs a plain INSERT, so clear first.
        zen_deregister_admin_pages(self::ADMIN_PAGE_KEYS);

        zen_register_admin_page(
            'configTrademark',
            'BOX_CONFIGURATION_TRADEMARK',
            'FILENAME_CONFIGURATION',
            'gID=' . $configGroupId,
            'configuration',
            'Y',
            $configGroupId
        );
    }

    /**
     * A SQL literal for a nullable string column.
     *
     * @param string $value
     * @return string
     */
    protected function tmSqlOrNull($value)
    {
        if ($value === '' || $value === null) {
            return 'NULL';
        }

        return "'" . $this->dbConn->prepare_input($value) . "'";
    }

    /**
     * Insert every configuration key. INSERT IGNORE keeps this idempotent --
     * `configuration_key` carries a UNIQUE index in every supported release, so
     * settings the store owner has already changed are never overwritten.
     */
    protected function tmAddConfigurationKeys($configGroupId)
    {
        foreach ($this->tmConfigurationKeys() as $key) {
            $sql =
                "INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
                    (configuration_title, configuration_key, configuration_value, configuration_description,
                     configuration_group_id, sort_order, date_added, use_function, set_function, val_function)
                 VALUES
                    ('" . $this->dbConn->prepare_input($key['title']) . "',
                     '" . $this->dbConn->prepare_input($key['key']) . "',
                     '" . $this->dbConn->prepare_input($key['value']) . "',
                     '" . $this->dbConn->prepare_input($key['description']) . "',
                     " . (int)$configGroupId . ",
                     " . (int)$key['sort_order'] . ",
                     now(),
                     NULL,
                     " . $this->tmSqlOrNull($key['set_function']) . ",
                     " . $this->tmSqlOrNull($key['val_function']) . ")";

            if ($this->executeInstallerSql($sql) === false) {
                return false;
            }

            // INSERT IGNORE deliberately leaves an existing row alone, which is
            // right for the VALUE -- that belongs to the store owner and must
            // never be reset by an upgrade. It is wrong for everything else:
            // the label, the help text, the ordering, the input type and the
            // validation all belong to the plugin, and a store upgrading from
            // an earlier version should get the current wording rather than
            // keep the old. So follow the insert with an update of the
            // metadata only. On a fresh install this rewrites what was just
            // inserted, which is harmless.
            $update =
                "UPDATE " . TABLE_CONFIGURATION . "
                    SET configuration_title = '" . $this->dbConn->prepare_input($key['title']) . "',
                        configuration_description = '" . $this->dbConn->prepare_input($key['description']) . "',
                        configuration_group_id = " . (int)$configGroupId . ",
                        sort_order = " . (int)$key['sort_order'] . ",
                        set_function = " . $this->tmSqlOrNull($key['set_function']) . ",
                        val_function = " . $this->tmSqlOrNull($key['val_function']) . ",
                        last_modified = now()
                  WHERE configuration_key = '" . $this->dbConn->prepare_input($key['key']) . "'
                  LIMIT 1";

            if ($this->executeInstallerSql($update) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * The full list of configuration records this plugin owns.
     *
     * Titles are Title Case and end in a colon, or a question mark where the
     * setting is a yes/no question.
     */
    protected function tmConfigurationKeys()
    {
        $boolean = "zen_cfg_select_option(array('true', 'false'), ";

        /**
         * The custom anchor carries a val_function, and that is doing two jobs.
         *
         * The obvious one is validation: any text up to 1000 characters.
         *
         * The one that matters is the side effect. On the single-setting edit
         * form (the only form on v1.5.8 through v2.1, and still there on
         * later releases) Zen Cart's admin request sanitizer normally runs a
         * configuration value through htmlspecialchars() before it is stored,
         * which would turn an anchor like `<div id="navMain">` into
         * `&lt;div id=&quot;navMain&quot;&gt;` -- and that never matches a
         * rendered page. When the row carries a val_function the sanitizer
         * applies NULL_ACTION instead and the value is stored exactly as
         * typed. init_sanitize.php checks val_function the same way in v1.5.8
         * and v3.0.0.
         *
         * The group "save all" form that v2.2 added is a different path: its
         * values go through PRODUCT_NAME_DEEP_REGEX before anything per-row,
         * and that strips `="` outside class/style. Which is why the
         * recommended anchor syntax is `id=NAME` / `class=NAME` -- no quote
         * after the `=`, so it survives both paths on every release. The
         * val_function still earns its keep for the fragment form on the
         * single-setting path.
         *
         * The value is never printed anywhere; it is only ever compared
         * against the page.
         */
        $anchorValidation = json_encode([
            'error' => 'TEXT_TRADEMARK_ANCHOR_INVALID',
            'id' => 'FILTER_VALIDATE_REGEXP',
            'options' => ['options' => ['regexp' => '/^[\s\S]{0,1000}$/']],
        ]);

        return [
            [
                'key' => 'TRADEMARK_STATUS',
                'title' => 'Enable Trademark?',
                // On from the start, on purpose: with every other setting at
                // its default this shows the store name, which is exactly what
                // the plugin is for. There is no half-configured state to
                // protect the owner from.
                'value' => 'true',
                'description' => 'Master switch. <strong>true</strong> shows the mark on every storefront page; <strong>false</strong> hides it again without uninstalling. On a fresh install this is already true, and the mark shows your store name.',
                'sort_order' => 10,
                'set_function' => $boolean,
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_TEXT',
                'title' => 'Trademark Text:',
                'value' => '',
                'description' => 'Leave this <strong>empty</strong> to show your store name from <em>Configuration &rarr; My Store &rarr; Store Name</em>, which is the usual case. Enter something here only when the mark you must display differs from the store name.',
                'sort_order' => 20,
                'set_function' => '',
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_SYMBOL',
                'title' => 'Append A Symbol?',
                'value' => 'None',
                'description' => 'Adds &trade;, &reg; or &#8480; after the text as a small superscript. <strong>None</strong> is usually right: the lawyers say the company name itself is enough, whether or not it is registered. Use <strong>R</strong> only for a mark that is actually registered.',
                'sort_order' => 30,
                'set_function' => "zen_cfg_select_option(array('None', 'TM', 'R', 'SM'), ",
                'val_function' => '',
            ],

            /* ---- where it goes ---- */
            [
                'key' => 'TRADEMARK_SHOW_NAV',
                'title' => 'Show In The Main Navigation Bar?',
                'value' => 'true',
                'description' => 'In <strong>responsive_classic</strong> (or a clone of it) the mark goes ahead of the <em>Home</em> link in the top navigation, on desktops and on tablets held in landscape. In <strong>ZCA Bootstrap</strong> (or a clone) it becomes the first item in the navbar, ahead of the menu, on every device.',
                'sort_order' => 40,
                'set_function' => $boolean,
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_SHOW_MOBILE_BAR',
                'title' => 'Show In The Mobile Header Bar?',
                'value' => 'true',
                'description' => 'In <strong>responsive_classic</strong> phones, and tablets held in portrait, get a fixed bar across the top with the menu icon at the left. This puts the mark beside that icon. ZCA Bootstrap has no separate bar, so this setting does nothing there.',
                'sort_order' => 50,
                'set_function' => $boolean,
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_CUSTOM_ANCHOR',
                'title' => 'Custom Anchor (Advanced):',
                'value' => '',
                'description' => 'For a template that is neither responsive_classic nor ZCA Bootstrap, or to put the mark somewhere else. Name the element by its id or class, for example <code>id=navMain</code> or <code>class=navbar</code>, and the mark goes inside the first such element on each page, as its first item. An exact fragment of the page\'s HTML also works, but on Zen Cart 2.2 and later this form strips <code>="</code> from what you type, so a fragment with a quoted attribute cannot be entered here; use the id or class form instead. <strong>When this is set the two settings above are ignored.</strong> Leave it empty for automatic placement.',
                'sort_order' => 60,
                'set_function' => '',
                'val_function' => $anchorValidation,
            ],
            [
                'key' => 'TRADEMARK_CUSTOM_POSITION',
                'title' => 'Custom Anchor Position:',
                'value' => 'After',
                'description' => 'With an id or class anchor, <strong>After</strong> puts the mark inside the element as its first item and <strong>Before</strong> puts it just ahead of the element. With an HTML fragment, immediately after or immediately before the fragment. Only used when a custom anchor is set.',
                'sort_order' => 70,
                'set_function' => "zen_cfg_select_option(array('After', 'Before'), ",
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_FALLBACK',
                'title' => 'Show At The Top Of The Page If Nothing Matches?',
                'value' => 'false',
                'description' => 'If none of the places above can be found in a page, <strong>true</strong> puts the mark at the very top of the page body instead so it is still shown; <strong>false</strong> leaves that page without a mark. Off by default because a mark in an unplanned spot usually looks wrong, but the choice is yours if the mark must appear no matter what.',
                'sort_order' => 80,
                'set_function' => $boolean,
                'val_function' => '',
            ],

            /* ---- how it looks ---- */
            [
                'key' => 'TRADEMARK_LOAD_CSS',
                'title' => 'Load The Plugin Stylesheet?',
                'value' => 'true',
                'description' => '<strong>true</strong> links the plugin\'s small stylesheet and applies the three settings below. Set to <strong>false</strong> if your template already styles <code>.tradeMark</code> in its own CSS, for example from the earlier file-based version of this mod, so the plugin does not override it.',
                'sort_order' => 100,
                'set_function' => $boolean,
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_COLOR',
                'title' => 'Text Color:',
                'value' => '#FFFFFF',
                'description' => 'Any CSS color, for example <code>#FFFFFF</code> or <code>gold</code>. White suits the dark navigation bars in responsive_classic and ZCA Bootstrap. Check the contrast against your own bar color; a value that does not look like a color is ignored and white is used.',
                'sort_order' => 110,
                'set_function' => '',
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_FONT_SIZE',
                'title' => 'Font Size:',
                'value' => '1em',
                'description' => 'Any CSS size, for example <code>1em</code>, <code>1.2rem</code> or <code>16px</code>. <code>1em</code> matches the navigation links around it.',
                'sort_order' => 120,
                'set_function' => '',
                'val_function' => '',
            ],
            [
                'key' => 'TRADEMARK_FONT_WEIGHT',
                'title' => 'Font Weight:',
                'value' => 'normal',
                'description' => 'Whether the mark is drawn in the same weight as the navigation links or in bold.',
                'sort_order' => 130,
                'set_function' => "zen_cfg_select_option(array('normal', 'bold'), ",
                'val_function' => '',
            ],
        ];
    }
}
