<?php
/*
Plugin Name: Address Autocomplete via Google for Gravity Forms
Plugin Url: https://pluginscafe.com
Version: 1.3.7
Description: This plugin adds autocomplete/suggestion feature to gravity forms address field with google map api
Author: PluginsCafe
Author URI: https://pluginscafe.com
License: GPLv2 or later
Text Domain: gf-google-address-autocomplete
*/

defined('ABSPATH') || die();

if (function_exists('ggaa_fs')) {
    ggaa_fs()->set_basename(false, __FILE__);
} else {
    if (! function_exists('ggaa_fs')) {
        // Create a helper function for easy SDK access.
        function ggaa_fs() {
            global $ggaa_fs;

            if (! isset($ggaa_fs)) {
                // Include Freemius SDK.
                require_once dirname(__FILE__) . '/vendor/freemius/start.php';

                $ggaa_fs = fs_dynamic_init(array(
                    'id'                  => '22226',
                    'slug'                => 'gf-google-address-autocomplete',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_179431341af36c5c08733999db1a6',
                    'is_premium'          => false,
                    'premium_suffix'      => 'PRO',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'menu'                => array(
                        'slug'           => 'gf-google-address-autocomplete-pro',
                        'support'        => false,
                        'contact'        => false,
                        'parent'         => array(
                            'slug' => 'options-general.php',
                        ),
                    ),
                    'is_live'        => true,
                ));
            }

            return $ggaa_fs;
        }

        // Init Freemius.
        ggaa_fs();
        // Signal that SDK was initiated.
        do_action('ggaa_fs_loaded');
    }
}


define('GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM', '1.3.7');
define('GF_AUTO_ADDRESS_COMPLETE_FILE', __FILE__);
define('GF_AUTO_ADDRESS_COMPLETE_PATH', plugin_dir_path(__FILE__));
define('GF_AUTO_ADDRESS_COMPLETE_URL', plugin_dir_url(__FILE__));

if (is_admin()) {
    require_once 'admin/class-admin.php';
}

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


function gf_auto_address_complete_redirect_after_update() {
    if (! is_admin() || ! current_user_can('manage_options')) {
        return;
    }

    $saved_version   = get_option('gfgaa_version_free');
    $current_version = GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM;
    $should_redirect = false;

    if ($saved_version === false) {
        $should_redirect = true;
    } elseif (version_compare($saved_version, $current_version, '<')) {
        $should_redirect = true;
    }

    if ($should_redirect) {
        update_option('gfgaa_version_free', $current_version);
        $redirect_url = admin_url('options-general.php?page=gf-google-address-autocomplete-pro');
        wp_safe_redirect($redirect_url);
        exit;
    }
}
add_action('admin_init', 'gf_auto_address_complete_redirect_after_update');
