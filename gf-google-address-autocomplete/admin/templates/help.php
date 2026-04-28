<?php
if (!defined('ABSPATH')) {
    exit;
}

$gfgaa_help_items = [
    'documentation' => [
        'name'      => __('Documentation', 'gf-google-address-autocomplete'),
        'desc'      => __('Check out our detailed online documentation and video tutorials to find out more about what you can do.', 'gf-google-address-autocomplete'),
        'icon'      => 'documentation.svg',
        'path'      => '',
        'url'       => 'https://pluginscafe.com/docs/address-autocomplete-for-gravity-forms-pro/',
        'btn_text'  => __('Documentation', 'gf-google-address-autocomplete')
    ],
    'support' => [
        'name'      => __('Support', 'gf-google-address-autocomplete'),
        'desc'      => __('We have dedicated support team to provide you fast, friendly & top-notch customer support.', 'gf-google-address-autocomplete'),
        'icon'      => 'supports.svg',
        'path'      => '',
        'url'       => 'https://wordpress.org/support/plugin/gf-google-address-autocomplete/',
        'btn_text'  => __('Support', 'gf-google-address-autocomplete')
    ],
    'pro_support' => [
        'name'      => __('Need Help Tweaking Your WordPress Site?', 'gf-google-address-autocomplete'),
        'desc'      => __('Want to make small changes, add features, or need customization? Our team can do it for you — just $29/hour, no hassle.', 'gf-google-address-autocomplete'),
        'icon'      => 'pro_support.svg',
        'path'      => '',
        'url'       => 'https://wa.me/+8801812438663',
        'btn_text'  => __('Get Free Quote', 'gf-google-address-autocomplete')
    ]
];

?>

<div class="tab-content" id="help" style="display: none;">
    <div class="gfgaa_dashboard_wrap">
        <div class="gfgaa_dashboard_left gfgaa_help_items">
            <?php
            foreach ($gfgaa_help_items as $key => $item) :
                $img_url = $item['path'] ? $item['path'] . $item['icon'] : GF_AUTO_ADDRESS_COMPLETE_URL . 'admin/images/' . $item['icon'];
            ?>
                <div class="helps_box">
                    <div class="help_img">
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                    </div>
                    <div class="helps_content">
                        <h3><?php echo esc_html($item['name']); ?></h3>
                        <p><?php echo esc_html($item['desc']); ?></p>
                        <div class="buttons">
                            <?php if (!empty($item['url'])): ?>
                                <a href="<?php echo esc_url($item['url']); ?>" class="gfgaa_button_help" target="_blank">
                                    <?php echo esc_html($item['btn_text']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
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