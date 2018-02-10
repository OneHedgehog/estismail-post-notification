<?php defined('ABSPATH') or exit;
/*
* Plugin Name: Estismail post notification
* Plugin URI: https://estismail.com
* Description: Send posts to subscribers email
* Author: Estismail
* Version: 1.0
* Author URI: https://estismail.com
* Text Domain: estis_pn_translate
* Domain Path: langs/
*/

if (version_compare(get_bloginfo('version'), '4', '<')) {
    wp_die(__('please, update the WordPress to use our plugin', 'estis_pn_translate'));
}

require_once('constant.php');

function estis_pn_activation_plugin()
{
    add_option(ESTIS_API_PREFIX . '_main_settings');
}

register_activation_hook(__FILE__, ESTIS_API_PREFIX . '_activation_plugin');

define('API_URL', "https://v1.estismail.com");
define('ESTIS_PN_ABS_PATH', plugin_dir_path(__FILE__) . "estismail-post-notification.php");

require_once('functions.php');
require_once('include/style-and-js.php');
require_once('include/menus.php');
require_once('include/plugin-settings.php');
require_once('include/list-settings.php');
require_once('templates/templates-settings.php');
require_once('templates/templates-add.php');
require_once('templates/templates-edit.php');
require_once('templates/templates-preview.php');

add_action('admin_menu', ESTIS_API_PREFIX . '_admin_menu');
add_action('plugins_loaded', ESTIS_API_PREFIX . '_true_load_textdomain');

function estis_pn_true_load_textdomain()
{
    load_plugin_textdomain('estis_pn_translate', false, dirname(plugin_basename(__FILE__)) . '/langs/');
}