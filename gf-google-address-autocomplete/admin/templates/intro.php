<?php
defined('ABSPATH') || exit;

$right_content = [
    'demo'      => [
        'title' => __('Check Demos', 'gf-google-address-autocomplete'),
        'desc'  => __('Explore live demos to see how Address AutoComplete Pro can enhance your WordPress experience.', 'gf-google-address-autocomplete'),
        'links' => 'https://demo.pluginscafe.com/address-autocomplete-for-gravity-forms-pro/',
        'button_text' => __('View Demos', 'gf-google-address-autocomplete'),
        'icon'  => 'demos.svg'
    ],
    'docs'      => [
        'title' => __('View Knowledgebase', 'gf-google-address-autocomplete'),
        'desc'  => __('Check our documentation to understand how Address Autocomplete Pro works.', 'gf-google-address-autocomplete'),
        'links' => 'https://pluginscafe.com/docs/address-autocomplete-for-gravity-forms-pro/',
        'button_text' => __('View Docs', 'gf-google-address-autocomplete'),
        'icon'  => 'documents.svg'
    ],
    'helps'   => [
        'title' => __('Need Helps', 'gf-google-address-autocomplete'),
        'desc'  => __('Get in touch with our dedicated support team whenever you face an issue.', 'gf-google-address-autocomplete'),
        'links' => 'https://wordpress.org/support/plugin/gf-google-address-autocomplete/',
        'button_text' => __('Contact Us', 'gf-google-address-autocomplete'),
        'icon'  => 'support.svg'
    ],
    'review'   => [
        'title' => __('Show Your Love', 'gf-google-address-autocomplete'),
        'desc'  => __('Show your love for Address AutoComplete Pro by rating us and helping us grow more.', 'gf-google-address-autocomplete'),
        'links' => 'https://wordpress.org/support/plugin/gf-google-address-autocomplete/reviews/#new-post',
        'button_text' => __('Write A Review', 'gf-google-address-autocomplete'),
        'icon'  => 'review.svg'
    ]
];


$features = [
    'fields' => [
        'title' => __('Advanced Fields', 'gf-google-address-autocomplete'),
        'desc' => __('Address AutoComplete Pro includes powerful fields to retrieve addresses and detailed location information. It is very easy to configure and provides accurate results. With a single address field, users can start typing and address suggestions will appear instantly. Using the Google API fields, you can connect multiple fields together to automatically capture and organize different parts of the address and other related data.', 'gf-google-address-autocomplete'),
        'icon' => 'gfgaa_fields.png'
    ],
    'maps' => [
        'title' => __('Maps', 'gf-google-address-autocomplete'),
        'desc' => __('With the Map field, you can connect multiple Google API fields to display the selected location on the map, which is linked to the address field. You can also set a default location that appears when the form loads. Additionally, it offers several useful options such as map type, zoom level, height, width, draggable marker, and more.', 'gf-google-address-autocomplete'),
        'icon' => 'maps.png'
    ],
    'distance' => [
        'title' => __('Distance Calculatioin', 'gf-google-address-autocomplete'),
        'desc' => __('Distance calculation is very easy with Address Autocomplete Pro. Simply use the Direction fields to calculate the distance between addresses. Add the Google API fields that are connected to your address fields into the Direction fields, and it will automatically display the distance and travel duration. You can also easily connect it with the Map field for a better visual experience.', 'gf-google-address-autocomplete'),
        'icon' => 'distance.png'
    ],
    'place_api' => [
        'title' => __('Place API (new) - (Beta)', 'gf-google-address-autocomplete'),
        'desc' => __('Address Autocomplete Pro now supports the new Google Places API. With this feature, you will receive address suggestions powered by the latest Places API. Simply enable the New Places API toggle in the Gravity Forms settings, and it will automatically activate for all address fields in Gravity Forms. There is a option to disable on specific form also.', 'gf-google-address-autocomplete'),
        'icon' => 'new_place_api.png'
    ],
    'place_details' => [
        'title' => __('Place Details', 'gf-google-address-autocomplete'),
        'desc' => __('Address Autocomplete Pro includes an excellent feature for retrieving detailed information about a selected address. It can capture data such as street name, street number, county, country code, state, latitude, longitude, and more. This can be configured using a single line field connected to the Google API field, and the setup process is very simple. When an address is selected or changed, the corresponding place details are automatically and dynamically populated based on your configuration.', 'gf-google-address-autocomplete'),
        'icon' => 'place_details.png'
    ],
    'place_types' => [
        'title' => __('Place Types', 'gf-google-address-autocomplete'),
        'desc' => __('Place types are very important for showing address suggestions based on your specific requirements. Address Autocomplete includes several place/result types by default, but you can easily add more place types as needed. In the Google Place Types documentation, you can find the complete list of place types offered by Google. Using a filter hook, you can easily add any required place type to customize the suggestions.', 'gf-google-address-autocomplete'),
        'icon' => 'place_type.png'
    ],
    'restriction_type' => [
        'title' => __('Address Restriction Type', 'gf-google-address-autocomplete'),
        'desc' => __('Address Autocomplete Pro includes a powerful feature that allows you to restrict the area for address suggestions. In the address field, you can limit suggestions using three different options. With country restriction, you can allow address suggestions from multiple selected countries. Using proximity restriction, you can define a location with latitude, longitude, and radius to prioritize nearby results. And with area bounds, you can set a specific region using Southwest and Northeast coordinates.', 'gf-google-address-autocomplete'),
        'icon' => 'restrictions.png'
    ],
    'location_button' => [
        'title' => __('Location Button', 'gf-google-address-autocomplete'),
        'desc' => __('In the address field, there is a map marker icon. By clicking this icon, users can easily get their current location. To display this button, the admin needs to enable the location button from the field settings.', 'gf-google-address-autocomplete'),
        'icon' => 'location_button.png'
    ],
];

