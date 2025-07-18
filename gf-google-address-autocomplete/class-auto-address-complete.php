<?php
defined('ABSPATH') || exit;

GFForms::include_addon_framework();

class GFAutoAddressComplete extends GFAddOn {

    protected $_version = GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM;
    protected $_min_gravityforms_version = '2.8';
    protected $_slug = 'gf-google-address-autocomplete';
    protected $_path = 'gf-google-address-autocomplete/gf-auto-address-complete.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Address Auto Complete For Gravity Forms';
    protected $_short_title = 'Address AutoComplete';

    private static $_instance = null;

    /**
     * Get an instance of this class.
     *
     * @return GFAutoAddressComplete
     */
    public static function get_instance() {
        if (self::$_instance == null) {
            self::$_instance = new GFAutoAddressComplete();
        }

        return self::$_instance;
    }


    /**
     * Handles hooks and loading of language files.
     */
    public function init() {
        parent::init();

        add_filter('gform_tooltips', [$this, 'add_tooltips']);
        add_action('gform_editor_js', [$this, 'editor_script']);
        add_filter('gform_register_init_scripts', [$this, 'add_init_script'], 10, 2);

        add_filter('gform_addon_navigation', [$this, 'api_key_menu_item']);
        add_filter('gform_field_settings_tabs', [$this, 'pcafe_aac_fields_settings_tab'], 10, 2);
        add_action('gform_field_settings_tab_content_address_auto_complete', [$this, 'pcafe_aac_fields_settings_tab_content'], 10, 2);
    }

    public function get_menu_icon() {
        // return 'gform-icon--place';
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>';
    }

    /**
     * Return the scripts which should be enqueued.
     *
     * @return array
     */
    public function scripts() {
        $api_key = $this->get_google_places_api_key();

        $frontend_enqueue_condition = ! is_admin() ? array(array($this, 'frontend_script_callback')) : array(function () {
            return false;
        });

        $is_async = $this->get_plugin_setting('pcafe_load_with_async');

        if ($is_async) {
            $map_api_url = 'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places&loading=async';
        } else {
            $map_api_url = 'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places';
        }


        $scripts = array(
            array(
                'handle'  => 'pcafe_aac_admin_script',
                'src'     => $this->get_base_url() . '/js/pcafe_aac_admin.js',
                'version' => $this->_version,
                'deps'    => array('jquery'),
                'enqueue'  => array(
                    array('admin_page' => array('form_editor')),
                ),
                'in_footer' => true,
            ),
            array(
                'handle'    => 'pcafe_maps_api_js',
                'src'       => $map_api_url,
                'version'   => $this->_version,
                'deps'      => array(),
                'in_footer' => true,
                'enqueue'   => $frontend_enqueue_condition
            ),
            array(
                'handle'    => 'pcafe_aac_script',
                'src'       => $this->get_base_url() . '/js/pcafe_aac_active.js',
                'version'   => time(),
                'deps'      => array('jquery', 'pcafe_maps_api_js'),
                'enqueue'  => $frontend_enqueue_condition,
                'in_footer' => true
            )
        );

        return array_merge(parent::scripts(), $scripts);
    }

    /**
     * Returns the Google Places API key.
     *
     * @return string
     */
    public function get_google_places_api_key() {
        $old_key = get_option('pc_gf_google_api_key');
        $api_key = $this->get_plugin_setting('pcafe_google_api_key_v1');
        if (empty($api_key) && !empty($old_key)) {
            $api_key = $old_key;
        }

        return $api_key;
    }

    /**
     * Checks if the form has fields that require the frontend script.
     *
     * @param array $form The form object.
     * @return bool
     */
    public function frontend_script_callback($form) {
        $fields_data = [];

        foreach ($form['fields'] as $field) {
            if (property_exists($field, 'autocompleteGField') && $field->autocompleteGField && $field->type === 'address') {
                $fields_data[] = [
                    'id' => $field->id
                ];
            }
            if (property_exists($field, 'textAutocompleteGField') && $field->textAutocompleteGField && $field->type === 'text') {
                $fields_data[] = [
                    'id' => $field->id
                ];
            }
        }

        if (count($fields_data) === 0) {
            return false;
        }

        return true;
    }


