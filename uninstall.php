<?php

require_once('constant.php');

if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

delete_option(ESTIS_API_PREFIX . '_array');
delete_option(ESTIS_API_PREFIX . '_main_settings');
delete_option(ESTIS_API_PREFIX . '_api_key');