?>

<div class="tab-content" id="intro" style="display: none;">
    <div class="gfgaa_dashboard_wrap">
        <div class="gfgaa_dashboard_left">
            <div class="gfgaa_dashboard_content_wrap">
                <div class="dashboard_content">
                    <h2><span class="gradient_text">Address AutoComplete For Gravity Forms Pro</span></h2>
                    <p><?php esc_html_e('Speed up form submissions and eliminate address errors with Google Places API powered address autocomplete, fully integrated with Gravity Forms.', 'gf-google-address-autocomplete'); ?></p>
                    <a target="_blank" href="https://demo.pluginscafe.com/address-autocomplete-for-gravity-forms-pro/" class="gfgaa_dashboard_btn"><?php esc_html_e('Check Demo Now', 'gf-google-address-autocomplete'); ?></a>
                </div>
                <img src="<?php echo esc_url(GF_AUTO_ADDRESS_COMPLETE_URL . '/admin/images/banner.svg'); ?>" alt="">
            </div>

            <?php foreach ($features as $key => $feature) : ?>
                <div class="single_feature_box">
                    <div class="feature_content">
                        <h3><?php echo esc_html($feature['title']); ?></h3>
                        <p><?php echo esc_html($feature['desc']); ?></p>
                    </div>
                    <img src="<?php echo esc_url(GF_AUTO_ADDRESS_COMPLETE_URL . '/admin/images/' . esc_attr($feature['icon'])); ?>" />
                </div>
            <?php endforeach; ?>
        </div>
        <div class="gfgaa_dashboard_right">
            <?php foreach ($right_content as $key => $value) : ?>
                <div class="single_info_box">
                    <h3>
                        <img src="<?php echo esc_url(GF_AUTO_ADDRESS_COMPLETE_URL . 'admin/images/' . esc_attr($value['icon'])); ?>" />
                        <?php echo esc_html($value['title']); ?>
                    </h3>
                    <p><?php echo esc_html($value['desc']); ?></p>
                    <a href="<?php echo esc_url($value['links']); ?>" class="gfgaa_info_btn" target="_blank"><?php echo esc_html($value['button_text']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>