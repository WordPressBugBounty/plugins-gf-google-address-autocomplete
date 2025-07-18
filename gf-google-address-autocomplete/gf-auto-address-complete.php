<?php
/*
Plugin Name: Address Autocomplete via Google for Gravity Forms
Plugin Url: https://pluginscafe.com
Version: 1.3.5
Description: This plugin adds autocomplete/suggestion feature to gravity forms address field with google map api
Author: PluginsCafe
Author URI: https://pluginscafe.com
License: GPLv2 or later
Text Domain: gf-google-address-autocomplete
*/

defined('ABSPATH') || die();

define('GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM', '1.3.5');
define('GF_AUTO_ADDRESS_COMPLETE_FILE', __FILE__);
define('GF_AUTO_ADDRESS_COMPLETE_PATH', plugin_dir_path(__FILE__));
define('GF_AUTO_ADDRESS_COMPLETE_URL', plugin_dir_url(__FILE__));

add_action('gform_loaded', array('GF_Auto_Address_Complete_Bootstrap', 'load'), 5);
class GF_Auto_Address_Complete_Bootstrap {
    public static function load() {
        if (!method_exists('GFForms', 'include_addon_framework')) {
            return;
        }

        require_once 'class-auto-address-complete.php';
        GFAddOn::register('GFAutoAddressComplete');
    }
}
function GF_Address_Auto_Complete() {
    return GFAutoAddressComplete::get_instance();
}
