<?php

/*
Plugin Name: WP-Invites widget
Plugin URI: http://dev.fiff.se/wordpress-plugins/wpinviteswidget
Description: A widget for the WP-Invites plugin by Jehy – http://wordpress.org/extend/plugins/wp-invites/
Author: nettle
Version: 1.0
Author URI: http://dev.fiff.se
Licence: GPL2
*/

/*  Copyright 2010  nettle  (email : nettle@riseup.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$plugin = plugin_basename (__FILE__);

// Is the wp-invites plugin available?
if (file_exists (WP_PLUGIN_DIR . '/wp-invites/wp-invites.php')) {
	// Yes it is. Now, check for compability.
	require_once (WP_PLUGIN_DIR . '/wp-invites/wp-invites.php');
	if (function_exists ('invites_get_options') && function_exists ('invites_make') && function_exists ('invites_add')) {
		// Nice, everything seems ok. So let's load some classes and add some action hooks.
		require_once ('classes/WP_Invites_Widget.php');
		add_action ('widgets_init', 'load_wp_invites_widget');
		add_action ('wp_ajax_wp_invites_widget', 'ajax_wp_invites_widget');
	} else {
		add_action ('after_plugin_row_' . $plugin, 'show_error_compatible_wp_invites_widget');
	}
} else {
	add_action ('after_plugin_row_' . $plugin, 'show_error_missing_wp_invites_widget');
}

// Loads the wp-invites-widget plugin.
function load_wp_invites_widget () {
	register_widget ('WP_Invites_Widget');
	wp_enqueue_script ('wp-invites-widget', WP_PLUGIN_URL . '/wp-invites-widget/javascripts/wp-invites-widget.js', array ('jquery-form'), '1.4');
}

// Takes care of the ajax command from the widget.
function ajax_wp_invites_widget () {
	preg_match_all ('/([a-zA-Z0-9])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/i', $_POST ['email'], $emails);
	if (count ($emails [0])) {
		global $current_user;
		get_currentuserinfo ();
		invites_get_options ();
		$wp_invites_options ['INVITE_LENGTH'] = 1;
		$widget = new WP_Invites_Widget ();
		$widget->setInstance ($_POST ['id']);

		for ($i = 0, $l = count ($emails [0]); $i < $l; $i++) {
			$code = invites_make ();
			invites_add ($code);
			$widget->sendInvitationEmail ($current_user, $emails [0][$i], $code);
		}

		echo $widget->getMessage ();
	} else {
		echo 'FALSE';
	}

	exit;
}

// The wp-invites plugin is missing. Let's show this for the admin in the plugin-list.
function show_error_missing_wp_invites_widget () {
	$columns = substr ($wp_version, 0, 3) >= "2.8" ? 3 : 5;
	echo '<tr><td colspan="' . $columns . '">The <a href="http://wordpress.org/extend/plugins/wp-invites/">wp-invites plugins</a> was not found (' . WP_PLUGIN_DIR . '/wp-invites/wp-invites.php).</td></tr>';
}

// The wp-invites plugin is not compatible. Let's show this for the admin in the plugin-list.
function show_error_compatible_wp_invites_widget () {
	$columns = substr ($wp_version, 0, 3) >= "2.8" ? 3 : 5;
	echo '<tr><td colspan="' . $columns . '">The <a href="http://wordpress.org/extend/plugins/wp-invites/">wp-invites plugins</a> was not compatible (invites_get_options, invites_make and invites_add).</td></tr>';
}