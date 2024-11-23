<?php
/*
Plugin Name: Address Autocomplete via Google for Gravity Forms
Plugin Url: https://pluginscafe.com
Version: 1.3.4
Description: This plugin adds autocomplete/suggestion feature to gravity forms address field with google map api
Author: KaisarAhmmed
Author URI: https://pluginscafe.com
License: GPLv2 or later
Text Domain: gravityforms
*/

if (!defined('ABSPATH')) {
    exit;
}


if (!defined('GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM'))
    define('GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM', '1.3.4');

if (!defined('GF_AUTO_ADDRESS_COMPLETE_FILE'))
    define('GF_AUTO_ADDRESS_COMPLETE_FILE', __FILE__);

if (!defined('GF_AUTO_ADDRESS_COMPLETE_PATH'))
    define('GF_AUTO_ADDRESS_COMPLETE_PATH', plugin_dir_path(__FILE__));

if (!defined('GF_AUTO_ADDRESS_COMPLETE_URL'))
    define('GF_AUTO_ADDRESS_COMPLETE_URL', plugin_dir_url(__FILE__));


class GF_auto_address_complete {

    function __construct() {

        if (is_admin()) {
            add_action('plugins_loaded', array($this, 'GF_admin_init'), 14);
        } else {
            add_action('plugins_loaded', array($this, 'frontend_init'), 14);
        }
    }



    /**
     * Init frontend
     */
    function frontend_init() {
        require_once(plugin_dir_path(__FILE__) . 'frontend/class-frontend.php');
    }

    /**
     * Init admin side
     */
    function GF_admin_init() {
        require_once(plugin_dir_path(__FILE__) . 'admin/class-admin.php');
        require_once(plugin_dir_path(__FILE__) . 'admin/class-helper.php');
    }
}

new GF_auto_address_complete();