    /**
     * Adds the init script for the Address Auto Complete functionality.
     *
     * @param array $form The form object.
     */
    public function add_init_script($form) {
        $aac_fields = $this->get_aac_fields($form);

        if (empty($aac_fields)) {
            return $form;
        }

        require_once(GFCommon::get_base_path() . '/form_display.php');

        foreach ($aac_fields as $field) {
            $form_id = $field['formId'];
            $id      = $field['id'];

            $config = [
                'formId' => $form_id,
                'fieldId' => $id,
                'input' => 'input_' . $form_id . '_' . $id,
                'type'  => $field['type'],
                'is_async' => $this->get_plugin_setting('pcafe_load_with_async'),
            ];

            if ($field['type'] === 'text') {
                $config['restrict_countries']   = $field['restrictCountryTextGField'] ?? false;
            }

            if ($field['type'] === 'address') {
                $config['single_line']          = $field['singleAutofillGField'] ?? false;
                $config['restrict_countries']   = $field['restrictCountryGField'] ?? false;
            }

            $slug   = 'aac_input_' . $form_id . '_' . $id;
            $script = 'window.' . $slug . ' = new PCAFE_AAC_Frontend( ' . json_encode($config) . ' );';

            GFFormDisplay::add_init_script($form_id, $slug, GFFormDisplay::ON_PAGE_RENDER, $script);
        }
    }

    public function pcafe_aac_fields_settings_tab($tabs, $form) {
        $tabs[] = array(
            // Define the unique ID for your tab.
            'id'             => 'address_auto_complete',
            // Define the title to be displayed on the toggle button your tab.
            'title'          => 'Address Auto Complete',
            // Define an array of classes to be added to the toggle button for your tab.
            'toggle_classes' => array('aac_toggle_1', 'aac_toggle_2'),
            // Define an array of classes to be added to the body of your tab.
            'body_classes'   => array('aac_toggle_class'),
        );

        return $tabs;
    }

    public function pcafe_aac_fields_settings_tab_content($form, $field) {
?>
        <li class="gfautocomplete_setting field_setting">
            <ul>
                <li>
                    <input type="checkbox" id="pc_field_autocomplete_value" onclick="SetFieldProperty('autocompleteGField', this.checked);" />
                    <label for="pc_field_autocomplete_value" class="inline">
                        <?php esc_html_e("Enable Autocomplete with Google Places API", "gf-google-address-autocomplete"); ?>
                        <?php gform_tooltip("pc_autocomplete_tooltip"); ?>
                    </label>
                </li>
            </ul>
        </li>
        <li class="gfautosingle_setting field_setting">
            <ul>
                <li>
                    <input type="checkbox" id="pc_singe_field_autocomplete_value" onclick="SetFieldProperty('singleAutofillGField', this.checked);" />
                    <label for="pc_singe_field_autocomplete_value" class="inline">
                        <?php esc_html_e("Use Single Line autofill ", "gf-google-address-autocomplete"); ?>
                        <?php gform_tooltip("pc_single_autocomplete_tooltip"); ?>
                    </label>
                </li>
                <li class="gfrestrict_setting field_setting">
                    <label for="field_admin_label" class="section_label">
                        <?php esc_html_e("Restrict country", "gf-google-address-autocomplete"); ?>
                        <?php gform_tooltip("restrict_tooltips"); ?>
                    </label>
                    <select name="pc_restrict_country_value" id="pc_restrict_country_value" onChange="SetFieldProperty('restrictCountryGField', this.value);">
                        <option value="">Please select</option>
                        <?php
                        foreach ($this->get_countries() as $value => $name) {
                            echo '<option value="' . esc_attr($value) . '">' . esc_attr($name) . '</option>';
                        }
                        ?>
                    </select>
                </li>
            </ul>
        </li>

        <li class="gftextautocomplete_setting field_setting">
            <ul>
                <li>
                    <input type="checkbox" id="pc_text_field_autocomplete_value" onclick="SetFieldProperty('textAutocompleteGField', this.checked);" />
                    <label for="pc_text_field_autocomplete_value" class="inline">
                        <?php esc_html_e("Enable Autocomplete with Google Places API", "gf-google-address-autocomplete"); ?>
                        <?php gform_tooltip("pc_autocomplete_tooltip"); ?>
                    </label>
                </li>
                <li class="gf_country_restrict_setting field_setting">
                    <label for="field_admin_label" class="section_label">
                        <?php esc_html_e("Restrict country", "gf-google-address-autocomplete"); ?>
                        <?php gform_tooltip("restrict_tooltips"); ?>
                    </label>
                    <select name="pc_restrict_country_text_value" id="pc_restrict_country_text_value" onChange="SetFieldProperty('restrictCountryTextGField', this.value);">
                        <option value="">Please select</option>
                        <?php
                        foreach ($this->get_countries() as $value => $name) {
                            echo '<option value="' . esc_attr($value) . '">' . esc_attr($name) . '</option>';
                        }
                        ?>
                    </select>
                </li>
            </ul>
        </li>
    <?php
    }

