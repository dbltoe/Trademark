<?php
/**
 * Trademark -- admin language strings (English).
 *
 * BOX_CONFIGURATION_TRADEMARK is the `language_key` the ScriptedInstaller
 * writes into the `admin_pages` table, so it must exist before the menus are
 * drawn -- hence `extra_definitions`.
 *
 * TEXT_TRADEMARK_ANCHOR_INVALID is the message Zen Cart shows if the custom
 * anchor fails its validation (it is over 1000 characters).
 *
 * @package  Trademark
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

$define = [
    'BOX_CONFIGURATION_TRADEMARK' => 'Trademark',
    'TEXT_TRADEMARK_ANCHOR_INVALID' => 'The custom anchor was not saved: it must be 1000 characters or fewer.',
];

return $define;
