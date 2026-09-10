<?php
/**
 * Uninstall cleanup for Returns.
 *
 * Runs when the plugin is deleted from wp-admin. Removes the plugin's options.
 * Submitted return requests (the returns_rma custom post type) are intentionally
 * left in place: they are merchant records that should survive a reinstall and
 * can be removed manually from the Return Requests list.
 *
 * @package Returns
 */

declare(strict_types=1);

defined('WP_UNINSTALL_PLUGIN') || exit;

delete_option('returns_settings');
delete_option('returns_db_version');

// The PRO banner's dismissal is stored per user, so it belongs to the
// plugin rather than to the site content. User meta is global, not
// per-site, which is why this uses delete_metadata's \$delete_all rather
// than a loop over the users of one blog.
delete_metadata('user', 0, 'returns_pro_banner_dismissed', '', true);
