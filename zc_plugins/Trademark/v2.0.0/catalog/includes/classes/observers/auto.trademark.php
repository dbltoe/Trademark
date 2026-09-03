<?php
/**
 * Trademark -- storefront observer.
 *
 * Auto-loaded and instantiated by `includes/init_includes/init_observers.php`
 * on every supported Zen Cart release. The filename (`auto.trademark.php`)
 * and the class name must stay in step: Zen Cart derives the expected class
 * as 'zcObserver' . base::camelize('trademark', true).
 *
 * Two notifiers, both present from v1.5.8 through v3.0.0-dev:
 *
 *   NOTIFY_HTML_HEAD_END   just before </head>. The stylesheet link goes out
 *                          here, and then an output buffer is opened so that
 *                          everything from here to the end of the page passes
 *                          through trademark_ob_callback() on its way out.
 *   NOTIFY_FOOTER_END      just before </body>. The buffer is closed, which is
 *                          when the callback runs and the mark is inserted.
 *
 * If NOTIFY_FOOTER_END never fires -- a custom template that dropped it --
 * PHP flushes the buffer at script end and the callback runs then instead.
 * The mark still appears; the page is merely held a little longer.
 *
 * @package  Trademark
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

class zcObserverTrademark extends base
{
    /**
     * The output-buffer nesting level this observer's buffer sits at, or 0
     * when it has none open.
     *
     * Recorded so the close can be certain it is closing its own buffer. If
     * something between the two notifiers opened a buffer of its own and
     * left it open, blindly calling ob_end_flush() here would close THAT one
     * and leave ours for script end. Checking the level first means the
     * wrong buffer is never touched.
     *
     * @var int
     */
    protected $bufferLevel = 0;

    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_HTML_HEAD_END',
            'NOTIFY_FOOTER_END',
        ]);
    }

    /**
     * End of <head>: link the stylesheet, then start buffering the body.
     */
    public function updateNotifyHtmlHeadEnd($class, $eventID, $param1 = null)
    {
        if (!function_exists('trademark_is_enabled') || !trademark_is_enabled()) {
            // The plugin's extra_functions file did not load, or the owner
            // switched the plugin off. Either way, do nothing at all.
            return;
        }

        if ($this->bufferLevel > 0) {
            return;
        }

        echo trademark_render_head();

        if (ob_start('trademark_ob_callback')) {
            $this->bufferLevel = ob_get_level();
        }
    }

    /**
     * End of <body>: close the buffer, which inserts the mark.
     */
    public function updateNotifyFooterEnd($class, $eventID, $param1 = null)
    {
        if ($this->bufferLevel === 0) {
            return;
        }

        // Only close it if it is the innermost buffer. If it is not, someone
        // else's buffer is still open above ours; leave both for script end,
        // where PHP closes them in order and our callback still runs.
        if (ob_get_level() === $this->bufferLevel) {
            ob_end_flush();
        }

        $this->bufferLevel = 0;
    }
}
