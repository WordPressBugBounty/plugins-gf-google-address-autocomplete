<?php
if (! defined('ABSPATH')) exit;

$features = [
    [
        'feature'   => __('Place API', 'gf-google-address-autocomplete'),
        'pro'       => 0
    ],
    [
        'feature'   => __('Single & Address Field Support', 'gf-google-address-autocomplete'),
        'pro'       => 0
    ],
    [
        'feature'   => __('Country Restriction', 'gf-google-address-autocomplete'),
        'pro'       => 0
    ],
    [
        'feature'   => __('Place API (New)', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('Map Field', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('Direction Field', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('Distance Calculation', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('Google API Field', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('Place Details', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('3 Type Restriction', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('Place type (School, Restaurant, cities, addresses and more)', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
    [
        'feature'   => __('Location Button)', 'gf-google-address-autocomplete'),
        'pro'       => true
    ],
];

?>
<div class="tab-content" id="features" style="display: none;">
    <div class="gfgaa_dashboard_wrap">
        <div class="gfgaa_dashboard_left">
            <div class="content_heading">
                <h2><?php esc_html_e('Unlock the full power of Address AutoComplete', 'gf-google-address-autocomplete'); ?></h2>
                <p><?php esc_html_e('The amazing PRO features will make your Address AutoComplete even more efficient.', 'gf-google-address-autocomplete'); ?></p>
            </div>

            <div class="content_heading free_vs_pro">
                <h2>
                    <span><?php esc_html_e('Free', 'gf-google-address-autocomplete'); ?></span>
                    <?php esc_html_e('vs', 'gf-google-address-autocomplete'); ?>
                    <span><?php esc_html_e('Pro', 'gf-google-address-autocomplete'); ?></span>
                </h2>
            </div>

            <div class="features_list">
                <div class="list_header">
                    <div class="feature_title"><?php esc_html_e('Feature List', 'gf-google-address-autocomplete'); ?></div>
                    <div class="feature_free"><?php esc_html_e('Free', 'gf-google-address-autocomplete'); ?></div>
                    <div class="feature_pro"><?php esc_html_e('Pro', 'gf-google-address-autocomplete'); ?></div>
                </div>
                <?php foreach ($features as $feature) : ?>
                    <div class="feature">
                        <div class="feature_title"><?php echo esc_html($feature['feature']); ?></div>
                        <div class="feature_free">
                            <?php if ($feature['pro']) : ?>
                                <i class="dashicons dashicons-no-alt"></i>
                            <?php else : ?>
                                <i class="dashicons dashicons-saved"></i>
                            <?php endif; ?>
                        </div>
                        <div class="feature_pro">
                            <i class="dashicons dashicons-saved"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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