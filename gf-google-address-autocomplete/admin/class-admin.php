<?php
if (!defined('ABSPATH')) {
    exit;
}

class GFGAA_Admin {
    public function __construct() {
        add_filter('admin_footer_text', [$this, 'admin_footer'], 1, 2);
        add_action('admin_menu', [$this, 'add_menu_under_options']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
    }

    public function admin_scripts() {
        $current_screen = get_current_screen();
        if (strpos($current_screen->base, 'gf-google-address-autocomplete-pro') === false) {
            return;
        }

        wp_enqueue_style('gfgaa_admin_style', GF_AUTO_ADDRESS_COMPLETE_URL . 'admin/css/gfgaa_dashboard_style.css', array(), GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM);
        wp_enqueue_script('gfgaa_admin_script', GF_AUTO_ADDRESS_COMPLETE_URL . 'admin/js/admin.js', array('jquery'), GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM, true);
    }

    public function add_menu_under_options() {
        add_submenu_page(
            'options-general.php',
            'Address Autocomplete For Gravity Forms',
            'GF Address AutoComplete',
            'administrator',
            'gf-google-address-autocomplete-pro',
            [$this, 'gfgaa_admin_page']
        );
    }

    public function gfgaa_admin_page() {
        echo '<div class="gfgaa_dashboard">';
        include_once __DIR__ . '/templates/header.php';

        echo '<div id="pcafe_tab_box" class="pcafe_container">';
        include_once __DIR__ . '/templates/intro.php';
        include_once __DIR__ . '/templates/help.php';
        include_once __DIR__ . '/templates/features.php';
        echo '</div>';
        echo '</div>';
    }

    public function admin_footer($text) {
        global $current_screen;

        if (! empty($current_screen->id) && strpos($current_screen->id, 'gf-google-address-autocomplete') !== false) {
            $url  = 'https://wordpress.org/support/plugin/gf-google-address-autocomplete/reviews/';
            $text = sprintf(
                wp_kses(
                    /* translators: $1$s - WPForms plugin name; $2$s - WP.org review link; $3$s - WP.org review link. */
                    __('Thank you for using %1$s. Please rate us <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> to boost our motivation.', 'gf-google-address-autocomplete'),
                    array(
                        'a' => array(
                            'href'   => array(),
                            'target' => array(),
                            'rel'    => array(),
                        ),
                    )
                ),
                '<strong>Address Autocomplete For Gravity Forms</strong>',
                $url,
                $url
            );
        }

        return $text;
    }

    public function is_active_gravityforms() {
        if (!method_exists('GFForms', 'include_payment_addon_framework')) {
            return false;
        }
        return true;
    }
}


new GFGAA_Admin;