    public function editor_script() {
    ?>
        <script type='text/javascript'>
            //adding setting to fields of type "address"
            fieldSettings.address += ", .gfautocomplete_setting";
            fieldSettings.address += ", .gfautosingle_setting";
            fieldSettings.address += ", .gfrestrict_setting";
            fieldSettings.text += ", .gftextautocomplete_setting";
            fieldSettings.text += ", .gf_country_restrict_setting";

            //binding to the load field settings event to initialize the checkbox
            jQuery(document).bind("gform_load_field_settings", function(event, field, form) {
                jQuery("#pc_field_autocomplete_value").prop('checked', Boolean(rgar(field, 'autocompleteGField')));
                jQuery("#pc_singe_field_autocomplete_value").prop('checked', Boolean(rgar(field, 'singleAutofillGField')));
                jQuery("#pc_restrict_country_value").val(field["restrictCountryGField"]);
                jQuery("#pc_restrict_country_text_value").val(field["restrictCountryTextGField"]);
                jQuery("#pc_text_field_autocomplete_value").prop('checked', Boolean(rgar(field, 'textAutocompleteGField')));
            });
        </script>
    <?php
    }

    public function api_key_menu_item($menu_items) {
        $menu_items[] = array(
            "name"          => "pc_gf_api_key_settings",
            "label"         => "Autocomplete API Settings",
            "callback"      => [$this, "pcafe_gf_api_key_set_fields"],
            "permission"    => "manage_options"
        );
        return $menu_items;
    }

    public function pcafe_gf_api_key_set_fields() {
    ?>
        <div class="wrap">
            <h3><?php esc_html_e("Gravity Forms Address Autocomplete Settings", "gf-google-address-autocomplete"); ?></h3>
            <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=gf_settings&subview=gf-google-address-autocomplete')); ?>" class="button button-large button-primary"><?php esc_html_e("Go To Setting Page", "gf-google-address-autocomplete"); ?></a>
        </div>
<?php
    }

    public function plugin_settings_fields() {
        $api_key    =    get_option('pc_gf_google_api_key') ? get_option('pc_gf_google_api_key') : '';
        $is_async   =    get_option('pcafe_load_with_async') ? true : false;

        return array(
            array(
                'title'  => esc_html__('Address Auto Complete Settings', 'gf-google-address-autocomplete'),
                'fields' => array(
                    array(
                        'name'          => 'pcafe_google_api_key_v1',
                        'label'         => esc_html__('Google Places API Key', 'gf-google-address-autocomplete'),
                        'type'          => 'text',
                        'class'         => 'medium',
                        'default_value' => $api_key
                    ),
                    array(
                        'name'          => 'pcafe_load_with_async',
                        'label'         => esc_html__('Load with Async', 'gf-google-address-autocomplete'),
                        'type'          => 'toggle',
                        'default_value' => $is_async
                    )
                ),
            )
        );
    }

    public function add_tooltips($tooltips) {
        $tooltips['pc_autocomplete_tooltip'] = "<h6>" . esc_html__("Enable google auto suggestion", "gf-google-address-autocomplete") . "</h6>" . esc_html__("Check this box to show google address auto complete suggestion", "gf-google-address-autocomplete") . "";
        $tooltips['pc_single_autocomplete_tooltip'] = esc_html__("Check this box for use single field autocomplete", "gf-google-address-autocomplete");
        $tooltips['restrict_tooltips'] = esc_html__("Choose country for adding restriction.", "gf-google-address-autocomplete");
        return $tooltips;
    }

    public function get_countries() {
        return [
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AC' => 'Ascension Island',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'VG' => 'British Virgin Islands',
            'BN' => 'Brunei',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'IC' => 'Canary Islands',
            'CV' => 'Cape Verde',
            'BQ' => 'Caribbean Netherlands',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'EA' => 'Ceuta and Melilla',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CP' => 'Clipperton Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CD' => 'Congo (DRC)',
            'CG' => 'Congo (Republic)',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote dIvoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CW' => 'Curacao',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DG' => 'Diego Garcia',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard & McDonald Islands',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'XK' => 'Kosovo',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Laos',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macau',
            'MK' => 'Macedonia (FYROM)',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar (Burma)',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'KP' => 'North Korea',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestine',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn Islands',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre and Miquelon',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SX' => 'Sint Maarten',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia & South Sandwich Islands',
            'KR' => 'South Korea',
            'SS' => 'South Sudan',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'VC' => 'St. Vincent & Grenadines',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syria',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TA' => 'Tristan da Cunha',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UM' => 'U.S. Outlying Islands',
            'VI' => 'U.S. Virgin Islands',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VA' => 'Vatican City',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe'
        ];
    }


    public function get_aac_fields($form) {
        if (empty($form['fields'])) {
            return array();
        }

        $fields = array();

        foreach ($form['fields'] as $field) {
            if ($this->is_aac_field($field)) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function is_aac_field($field) {
        return (rgar($field, 'type') === 'address' && rgar($field, 'autocompleteGField') || rgar($field, 'type') === 'text' && rgar($field, 'textAutocompleteGField'));
    }
}
